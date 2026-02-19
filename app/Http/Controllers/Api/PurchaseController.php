<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\HistoricalPrice;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $query = Purchase::with(['peasant', 'coffeeType']);
        } else {
            $query = Purchase::where('peasant_id', $user->id)
                ->with('coffeeType');
        }

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('coffee_type_id')) {
            $query->where('coffee_type_id', $request->coffee_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(15);

        return response()->json($purchases);
    }

    public function show(Purchase $purchase)
    {
        $user = Auth::user();

        // Los campesinos solo pueden ver sus propias compras
        if ($user->isPeasant() && $purchase->peasant_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $purchase->load(['peasant', 'coffeeType', 'payments', 'invoice']);

        return response()->json($purchase);
    }

    /**
     * Crear una nueva compra (solo administradores)
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'peasant_id' => 'required|exists:users,id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'price_per_kg' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'observations' => 'nullable|string',
        ]);

        // Calcular el total de la compra
        $total = $validated['quantity'] * $validated['price_per_kg'];

        // Si la compra está completada, validar y actualizar la caja
        if ($validated['status'] === 'completed') {
            $openCash = CashRegister::where('status', 'open')->first();
            
            if (!$openCash) {
                return response()->json([
                    'message' => 'Debe abrir una caja antes de realizar una compra completada.'
                ], 422);
            }

            // Validar que el saldo de la caja sea suficiente
            if ($total > $openCash->available_balance) {
                return response()->json([
                    'message' => "El saldo disponible de la caja (\$" . number_format($openCash->available_balance, 0) . ") es insuficiente para realizar esta compra (\$" . number_format($total, 0) . ")."
                ], 422);
            }

            // Actualizar el saldo de la caja y los kilos comprados
            $openCash->update([
                'available_balance' => $openCash->available_balance - $total,
                'kilos_purchased' => $openCash->kilos_purchased + $validated['quantity'],
            ]);
        }

        $purchase = Purchase::create($validated);

        // Registrar historial de precios cuando la compra esté completada
        if ($validated['status'] === 'completed') {
            HistoricalPrice::create([
                'coffee_type_id' => $validated['coffee_type_id'],
                'price' => $validated['price_per_kg'],
                'price_date' => $validated['purchase_date'],
                'operation_type' => 'purchase',
            ]);
        }

        $purchase->load(['peasant', 'coffeeType']);

        return response()->json($purchase, 201);
    }

    /**
     * Actualizar una compra (solo administradores)
     */
    public function update(Request $request, Purchase $purchase)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'peasant_id' => 'required|exists:users,id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'price_per_kg' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'observations' => 'nullable|string',
        ]);

        // Calcular el total de la compra
        $total = $validated['quantity'] * $validated['price_per_kg'];
        $oldTotal = $purchase->quantity * $purchase->price_per_kg;
        $oldStatus = $purchase->status;

        // Si se está cambiando el estado a completado, validar caja y saldo
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $openCash = CashRegister::where('status', 'open')->first();
            
            if (!$openCash) {
                return response()->json([
                    'message' => 'Debe abrir una caja antes de completar una compra.'
                ], 422);
            }

            // Validar que el saldo de la caja sea suficiente
            if ($total > $openCash->available_balance) {
                return response()->json([
                    'message' => "El saldo disponible de la caja (\$" . number_format($openCash->available_balance, 0) . ") es insuficiente para completar esta compra (\$" . number_format($total, 0) . ")."
                ], 422);
            }

            // Actualizar saldo de caja
            $openCash->update([
                'available_balance' => $openCash->available_balance - $total,
                'kilos_purchased' => $openCash->kilos_purchased + $validated['quantity'],
            ]);
        }
        // Si se estaba completada y ahora no, revertir el cambio en la caja
        elseif ($oldStatus === 'completed' && $validated['status'] !== 'completed') {
            $openCash = CashRegister::where('status', 'open')->first();
            
            if ($openCash) {
                // Revertir el saldo y los kilos
                $openCash->update([
                    'available_balance' => $openCash->available_balance + $oldTotal,
                    'kilos_purchased' => max(0, $openCash->kilos_purchased - $purchase->quantity),
                ]);
            }
        }
        // Si estaba completada y sigue completada pero cambió el total
        elseif ($oldStatus === 'completed' && $validated['status'] === 'completed' && $total != $oldTotal) {
            $openCash = CashRegister::where('status', 'open')->first();
            
            if ($openCash) {
                $difference = $total - $oldTotal;
                $kilosDifference = $validated['quantity'] - $purchase->quantity;
                
                // Validar que el saldo sea suficiente si aumentó
                if ($difference > 0 && $difference > $openCash->available_balance) {
                    return response()->json([
                        'message' => "El saldo disponible de la caja es insuficiente para este cambio."
                    ], 422);
                }
                
                $openCash->update([
                    'available_balance' => $openCash->available_balance - $difference,
                    'kilos_purchased' => $openCash->kilos_purchased + $kilosDifference,
                ]);
            }
        }

        $purchase->update($validated);

        // Registrar historial de precios cuando la compra esté completada
        if ($validated['status'] === 'completed') {
            HistoricalPrice::create([
                'coffee_type_id' => $validated['coffee_type_id'],
                'price' => $validated['price_per_kg'],
                'price_date' => $validated['purchase_date'],
                'operation_type' => 'purchase',
            ]);
        }

        $purchase->load(['peasant', 'coffeeType']);

        return response()->json($purchase);
    }

    /**
     * Eliminar una compra (solo administradores)
     */
    public function destroy(Purchase $purchase)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar si tiene registros relacionados
        if ($purchase->payments()->count() > 0 || $purchase->invoice) {
            return response()->json([
                'message' => 'No se puede eliminar la compra porque tiene pagos o facturas relacionadas.'
            ], 422);
        }

        $purchase->delete();

        return response()->json(['message' => 'Compra eliminada exitosamente']);
    }
}

