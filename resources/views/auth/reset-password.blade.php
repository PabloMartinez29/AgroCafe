<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-coffee-50 to-coffee-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo y Título -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-coffee-600 mb-4">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-coffee-900">
                    Restablecer Contraseña
                </h2>
                <p class="mt-2 text-sm text-coffee-600">
                    Ingresa tu nueva contraseña para completar el restablecimiento.
                </p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 border border-coffee-200">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-coffee-600"></i>
                            Correo Electrónico
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email', $request->email) }}" 
                            required 
                            autofocus
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 focus:z-10 sm:text-sm transition-colors"
                            placeholder="tu@email.com"
                        />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-coffee-600"></i>
                            Nueva Contraseña
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 focus:z-10 sm:text-sm transition-colors"
                            placeholder="Mínimo 8 caracteres"
                        />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-coffee-600"></i>
                            Confirmar Contraseña
                        </label>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 focus:z-10 sm:text-sm transition-colors"
                            placeholder="Repite tu contraseña"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-coffee-600 hover:bg-coffee-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coffee-500 transition-colors"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-check-circle text-coffee-300 group-hover:text-coffee-200"></i>
                            </span>
                            Restablecer Contraseña
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a 
                        href="{{ route('login') }}" 
                        class="inline-flex items-center text-sm font-medium text-coffee-600 hover:text-coffee-700 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al inicio de sesión
                    </a>
                </div>
            </div>

            <!-- Help Text -->
            <div class="text-center">
                <p class="text-xs text-coffee-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Tu contraseña debe tener al menos 8 caracteres.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
