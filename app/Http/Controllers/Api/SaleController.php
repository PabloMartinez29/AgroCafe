<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\CoffeeType;
use App\Models\HistoricalPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Solo los administradores pueden ver las ventas
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Sale::with(['cooperative', 'coffeeType']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cooperative_id')) {
            $query->where('cooperative_id', $request->cooperative_id);
        }

        if ($request->filled('date_from')) {
            $query->where('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(15);

        return response()->json($sales);
    }

    public function show(Sale $sale)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sale->load(['cooperative', 'coffeeType', 'payments', 'invoice']);

        return response()->json($sale);
    }

    /**
     * Crear una nueva venta (solo administradores)
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'cooperative_id' => 'nullable|exists:cooperatives,id',
            'client_name' => 'nullable|string|max:150|required_without:cooperative_id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'price_per_kg' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed') {
            $coffeeType = CoffeeType::find($validated['coffee_type_id']);
            $available = $coffeeType ? max(0, (float) $coffeeType->available_quantity) : 0;
            if ($available <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede vender: no hay stock disponible para este tipo de café.',
                ], 422);
            }
            if ((float) $validated['quantity'] > $available) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock. Disponible: ' . number_format($available, 2, ',', '.') . ' kg.',
                ], 422);
            }
        }

        $sale = Sale::create($validated);

        // Registrar historial de precios cuando la venta esté completada
        if ($validated['status'] === 'completed') {
            HistoricalPrice::create([
                'coffee_type_id' => $validated['coffee_type_id'],
                'price' => $validated['price_per_kg'],
                'price_date' => $validated['sale_date'],
                'operation_type' => 'sale',
            ]);
        }

        $sale->load(['cooperative', 'coffeeType']);

        return response()->json($sale, 201);
    }

    /**
     * Actualizar una venta (solo administradores)
     */
    public function update(Request $request, Sale $sale)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'cooperative_id' => 'nullable|exists:cooperatives,id',
            'client_name' => 'nullable|string|max:150|required_without:cooperative_id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'price_per_kg' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed') {
            $coffeeType = CoffeeType::find($validated['coffee_type_id']);
            $available = $coffeeType ? max(0, (float) $coffeeType->available_quantity) : 0;
            if ($sale->status === 'completed' && (int) $sale->coffee_type_id === (int) $validated['coffee_type_id']) {
                $available += (float) $sale->quantity;
            }
            if ($available <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede vender: no hay stock disponible para este tipo de café.',
                ], 422);
            }
            if ((float) $validated['quantity'] > $available) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock. Disponible: ' . number_format($available, 2, ',', '.') . ' kg.',
                ], 422);
            }
        }

        $sale->update($validated);

        // Registrar historial de precios cuando la venta esté completada
        if ($validated['status'] === 'completed') {
            HistoricalPrice::create([
                'coffee_type_id' => $validated['coffee_type_id'],
                'price' => $validated['price_per_kg'],
                'price_date' => $validated['sale_date'],
                'operation_type' => 'sale',
            ]);
        }

        $sale->load(['cooperative', 'coffeeType']);

        return response()->json($sale);
    }

    /**
     * Eliminar una venta (solo administradores)
     */
    public function destroy(Sale $sale)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar si tiene registros relacionados
        if ($sale->payments()->count() > 0 || $sale->invoice) {
            return response()->json([
                'message' => 'No se puede eliminar la venta porque tiene pagos o facturas relacionadas.'
            ], 422);
        }

        $sale->delete();

        return response()->json(['message' => 'Venta eliminada exitosamente']);
    }
}

