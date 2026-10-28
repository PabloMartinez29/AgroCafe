<?php

namespace App\Http\Controllers;

use App\Models\ConsentRecord;
use App\Services\ConsentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public function __construct(private ConsentService $consents) {}

    /**
     * Pantalla para aceptar los documentos vigentes (cuentas antiguas, creadas
     * por un administrador o con documentos actualizados).
     */
    public function show(Request $request): View|RedirectResponse
    {
        if ($this->consents->hasAcceptedAll($request->user())) {
            return redirect()->route('dashboard');
        }

        return view('consent.show');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            $this->consents->validationRules(),
            $this->consents->validationMessages(),
        );

        $this->consents->record(
            $request->user(),
            $request,
            ConsentRecord::CHANNEL_WEB_ACCEPT,
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Aceptación desde la app móvil para cuentas que aún no tienen registro.
     */
    public function storeApi(Request $request): JsonResponse
    {
        $request->validate(
            $this->consents->validationRules(),
            $this->consents->validationMessages(),
        );

        $this->consents->record(
            $request->user(),
            $request,
            ConsentRecord::CHANNEL_API_ACCEPT,
        );

        return response()->json(['message' => 'Autorización registrada.']);
    }
}
