<?php

namespace App\Console\Commands;

use App\Models\ConsentRecord;
use App\Services\ConsentService;
use Illuminate\Console\Command;

class ShowConsentEvidence extends Command
{
    /**
     * @var string
     */
    protected $signature = 'consents:evidence {email : Correo del titular} {--json : Imprime la evidencia completa en JSON}';

    /**
     * @var string
     */
    protected $description = 'Muestra la evidencia de autorización de un titular (para una reclamación o requerimiento de la SIC)';

    public function handle(ConsentService $consents): int
    {
        $email = mb_strtolower((string) $this->argument('email'));

        $records = ConsentRecord::query()
            ->whereRaw('LOWER(subject_email) = ?', [$email])
            ->orderBy('id')
            ->get();

        if ($records->isEmpty()) {
            $this->error("No existe ningún registro de autorización para {$email}. Sin registro, la autorización no puede probarse.");

            return self::FAILURE;
        }

        if ($this->option('json')) {
            $this->line($records->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(
                ['#', 'Fecha', 'Documento', 'Versión', 'Acción', 'Canal', 'IP', 'Capturó'],
                $records->map(fn (ConsentRecord $record): array => [
                    $record->id,
                    $record->created_at->format('Y-m-d H:i:s'),
                    $record->document,
                    $record->document_version,
                    $record->action,
                    $record->channel,
                    $record->ip_address,
                    $record->captured_by_name ?? 'El propio titular',
                ])->all(),
            );
        }

        $chain = $consents->verifyChain();
        $this->line($chain['valid']
            ? "Integridad de la cadena: OK ({$chain['checked']} registros)."
            : "Integridad de la cadena: FALLA en el registro #{$chain['broken_id']} — {$chain['reason']}");

        return $chain['valid'] ? self::SUCCESS : self::FAILURE;
    }
}
