<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * Evidencia de una autorización otorgada (o revocada) por un titular.
 *
 * Es un registro de solo escritura: no se actualiza ni se elimina. Cada fila
 * incluye el hash de la fila anterior, de modo que cualquier alteración
 * posterior rompe la cadena y se detecta con `php artisan consents:verify`.
 *
 * @property int $id
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string $subject_name
 * @property string|null $subject_email
 * @property string $document
 * @property string $document_version
 * @property string $document_hash
 * @property string $action
 * @property string $channel
 * @property string $consent_text
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property int|null $captured_by_id
 * @property string|null $captured_by_name
 * @property string|null $previous_hash
 * @property string $record_hash
 * @property \Illuminate\Support\Carbon $created_at
 */
class ConsentRecord extends Model
{
    /** @use HasFactory<\Database\Factories\ConsentRecordFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    public const ACTION_GRANTED = 'granted';

    public const ACTION_REVOKED = 'revoked';

    public const CHANNEL_WEB_REGISTER = 'web_register';

    public const CHANNEL_API_REGISTER = 'api_register';

    public const CHANNEL_WEB_ACCEPT = 'web_accept';

    public const CHANNEL_API_ACCEPT = 'api_accept';

    public const CHANNEL_ADMIN_ASSISTED = 'admin_assisted';

    public const CHANNEL_ACCOUNT_DELETED = 'account_deleted';

    /**
     * Canales en los que el propio titular marcó la casilla. Una casilla
     * marcada por un administrador queda registrada, pero no reemplaza la
     * autorización del titular.
     *
     * @var list<string>
     */
    public const TITULAR_CHANNELS = [
        self::CHANNEL_WEB_REGISTER,
        self::CHANNEL_API_REGISTER,
        self::CHANNEL_WEB_ACCEPT,
        self::CHANNEL_API_ACCEPT,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'subject_type',
        'subject_id',
        'subject_name',
        'subject_email',
        'document',
        'document_version',
        'document_hash',
        'action',
        'channel',
        'consent_text',
        'ip_address',
        'user_agent',
        'captured_by_id',
        'captured_by_name',
        'previous_hash',
        'record_hash',
        'created_at',
    ];

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Los registros de autorización son inmutables y no se pueden modificar.');
        });

        static::deleting(function (): never {
            throw new LogicException('Los registros de autorización son inmutables y no se pueden eliminar.');
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'subject_id' => 'integer',
            'captured_by_id' => 'integer',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function isGranted(): bool
    {
        return $this->action === self::ACTION_GRANTED;
    }

    /**
     * Calcula la huella SHA-256 del registro (incluye la huella anterior).
     */
    public function computeHash(): string
    {
        $payload = [
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id === null ? null : (int) $this->subject_id,
            'subject_name' => $this->subject_name,
            'subject_email' => $this->subject_email,
            'document' => $this->document,
            'document_version' => $this->document_version,
            'document_hash' => $this->document_hash,
            'action' => $this->action,
            'channel' => $this->channel,
            'consent_text' => $this->consent_text,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'captured_by_id' => $this->captured_by_id === null ? null : (int) $this->captured_by_id,
            'captured_by_name' => $this->captured_by_name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'previous_hash' => $this->previous_hash,
        ];

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }
}
