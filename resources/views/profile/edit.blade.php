@php
    $layout = Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.peasant';
@endphp
@extends($layout)

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-4xl">
        @if(session('status') === 'profile-updated')
            <div class="mb-6 bg-coffee-50 border-l-4 border-coffee-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-coffee-600 mr-3"></i>
                    <p class="text-coffee-800">Perfil actualizado exitosamente.</p>
                </div>
            </div>
        @endif

        <!-- Información del Perfil -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-user-circle mr-3"></i>
                    Información del Perfil
                </h3>
                <p class="text-coffee-100 mt-1">Gestiona tu información personal y foto de perfil</p>
            </div>

            <!-- Contenido -->
            <div class="p-8">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Foto de Perfil -->
                    <div class="flex flex-col items-center mb-8 pb-8 border-b border-gray-200">
                        <div class="relative mb-4">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                     alt="Foto de perfil" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-coffee-200 shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full {{ Auth::user()->role === 'admin' ? 'bg-gradient-to-br from-coffee-600 to-coffee-800' : 'bg-gradient-to-br from-green-600 to-green-800' }} flex items-center justify-center border-4 border-coffee-200 shadow-lg">
                                    <i class="fas {{ Auth::user()->role === 'admin' ? 'fa-user-shield' : 'fa-user' }} text-white text-5xl"></i>
                                </div>
                            @endif
                            <label for="profile_image" 
                                   class="absolute bottom-0 right-0 w-10 h-10 bg-coffee-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-coffee-700 transition-colors shadow-lg border-2 border-white">
                                <i class="fas fa-camera text-white text-sm"></i>
                            </label>
                            <input type="file" 
                                   id="profile_image" 
                                   name="profile_image" 
                                   accept="image/*" 
                                   class="hidden"
                                   onchange="previewImage(this)">
                        </div>
                        <p class="text-sm text-gray-600 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Haz clic en el ícono de cámara para cambiar tu foto de perfil
                        </p>
                        <div id="imagePreview" class="mt-4 hidden">
                            <p class="text-sm text-coffee-600 font-medium">
                                <i class="fas fa-check-circle mr-1"></i>
                                Vista previa de la nueva imagen
                            </p>
                        </div>
                        @error('profile_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información Personal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input 
                            label="Nombre Completo" 
                            name="name" 
                            value="{{ old('name', Auth::user()->name) }}" 
                            required 
                            :error="$errors->first('name')"
                        />

                        <x-input 
                            label="Correo Electrónico" 
                            name="email" 
                            type="email" 
                            value="{{ old('email', Auth::user()->email) }}" 
                            required 
                            :error="$errors->first('email')"
                        />

                        <x-input 
                            label="Teléfono" 
                            name="phone" 
                            value="{{ old('phone', Auth::user()->phone) }}" 
                            :error="$errors->first('phone')"
                        />

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-coffee-600"></i>
                                Dirección
                            </label>
                            <textarea 
                                name="address" 
                                rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all resize-none"
                                placeholder="Ingrese su dirección"
                            >{{ old('address', Auth::user()->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <div class="bg-coffee-50 border-2 border-coffee-200 rounded-xl p-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 {{ Auth::user()->role === 'admin' ? 'bg-coffee-600' : 'bg-green-600' }} rounded-full flex items-center justify-center">
                                        <i class="fas {{ Auth::user()->role === 'admin' ? 'fa-user-shield' : 'fa-user' }} text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-coffee-900">Rol del Usuario</p>
                                        <p class="text-sm text-coffee-700">
                                            {{ Auth::user()->role === 'admin' ? 'Administrador' : 'Campesino' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('peasant.dashboard') }}" 
                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            const previewDiv = document.getElementById('imagePreview');
            
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-32 h-32 rounded-full object-cover border-4 border-coffee-200 shadow-lg mx-auto';
                previewDiv.innerHTML = '';
                previewDiv.appendChild(img);
                previewDiv.classList.remove('hidden');
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
