@extends('layouts.admin')

@section('title', 'Nueva Cooperativa - Administrador')
@section('page-title', 'Crear Nueva Cooperativa')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-building mr-3"></i>
                    Nueva Cooperativa
                </h3>
                <p class="text-coffee-100 mt-1">Completa el formulario para crear una nueva cooperativa</p>
            </div>

            <!-- Contenido del formulario -->
            <form action="{{ route('admin.cooperatives.store') }}" method="POST" class="p-8">
                @csrf

                <div class="space-y-6">
                    <x-input 
                        label="Nombre de la Cooperativa" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        :error="$errors->first('name')"
                    />

                    <x-input 
                        label="NIT" 
                        name="nit" 
                        value="{{ old('nit') }}" 
                        required 
                        :error="$errors->first('nit')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input 
                            label="Teléfono" 
                            name="phone" 
                            value="{{ old('phone') }}" 
                            :error="$errors->first('phone')"
                        />

                        <x-input 
                            label="Email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email') }}" 
                            :error="$errors->first('email')"
                        />
                    </div>

                    <x-input 
                        label="Representante Legal" 
                        name="legal_representative" 
                        value="{{ old('legal_representative') }}" 
                        :error="$errors->first('legal_representative')"
                    />

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-coffee-600"></i>
                            Dirección
                        </label>
                        <textarea 
                            name="address" 
                            rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all resize-none"
                            placeholder="Ingrese la dirección de la cooperativa"
                        >{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <input 
                            type="checkbox" 
                            name="active" 
                            id="active" 
                            value="1"
                            {{ old('active', true) ? 'checked' : '' }}
                            class="w-5 h-5 text-coffee-600 border-gray-300 rounded focus:ring-coffee-500"
                        >
                        <label for="active" class="ml-3 text-sm font-medium text-gray-700">
                            <i class="fas fa-check-circle mr-2 text-coffee-600"></i>
                            Cooperativa activa
                        </label>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('admin.cooperatives.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cooperativa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
