<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-coffee-50 to-coffee-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo y Título -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-coffee-600 mb-4">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="12" cy="12" rx="6" ry="9" fill="white"/>
                        <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#f3f4f6" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <ellipse cx="12" cy="14" rx="4" ry="5" fill="#f3f4f6" opacity="0.3"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-coffee-900">
                    ¿Olvidaste tu contraseña?
                </h2>
                <p class="mt-2 text-sm text-coffee-600">
                    No te preocupes. Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 border border-coffee-200">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

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
                            value="{{ old('email') }}" 
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

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-coffee-600 hover:bg-coffee-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coffee-500 transition-colors"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-paper-plane text-coffee-300 group-hover:text-coffee-200"></i>
                            </span>
                            Enviar Enlace de Restablecimiento
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
                    <i class="fas fa-info-circle mr-1"></i>
                    Si no recibes el correo, revisa tu carpeta de spam o contacta al administrador.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
