<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeType;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoffeeTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = CoffeeType::query();

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->filled('variety')) {
            $query->where('variety', $request->variety);
        }

        if ($request->filled('quality')) {
            $query->where('quality', $request->quality);
        }

        $coffee_types = $query->with(['purchases', 'sales', 'inventoryMovements'])->orderBy('name')->get();
        
        // Calcular inventario disponible para cada tipo de café
        $coffee_types_with_inventory = $coffee_types->map(function ($coffee_type) {
            $purchased = $coffee_type->purchases()
                ->where('status', 'completed')
                ->sum('quantity');

            $sold = $coffee_type->sales()
                ->where('status', 'completed')
                ->sum('quantity');

            $adjustments = $coffee_type->inventoryMovements()
                ->where('movement_type', 'adjustment')
                ->sum('quantity');

            $entries = $coffee_type->inventoryMovements()
                ->where('movement_type', 'entry')
                ->sum('quantity');

            $exits = $coffee_type->inventoryMovements()
                ->where('movement_type', 'exit')
                ->sum('quantity');

            $returns = $coffee_type->inventoryMovements()
                ->where('movement_type', 'return')
                ->sum('quantity');

            $available = $purchased - $sold + $adjustments + $entries - $exits + $returns;
            
            $coffee_type->available_quantity = max(0, $available);
            
            return $coffee_type;
        });

        return response()->json($coffee_types_with_inventory);
    }

    public function show(CoffeeType $coffeeType)
    {
        return response()->json($coffeeType);
    }

    /**
     * Normaliza el precio para pesos colombianos (22.000 / 22,000 → 22000).
     */
    private function normalizeBasePrice(mixed $value): float
    {
        $str = preg_replace('/\s+/', '', (string) $value);
        $str = str_replace(',', '', $str);
        if (preg_match('/^(\d+)\.(\d{3})$/', $str, $m)) {
            return (float) ($m[1] . $m[2]);
        }
        return (float) $str;
    }

    /**
     * Crear un nuevo tipo de café (solo administradores)
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->merge(['base_price' => $this->normalizeBasePrice($request->input('base_price'))]);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'variety' => 'required|in:arabica,robusta',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'quality' => 'required|in:premium,special,commercial',
            'processing_type' => 'required|in:normal,wet,dry,pasilla',
            'active' => 'boolean',
        ]);

        $coffeeType = CoffeeType::create($validated);

        return response()->json($coffeeType, 201);
    }

    /**
     * Actualizar un tipo de café (solo administradores)
     */
    public function update(Request $request, CoffeeType $coffeeType)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->merge(['base_price' => $this->normalizeBasePrice($request->input('base_price'))]);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'variety' => 'required|in:arabica,robusta',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'quality' => 'required|in:premium,special,commercial',
            'processing_type' => 'required|in:normal,wet,dry,pasilla',
            'active' => 'boolean',
        ]);

        $coffeeType->update($validated);

        return response()->json($coffeeType);
    }

    /**
     * Eliminar un tipo de café (solo administradores)
     */
    public function destroy(CoffeeType $coffeeType)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar si tiene registros relacionados
        if ($coffeeType->purchases()->count() > 0 || $coffeeType->sales()->count() > 0) {
            // Desactivar en lugar de eliminar
            $coffeeType->update(['active' => false]);
            return response()->json([
                'message' => 'Tipo de café desactivado (tiene registros relacionados)',
                'coffee_type' => $coffeeType
            ]);
        }

        $coffeeType->delete();

        return response()->json(['message' => 'Tipo de café eliminado exitosamente']);
    }
}

