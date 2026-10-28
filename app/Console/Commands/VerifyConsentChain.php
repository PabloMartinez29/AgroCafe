<?php

namespace App\Console\Commands;

use App\Models\ConsentRecord;
use App\Services\ConsentService;
use Illuminate\Console\Command;

class VerifyConsentChain extends Command
{
    /**
     * @var string
     */
    protected $signature = 'consents:verify';

    /**
     * @var string
     */
    protected $description = 'Verifica que el registro de autorizaciones no haya sido alterado';

    public function handle(ConsentService $consents): int
    {
        $result = $consents->verifyChain();

        if (! $result['valid']) {
            $this->error("Cadena rota en el registro #{$result['broken_id']}: {$result['reason']}");

            return self::FAILURE;
        }

        $this->info("Cadena íntegra: {$result['checked']} registros verificados.");

        $last = ConsentRecord::query()->latest('id')->first();

        if ($last !== null) {
            $this->line("Último registro #{$last->id} — huella: {$last->record_hash}");
            $this->line('Guarda esta huella fuera del servidor para detectar si borran los últimos registros.');
        }

        return self::SUCCESS;
    }
}
