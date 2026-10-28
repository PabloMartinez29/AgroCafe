<?php

namespace App\Services;

use App\Models\ConsentRecord;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Registra y consulta las autorizaciones de los titulares (Ley 1581 de 2012).
 */
class ConsentService
{
    /**
     * Documentos que el titular debe aceptar de forma expresa.
     *
     * @return list<string>
     */
    public function requiredDocuments(): array
    {
        return collect(config('legal.documents'))
            ->filter(fn (array $document): bool => $document['consent'] ?? false)
            ->keys()
            ->all();
    }

    public function version(string $document): string
    {
        return (string) config("legal.documents.{$document}.version");
    }

    /**
     * Texto exacto de la casilla. Cuando la marca un administrador en nombre
     * del titular se usa la variante "assisted", para que la evidencia diga
     * quién afirmó qué.
     */
    public function checkboxText(string $document, bool $assisted = false): string
    {
        $key = $assisted ? 'checkbox_assisted' : 'checkbox';

        return (string) config("legal.documents.{$document}.{$key}");
    }

    /**
     * Huella SHA-256 del texto del documento vigente. Se normalizan los saltos
     * de línea para que sea la misma en Windows y en Linux.
     */
    public function documentHash(string $document): string
    {
        $path = view()->getFinder()->find((string) config("legal.documents.{$document}.view"));

        return hash('sha256', str_replace("\r\n", "\n", (string) file_get_contents($path)));
    }

    /**
     * @return array<string, list<string>>
     */
    public function validationRules(): array
    {
        return [
            'accept_terms' => ['accepted'],
            'accept_privacy' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationMessages(): array
    {
        return [
            'accept_terms.accepted' => 'Debes aceptar los Términos y Condiciones para continuar.',
            'accept_privacy.accepted' => 'Debes autorizar el tratamiento de tus datos con la Política de Privacidad para continuar.',
        ];
    }

    /**
     * Guarda una autorización por cada documento, encadenada a la anterior.
     *
     * @param  list<string>|null  $documents
     * @return Collection<int, ConsentRecord>
     */
    public function record(
        Model $subject,
        Request $request,
        string $channel,
        ?User $capturedBy = null,
        ?array $documents = null,
        string $action = ConsentRecord::ACTION_GRANTED,
    ): Collection {
        $documents ??= $this->requiredDocuments();

        return Cache::lock('consent-chain', 10)->block(5, function () use ($subject, $request, $channel, $capturedBy, $documents, $action): Collection {
            return (new ConsentRecord)->getConnection()->transaction(function () use ($subject, $request, $channel, $capturedBy, $documents, $action): Collection {
                $previous = ConsentRecord::query()->latest('id')->first();
                $records = new Collection;

                foreach ($documents as $document) {
                    $record = new ConsentRecord([
                        'subject_type' => $subject->getMorphClass(),
                        'subject_id' => $subject->getKey(),
                        'subject_name' => $this->describe($subject),
                        'subject_email' => $subject->email ?? null,
                        'document' => $document,
                        'document_version' => $this->version($document),
                        'document_hash' => $this->documentHash($document),
                        'action' => $action,
                        'channel' => $channel,
                        'consent_text' => $action === ConsentRecord::ACTION_REVOKED
                            ? 'Revocación de la autorización otorgada (eliminación de la cuenta por el titular).'
                            : $this->checkboxText($document, $channel === ConsentRecord::CHANNEL_ADMIN_ASSISTED),
                        'ip_address' => $request->ip(),
                        'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
                        'captured_by_id' => $capturedBy?->getKey(),
                        'captured_by_name' => $capturedBy?->name,
                        'previous_hash' => $previous?->record_hash,
                        'created_at' => now()->startOfSecond(),
                    ]);
                    $record->record_hash = $record->computeHash();
                    $record->save();

                    $records->push($record);
                    $previous = $record;
                }

                return $records;
            });
        });
    }

    /**
     * Anota la revocación de todas las autorizaciones del titular.
     *
     * @return Collection<int, ConsentRecord>
     */
    public function revokeAll(Model $subject, Request $request, string $channel = ConsentRecord::CHANNEL_ACCOUNT_DELETED): Collection
    {
        return $this->record($subject, $request, $channel, null, null, ConsentRecord::ACTION_REVOKED);
    }

    /**
     * Indica si el propio titular aceptó la versión vigente del documento.
     */
    public function hasAccepted(Model $subject, string $document): bool
    {
        $latest = ConsentRecord::query()
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey())
            ->where('document', $document)
            ->where('channel', '!=', ConsentRecord::CHANNEL_ADMIN_ASSISTED)
            ->latest('id')
            ->first();

        return $latest !== null
            && $latest->isGranted()
            && $latest->document_version === $this->version($document)
            && in_array($latest->channel, ConsentRecord::TITULAR_CHANNELS, true);
    }

    /**
     * Documentos vigentes que el titular todavía no ha aceptado.
     *
     * @return list<string>
     */
    public function pendingDocuments(Model $subject): array
    {
        return array_values(array_filter(
            $this->requiredDocuments(),
            fn (string $document): bool => ! $this->hasAccepted($subject, $document),
        ));
    }

    public function hasAcceptedAll(Model $subject): bool
    {
        return $this->pendingDocuments($subject) === [];
    }

    /**
     * Recalcula toda la cadena de huellas para detectar alteraciones.
     *
     * @return array{valid: bool, checked: int, broken_id: int|null, reason: string|null}
     */
    public function verifyChain(): array
    {
        $checked = 0;
        $previousHash = null;

        foreach (ConsentRecord::query()->orderBy('id')->lazyById(500) as $record) {
            $checked++;

            if ($record->previous_hash !== $previousHash) {
                return ['valid' => false, 'checked' => $checked, 'broken_id' => $record->id, 'reason' => 'La huella anterior no coincide con el registro previo (posible borrado o inserción).'];
            }

            if ($record->computeHash() !== $record->record_hash) {
                return ['valid' => false, 'checked' => $checked, 'broken_id' => $record->id, 'reason' => 'El contenido del registro fue modificado.'];
            }

            $previousHash = $record->record_hash;
        }

        return ['valid' => true, 'checked' => $checked, 'broken_id' => null, 'reason' => null];
    }

    private function describe(Model $subject): string
    {
        if ($subject instanceof Cooperative) {
            return trim("{$subject->name} (NIT {$subject->nit})");
        }

        return (string) ($subject->name ?? class_basename($subject).' #'.$subject->getKey());
    }
}
