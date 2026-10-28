<?php

namespace App\Http\Middleware;

use App\Services\ConsentService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsentsAccepted
{
    public function __construct(private ConsentService $consents) {}

    /**
     * Exige que el usuario haya aceptado la versión vigente de los Términos y
     * de la Política de Privacidad. Cubre cuentas antiguas, cuentas creadas
     * por un administrador y cambios de versión.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $pending = $this->consents->pendingDocuments($user);

        if ($pending === []) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Debes aceptar los Términos y Condiciones y la Política de Privacidad vigentes.',
                'consents_pending' => $pending,
            ], 403);
        }

        return redirect()->route('consent.show');
    }
}
