<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CooperativeController extends Controller
{
    public function index(Request $request)
    {
        $query = Cooperative::query();

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $cooperatives = $query->orderBy('name')->get();

        return response()->json($cooperatives);
    }

    public function show(Cooperative $cooperative)
    {
        return response()->json($cooperative);
    }

    /**
     * Crear una nueva cooperativa (solo administradores)
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'nit' => 'required|string|max:20|unique:cooperatives',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'legal_representative' => 'nullable|string|max:100',
            'active' => 'boolean',
        ]);

        $cooperative = Cooperative::create($validated);

        return response()->json($cooperative, 201);
    }

    /**
     * Actualizar una cooperativa (solo administradores)
     */
    public function update(Request $request, Cooperative $cooperative)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'nit' => ['required', 'string', 'max:20', 'unique:cooperatives,nit,' . $cooperative->id],
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'legal_representative' => 'nullable|string|max:100',
            'active' => 'boolean',
        ]);

        $cooperative->update($validated);

        return response()->json($cooperative);
    }

    /**
     * Eliminar una cooperativa (solo administradores)
     */
    public function destroy(Cooperative $cooperative)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar si tiene registros relacionados
        if ($cooperative->sales()->count() > 0) {
            // Desactivar en lugar de eliminar
            $cooperative->update(['active' => false]);
            return response()->json([
                'message' => 'Cooperativa desactivada (tiene ventas relacionadas)',
                'cooperative' => $cooperative
            ]);
        }

        $cooperative->delete();

        return response()->json(['message' => 'Cooperativa eliminada exitosamente']);
    }
}

