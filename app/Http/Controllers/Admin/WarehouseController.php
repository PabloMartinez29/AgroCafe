<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoffeeType;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        // Calcular inventario para cada tipo de café
        $coffee_types = CoffeeType::with(['purchases', 'sales', 'inventoryMovements'])->get();

        $inventory = $coffee_types->map(function ($coffee_type) {
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
            $available = max(0, $available);

            return [
                'coffee_type' => $coffee_type,
                'purchased' => $purchased,
                'sold' => $sold,
                'available' => $available,
                'adjustments' => $adjustments,
                'entries' => $entries,
                'exits' => $exits,
                'returns' => $returns,
            ];
        });

        $recent_purchases = Purchase::with(['coffeeType', 'peasant'])
            ->where('status', 'completed')
            ->orderBy('purchase_date', 'desc')
            ->limit(50)
            ->get()
            ->map(fn ($p) => (object) [
                'type' => 'compra',
                'date' => $p->purchase_date,
                'quantity' => $p->quantity,
                'coffee_type_name' => $p->coffeeType->name ?? '',
                'detail' => $p->peasant?->name ?? 'N/A',
            ]);

        $recent_sales = Sale::with(['coffeeType', 'cooperative'])
            ->where('status', 'completed')
            ->orderBy('sale_date', 'desc')
            ->limit(50)
            ->get()
            ->map(fn ($s) => (object) [
                'type' => 'venta',
                'date' => $s->sale_date,
                'quantity' => $s->quantity,
                'coffee_type_name' => $s->coffeeType->name ?? '',
                'detail' => $s->cooperative?->name ?? $s->client_name ?? 'N/A',
            ]);

        $combined = $recent_purchases->concat($recent_sales)
            ->sortByDesc('date')
            ->values();

        $perPage = 10;
        $page = $request->get('movimientos_page', 1);
        $recent_movements = new LengthAwarePaginator(
            $combined->forPage($page, $perPage),
            $combined->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'movimientos_page']
        );

        return view('admin.warehouse.index', compact('inventory', 'recent_movements'));
    }

    public function createMovement()
    {
        $coffee_types = CoffeeType::active()->get();
        return view('admin.warehouse.create-movement', compact('coffee_types'));
    }

    public function storeMovement(Request $request)
    {
        $validated = $request->validate([
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'quantity' => 'required|numeric',
            'movement_type' => 'required|in:adjustment,entry,exit,return',
            'reason' => 'nullable|string',
            'movement_date' => 'required|date',
        ]);

        $validated['user_id'] = auth()->id();

        InventoryMovement::create($validated);

        return redirect()->route('admin.warehouse.index')
            ->with('success', 'Movimiento de inventario registrado exitosamente.');
    }
}

