@extends('layouts.admin')

@section('title', 'Editar Tipo de Café - Administrador')
@section('page-title', 'Editar Tipo de Café')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="12" cy="12" rx="6" ry="9" fill="white"/>
                        <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#f3f4f6" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <ellipse cx="12" cy="14" rx="4" ry="5" fill="#f3f4f6" opacity="0.3"/>
                    </svg>
                    Editar Tipo de Café
                </h3>
                <p class="text-coffee-100 mt-1">Modifica la información del tipo de café</p>
            </div>

            <!-- Contenido del formulario -->
            <form action="{{ route('admin.coffee-types.update', $coffeeType) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <x-input 
                        label="Nombre del Tipo de Café" 
                        name="name" 
                        value="{{ old('name', $coffeeType->name) }}" 
                        required 
                        :error="$errors->first('name')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-select 
                            label="Variedad" 
                            name="variety" 
                            required 
                            :error="$errors->first('variety')"
                        >
                            <option value="arabica" {{ old('variety', $coffeeType->variety) === 'arabica' ? 'selected' : '' }}>Arábica</option>
                            <option value="robusta" {{ old('variety', $coffeeType->variety) === 'robusta' ? 'selected' : '' }}>Robusta</option>
                        </x-select>

                        <x-select 
                            label="Calidad" 
                            name="quality" 
                            required 
                            :error="$errors->first('quality')"
                        >
                            <option value="premium" {{ old('quality', $coffeeType->quality) === 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="special" {{ old('quality', $coffeeType->quality) === 'special' ? 'selected' : '' }}>Especial</option>
                            <option value="commercial" {{ old('quality', $coffeeType->quality) === 'commercial' ? 'selected' : '' }}>Comercial</option>
                        </x-select>

                        <x-select 
                            label="Tipo de Procesamiento" 
                            name="processing_type" 
                            required 
                            :error="$errors->first('processing_type')"
                        >
                            <option value="normal" {{ old('processing_type', $coffeeType->processing_type) === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="wet" {{ old('processing_type', $coffeeType->processing_type) === 'wet' ? 'selected' : '' }}>Mojado</option>
                            <option value="dry" {{ old('processing_type', $coffeeType->processing_type) === 'dry' ? 'selected' : '' }}>Seco</option>
                            <option value="pasilla" {{ old('processing_type', $coffeeType->processing_type) === 'pasilla' ? 'selected' : '' }}>Pasilla</option>
                        </x-select>

                        <x-input 
                            label="Precio Base (por kg) - Ej: 22000 o 22.000" 
                            name="base_price" 
                            type="text" 
                            inputmode="numeric"
                            value="{{ old('base_price', number_format((float) $coffeeType->base_price, 0, '', '')) }}" 
                            required 
                            :error="$errors->first('base_price')"
                            placeholder="Ej: 22000"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-coffee-600"></i>
                            Descripción
                        </label>
                        <textarea 
                            name="description" 
                            rows="4"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all resize-none"
                            placeholder="Ingrese una descripción del tipo de café"
                        >{{ old('description', $coffeeType->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <input 
                            type="checkbox" 
                            name="active" 
                            id="active" 
                            value="1"
                            {{ old('active', $coffeeType->active) ? 'checked' : '' }}
                            class="w-5 h-5 text-coffee-600 border-gray-300 rounded focus:ring-coffee-500"
                        >
                        <label for="active" class="ml-3 text-sm font-medium text-gray-700">
                            <i class="fas fa-check-circle mr-2 text-coffee-600"></i>
                            Tipo de café activo
                        </label>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('admin.coffee-types.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Tipo de Café
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
