<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Cooperative;
use App\Models\CoffeeType;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Models\HistoricalPrice;
use Illuminate\Http\Request;

class SaleController extends Controller
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

    /** Inventario disponible (kg) para un tipo de café. Nunca negativo. */
    private function getAvailableInventory(int $coffeeTypeId): float
    {
        $coffee_type = CoffeeType::find($coffeeTypeId);
        if (! $coffee_type) {
            return 0.0;
        }
        $purchased = $coffee_type->purchases()->where('status', 'completed')->sum('quantity');
        $sold = $coffee_type->sales()->where('status', 'completed')->sum('quantity');
        $adjustments = $coffee_type->inventoryMovements()->where('movement_type', 'adjustment')->sum('quantity');
        $entries = $coffee_type->inventoryMovements()->where('movement_type', 'entry')->sum('quantity');
        $exits = $coffee_type->inventoryMovements()->where('movement_type', 'exit')->sum('quantity');
        $returns = $coffee_type->inventoryMovements()->where('movement_type', 'return')->sum('quantity');
        $available = $purchased - $sold + $adjustments + $entries - $exits + $returns;
        return max(0, (float) $available);
    }

    public function index(Request $request)
    {
        $query = Sale::with(['cooperative', 'coffeeType']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cooperative_id')) {
            $query->where('cooperative_id', $request->cooperative_id);
        }

        if ($request->filled('coffee_type_id')) {
            $query->where('coffee_type_id', $request->coffee_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $cooperatives = Cooperative::active()->get();
        $coffee_types = CoffeeType::active()->with(['purchases', 'sales', 'inventoryMovements'])->get();
        
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
            
            $coffee_type->available_inventory = max(0, $available);
            
            return $coffee_type;
        });

        return view('admin.sales.index', compact('sales', 'cooperatives', 'coffee_types_with_inventory'));
    }

    public function create()
    {
        $cooperatives = Cooperative::active()->get();
        $coffee_types = CoffeeType::active()->with(['purchases', 'sales', 'inventoryMovements'])->get();
        
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
            
            $coffee_type->available_inventory = max(0, $available);
            
            return $coffee_type;
        });

        return view('admin.sales.create', compact('cooperatives', 'coffee_types_with_inventory'));
    }

    public function store(Request $request)
    {
        $request->merge(['price_per_kg' => $this->normalizePrice($request->input('price_per_kg'))]);

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
            $available = $this->getAvailableInventory((int) $validated['coffee_type_id']);
            if ($available <= 0) {
                return redirect()->route('admin.sales.index')
                    ->with('error', 'No se puede vender: no hay stock disponible para este tipo de café.')
                    ->withInput();
            }
            if ((float) $validated['quantity'] > $available) {
                return redirect()->route('admin.sales.index')
                    ->with('error', 'No hay suficiente stock. Disponible: ' . number_format($available, 2, ',', '.') . ' kg.')
                    ->withInput();
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

        return redirect()->route('admin.sales.index')
            ->with('success', 'Venta creada exitosamente.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['cooperative', 'coffeeType', 'payments', 'invoice']);

        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $cooperatives = Cooperative::active()->get();
        $coffee_types = CoffeeType::active()->get();

        return view('admin.sales.edit', compact('sale', 'cooperatives', 'coffee_types'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->merge(['price_per_kg' => $this->normalizePrice($request->input('price_per_kg'))]);

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
            $available = $this->getAvailableInventory((int) $validated['coffee_type_id']);
            if ($sale->status === 'completed' && (int) $sale->coffee_type_id === (int) $validated['coffee_type_id']) {
                $available += (float) $sale->quantity;
            }
            if ($available <= 0) {
                return redirect()->route('admin.sales.index')
                    ->with('error', 'No se puede vender: no hay stock disponible para este tipo de café.');
            }
            if ((float) $validated['quantity'] > $available) {
                return redirect()->route('admin.sales.index')
                    ->with('error', 'No hay suficiente stock. Disponible: ' . number_format($available, 2, ',', '.') . ' kg.');
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

        return redirect()->route('admin.sales.index')
            ->with('info', 'Venta actualizada exitosamente.');
    }

    public function destroy(Sale $sale)
    {
        // Verificar si tiene registros relacionados
        if ($sale->payments()->count() > 0 || $sale->invoice) {
            return redirect()->route('admin.sales.index')
                ->with('error', 'No se puede eliminar la venta con pagos o facturas relacionadas.');
        }

        $sale->delete();

        return redirect()->route('admin.sales.index')
            ->with('error', 'Venta eliminada exitosamente.');
    }
}

