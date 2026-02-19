<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoffeeType;
use Illuminate\Http\Request;

class CoffeeTypeController extends Controller
{
    /**
     * Normaliza el precio para pesos colombianos: acepta 22.000, 22,000 o 22000.
     * En PHP "22.000" se interpreta como 22; aquí lo tratamos como 22000.
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

    public function index()
    {
        $coffee_types = CoffeeType::orderBy('created_at', 'desc')->get();
        return view('admin.coffee-types.index', compact('coffee_types'));
    }

    public function create()
    {
        return view('admin.coffee-types.create');
    }

    public function store(Request $request)
    {
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

        CoffeeType::create($validated);

        return redirect()->route('admin.coffee-types.index')
            ->with('success', 'Tipo de café creado exitosamente.');
    }

    public function show(CoffeeType $coffeeType)
    {
        return view('admin.coffee-types.show', compact('coffeeType'));
    }

    public function edit(CoffeeType $coffeeType)
    {
        return view('admin.coffee-types.edit', compact('coffeeType'));
    }

    public function update(Request $request, CoffeeType $coffeeType)
    {
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

        return redirect()->route('admin.coffee-types.index')
            ->with('info', 'Tipo de café actualizado exitosamente.');
    }

    public function destroy(CoffeeType $coffeeType)
    {
        // Verificar si tiene registros relacionados
        if ($coffeeType->purchases()->count() > 0 || $coffeeType->sales()->count() > 0) {
            // Desactivar en lugar de eliminar
            $coffeeType->update(['active' => false]);
            return redirect()->route('admin.coffee-types.index')
                ->with('error', 'Tipo de café desactivado (tiene registros relacionados).');
        }

        $coffeeType->delete();

        return redirect()->route('admin.coffee-types.index')
            ->with('error', 'Tipo de café eliminado exitosamente.');
    }
}

