<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use App\Models\CoffeeType;
use App\Models\CashRegister;
use App\Models\HistoricalPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /** Normaliza precio en pesos (22.000 / 22,000 → 22000). */
    private function normalizePrice(mixed $value): float
    {
        $str = preg_replace('/\s+/', '', (string) $value);
        $str = str_replace(',', '', $str);
        if (preg_match('/^(\d+)\.(\d{3})$/', $str, $m)) {
            return (float) ($m[1] . $m[2]);
        }
        return (float) $str;
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['peasant', 'coffeeType']);

        // Filtros
        if ($request->filled('peasant_id')) {
            $query->where('peasant_id', $request->peasant_id);
        }

        if ($request->filled('coffee_type_id')) {
            $query->where('coffee_type_id', $request->coffee_type_id);
        }

        if ($request->filled('processing_type')) {
            $query->whereHas('coffeeType', function($q) use ($request) {
                $q->where('processing_type', $request->processing_type);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();
        $peasants = User::where('role', 'peasant')->active()->get();
        $coffee_types = CoffeeType::active()->get();

        return view('admin.purchases.index', compact('purchases', 'peasants', 'coffee_types'));
    }

    public function create()
    {
        $peasants = User::where('role', 'peasant')->active()->get();
        $coffee_types = CoffeeType::active()->get();

        return view('admin.purchases.create', compact('peasants', 'coffee_types'));
    }

    public function store(Request $request)
    {
        // Validar que haya una caja abierta
        $openCash = CashRegister::where('status', 'open')->first();
        if (!$openCash) {
            return redirect()->route('admin.purchases.index')
                ->with('error', 'Debe abrir una caja antes de realizar una compra. Por favor, vaya a Gestión de Caja y abra una caja.')
                ->withInput()
                ->with('open_create_modal', true);
        }

        $request->merge(['price_per_kg' => $this->normalizePrice($request->input('price_per_kg'))]);

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

        // Validar que el saldo de la caja sea suficiente
        if ($total > $openCash->available_balance) {
            return redirect()->route('admin.purchases.index')
                ->with('error', "El saldo disponible de la caja (\$" . number_format($openCash->available_balance, 0) . ") es insuficiente para realizar esta compra (\$" . number_format($total, 0) . ").")
                ->withInput()
                ->with('open_create_modal', true);
        }

        // Crear la compra
        $purchase = Purchase::create($validated);

        // Si la compra está completada, actualizar el saldo de la caja y registrar historial de precios
        if ($validated['status'] === 'completed') {
            $openCash->update([
                'available_balance' => $openCash->available_balance - $total,
                'kilos_purchased' => $openCash->kilos_purchased + $validated['quantity'],
            ]);

            HistoricalPrice::create([
                'coffee_type_id' => $validated['coffee_type_id'],
                'price' => $validated['price_per_kg'],
                'price_date' => $validated['purchase_date'],
                'operation_type' => 'purchase',
            ]);
        }

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Compra creada exitosamente.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['peasant', 'coffeeType', 'payments', 'invoice']);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $peasants = User::where('role', 'peasant')->active()->get();
        $coffee_types = CoffeeType::active()->get();

        return view('admin.purchases.edit', compact('purchase', 'peasants', 'coffee_types'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->merge(['price_per_kg' => $this->normalizePrice($request->input('price_per_kg'))]);

        $validated = $request->validate([
            'peasant_id' => 'required|exists:users,id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'price_per_kg' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'observations' => 'nullable|string',
        ]);

        $oldTotal = $purchase->quantity * $purchase->price_per_kg;
        $oldStatus = $purchase->status;
        $newTotal = $validated['quantity'] * $validated['price_per_kg'];

        // Si se está cambiando el estado a completado, validar caja y saldo
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $openCash = CashRegister::where('status', 'open')->first();
            if (!$openCash) {
                return redirect()->route('admin.purchases.edit', $purchase)
                    ->with('error', 'Debe abrir una caja antes de completar una compra.')
                    ->withInput();
            }

            if ($newTotal > $openCash->available_balance) {
                return redirect()->route('admin.purchases.edit', $purchase)
                    ->with('error', "El saldo disponible de la caja (\$" . number_format($openCash->available_balance, 0) . ") es insuficiente para completar esta compra (\$" . number_format($newTotal, 0) . ").")
                    ->withInput();
            }

            // Actualizar saldo de caja
            $openCash->update([
                'available_balance' => $openCash->available_balance - $newTotal,
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
        elseif ($oldStatus === 'completed' && $validated['status'] === 'completed' && $newTotal != $oldTotal) {
            $openCash = CashRegister::where('status', 'open')->first();
            
            if ($openCash) {
                $difference = $newTotal - $oldTotal;
                $kilosDifference = $validated['quantity'] - $purchase->quantity;
                
                // Validar que el saldo sea suficiente si aumentó
                if ($difference > 0 && $difference > $openCash->available_balance) {
                    return redirect()->route('admin.purchases.edit', $purchase)
                        ->with('error', "El saldo disponible de la caja es insuficiente para este cambio.")
                        ->withInput();
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

        return redirect()->route('admin.purchases.index')
            ->with('info', 'Compra actualizada exitosamente.');
    }

    public function destroy(Purchase $purchase)
    {
        // Verificar si tiene registros relacionados
        if ($purchase->payments()->count() > 0 || $purchase->invoice) {
            return redirect()->route('admin.purchases.index')
                ->with('error', 'No se puede eliminar la compra con pagos o facturas relacionadas.');
        }

        $purchase->delete();

        return redirect()->route('admin.purchases.index')
            ->with('error', 'Compra eliminada exitosamente.');
    }
}

