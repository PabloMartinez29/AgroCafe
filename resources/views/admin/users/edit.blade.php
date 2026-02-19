@extends('layouts.admin')

@section('title', 'Editar Usuario - Administrador')
@section('page-title', 'Editar Usuario')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-user-edit mr-3"></i>
                    Editar Usuario
                </h3>
                <p class="text-coffee-100 mt-1">Modifica la información del usuario</p>
            </div>

            <!-- Contenido del formulario -->
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input 
                        label="Nombre Completo" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        :error="$errors->first('name')"
                    />

                    <x-input 
                        label="Correo Electrónico" 
                        name="email" 
                        type="email" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        :error="$errors->first('email')"
                    />

                    <x-input 
                        label="Teléfono" 
                        name="phone" 
                        value="{{ old('phone', $user->phone) }}" 
                        :error="$errors->first('phone')"
                    />

                    <x-select 
                        label="Rol" 
                        name="role" 
                        required 
                        :error="$errors->first('role')"
                    >
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="peasant" {{ old('role', $user->role) === 'peasant' ? 'selected' : '' }}>Campesino</option>
                    </x-select>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-coffee-600"></i>
                            Dirección
                        </label>
                        <textarea 
                            name="address" 
                            rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all resize-none"
                            placeholder="Ingrese la dirección del usuario"
                        >{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-input 
                        label="Nueva Contraseña (dejar vacío para mantener la actual)" 
                        name="password" 
                        type="password" 
                        :error="$errors->first('password')"
                    />

                    <x-input 
                        label="Confirmar Nueva Contraseña" 
                        name="password_confirmation" 
                        type="password" 
                    />

                    <div class="md:col-span-2">
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                            <input 
                                type="checkbox" 
                                name="active" 
                                id="active" 
                                value="1"
                                {{ old('active', $user->active) ? 'checked' : '' }}
                                class="w-5 h-5 text-coffee-600 border-gray-300 rounded focus:ring-coffee-500"
                            >
                            <label for="active" class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle mr-2 text-coffee-600"></i>
                                Usuario activo
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
