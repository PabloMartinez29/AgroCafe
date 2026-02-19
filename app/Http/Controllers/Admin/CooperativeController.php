<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cooperative;
use Illuminate\Http\Request;

class CooperativeController extends Controller
{
    public function index()
    {
        $cooperatives = Cooperative::orderBy('created_at', 'desc')->get();
        return view('admin.cooperatives.index', compact('cooperatives'));
    }

    public function create()
    {
        return view('admin.cooperatives.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'nit' => 'required|string|max:20|unique:cooperatives',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'legal_representative' => 'nullable|string|max:100',
            'active' => 'boolean',
        ]);

        Cooperative::create($validated);

        return redirect()->route('admin.cooperatives.index')
            ->with('success', 'Cooperativa creada exitosamente.');
    }

    public function show(Cooperative $cooperative)
    {
        $cooperative->load('sales');
        return view('admin.cooperatives.show', compact('cooperative'));
    }

    public function edit(Cooperative $cooperative)
    {
        return view('admin.cooperatives.edit', compact('cooperative'));
    }

    public function update(Request $request, Cooperative $cooperative)
    {
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

        return redirect()->route('admin.cooperatives.index')
            ->with('info', 'Cooperativa actualizada exitosamente.');
    }

    public function destroy(Cooperative $cooperative)
    {
        // Verificar si tiene registros relacionados
        if ($cooperative->sales()->count() > 0) {
            // Desactivar en lugar de eliminar
            $cooperative->update(['active' => false]);
            return redirect()->route('admin.cooperatives.index')
                ->with('error', 'Cooperativa desactivada (tiene ventas relacionadas).');
        }

        $cooperative->delete();

        return redirect()->route('admin.cooperatives.index')
            ->with('error', 'Cooperativa eliminada exitosamente.');
    }
}

