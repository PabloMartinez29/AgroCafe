@extends('layouts.admin')

@section('title', 'Nuevo Movimiento - Administrador')
@section('page-title', 'Registrar Nuevo Movimiento de Inventario')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.warehouse.movements.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <x-select 
                    label="Tipo de Café" 
                    name="coffee_type_id" 
                    required 
                    :error="$errors->first('coffee_type_id')"
                >
                    <option value="">Seleccione un tipo de café</option>
                    @foreach($coffee_types as $type)
                        <option value="{{ $type->id }}" {{ old('coffee_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </x-select>

                <x-select 
                    label="Tipo de Movimiento" 
                    name="movement_type" 
                    required 
                    :error="$errors->first('movement_type')"
                >
                    <option value="">Seleccione un tipo</option>
                    <option value="adjustment" {{ old('movement_type') === 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                    <option value="entry" {{ old('movement_type') === 'entry' ? 'selected' : '' }}>Entrada</option>
                    <option value="exit" {{ old('movement_type') === 'exit' ? 'selected' : '' }}>Salida</option>
                    <option value="return" {{ old('movement_type') === 'return' ? 'selected' : '' }}>Devolución</option>
                </x-select>

                <x-input 
                    label="Cantidad (kg)" 
                    name="quantity" 
                    type="number" 
                    step="0.01" 
                    value="{{ old('quantity') }}" 
                    required 
                    :error="$errors->first('quantity')"
                />

                <x-input 
                    label="Fecha del Movimiento" 
                    name="movement_date" 
                    type="date" 
                    value="{{ old('movement_date', date('Y-m-d')) }}" 
                    required 
                    :error="$errors->first('movement_date')"
                />

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Razón/Motivo</label>
                    <textarea 
                        name="reason" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-colors"
                    >{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.warehouse.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Movimiento
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

