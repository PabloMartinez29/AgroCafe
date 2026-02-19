<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-white relative overflow-hidden">
        <!-- Back to Home Button - Top Corner -->
        <div class="absolute top-6 right-6 z-20 animate-fade-in">
            <a 
                href="{{ url('/') }}" 
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white/90 backdrop-blur-sm rounded-lg hover:text-coffee-600 hover:bg-white transition-all duration-200 group shadow-md hover:shadow-lg"
            >
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Volver al inicio
            </a>
        </div>
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Floating Coffee Beans -->
            <div class="absolute top-20 left-10 w-16 h-16 opacity-10 animate-float">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                    <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="absolute top-40 right-20 w-12 h-12 opacity-10 animate-float-delayed">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                    <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="absolute bottom-32 left-1/4 w-14 h-14 opacity-10 animate-float-slow">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                    <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="absolute bottom-20 right-1/3 w-10 h-10 opacity-10 animate-float-delayed-slow">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                    <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            
            <!-- Gradient Orbs -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-coffee-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-coffee-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow-delayed"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                <!-- Left Side - Welcome Section -->
                <div class="text-center lg:text-left animate-slide-in-left">
                    <div class="inline-flex items-center justify-center mb-8 animate-bounce-in">
                        <div class="relative">
                            <div class="absolute inset-0 bg-coffee-200 rounded-full blur-xl opacity-50 animate-ping-slow"></div>
                            <div class="relative w-32 h-32 bg-white rounded-3xl shadow-2xl flex items-center justify-center transform hover:scale-110 hover:rotate-6 transition-all duration-500 border-4 border-coffee-100">
                                <svg class="w-20 h-20 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                                    <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                    <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 mb-4 tracking-tight leading-tight animate-fade-in-up">
                        Bienvenido a <span class="text-coffee-600 bg-gradient-to-r from-coffee-600 to-coffee-800 bg-clip-text text-transparent">AgroCafé</span>
                    </h1>
                    <p class="text-gray-600 text-lg lg:text-xl mb-8 animate-fade-in-up-delayed">Sistema de gestión integral para la compra y venta de café</p>
                    
                    <div class="space-y-4 text-gray-700 animate-fade-in-up-delayed-2">
                        <div class="flex items-center justify-center lg:justify-start group">
                            <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-4 group-hover:bg-coffee-200 transition-colors">
                                <i class="fas fa-shopping-cart text-coffee-600"></i>
                            </div>
                            <span class="text-base font-medium">Gestión de compras y ventas</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start group">
                            <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-4 group-hover:bg-coffee-200 transition-colors">
                                <i class="fas fa-warehouse text-coffee-600"></i>
                            </div>
                            <span class="text-base font-medium">Control de inventario en tiempo real</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start group">
                            <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-4 group-hover:bg-coffee-200 transition-colors">
                                <i class="fas fa-chart-line text-coffee-600"></i>
                            </div>
                            <span class="text-base font-medium">Análisis de precios y reportes</span>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full animate-slide-in-right">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Login Card -->
                    <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 lg:p-12 border-2 border-coffee-100 relative overflow-hidden group hover:shadow-3xl transition-all duration-500">
                        <!-- Decorative Elements -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-coffee-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-coffee-100 rounded-full -ml-12 -mb-12 opacity-30"></div>
                        
                        <div class="relative z-10">
                            <div class="mb-8 text-center lg:text-left">
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2 animate-fade-in">Iniciar Sesión</h2>
                                <p class="text-gray-600 animate-fade-in-delayed">Ingresa tus credenciales para acceder</p>
                            </div>
                            
                            <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

                                <!-- Email Field -->
                                <div class="space-y-2 animate-slide-up" style="animation-delay: 0.1s;">
                                    <label for="email" class="block text-sm font-bold text-gray-700">
                                        <i class="fas fa-envelope text-coffee-600 mr-2"></i>
                                        Correo Electrónico
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400 group-focus-within:text-coffee-600 transition-colors duration-300"></i>
                                        </div>
                                        <input 
                                            id="email" 
                                            name="email" 
                                            type="email" 
                                            value="{{ old('email') }}" 
                                            required 
                                            autofocus 
                                            autocomplete="username"
                                            class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all duration-300 bg-gray-50 focus:bg-white transform focus:scale-[1.02]"
                                            placeholder="correo@ejemplo.com"
                                        />
                                    </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

                                <!-- Password Field -->
                                <div class="space-y-2 animate-slide-up" style="animation-delay: 0.2s;">
                                    <label for="password" class="block text-sm font-bold text-gray-700">
                                        <i class="fas fa-lock text-coffee-600 mr-2"></i>
                                        Contraseña
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400 group-focus-within:text-coffee-600 transition-colors duration-300"></i>
                                        </div>
                                        <input 
                                            id="password" 
                                            name="password" 
                            type="password"
                                            required 
                                            autocomplete="current-password"
                                            class="block w-full pl-12 pr-12 py-4 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all duration-300 bg-gray-50 focus:bg-white transform focus:scale-[1.02]"
                                            placeholder="••••••••"
                                        />
                                        <button 
                                            type="button" 
                                            id="togglePassword"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-coffee-600 transition-all duration-200 transform hover:scale-110"
                                            aria-label="Mostrar/Ocultar contraseña"
                                        >
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="flex items-center justify-between animate-slide-up" style="animation-delay: 0.3s;">
                                    <label class="flex items-center cursor-pointer group">
                                        <input 
                                            id="remember_me" 
                                            name="remember" 
                                            type="checkbox" 
                                            class="w-4 h-4 text-coffee-600 bg-gray-100 border-gray-300 rounded focus:ring-coffee-500 focus:ring-2 cursor-pointer transition-all"
                                        />
                                        <span class="ml-2 text-sm text-gray-700 group-hover:text-coffee-600 transition-colors">
                                            Recordarme
                                        </span>
            </label>

            @if (Route::has('password.request'))
                                        <a 
                                            href="{{ route('password.request') }}" 
                                            class="text-sm font-semibold text-coffee-600 hover:text-coffee-700 transition-all duration-200 flex items-center group"
                                        >
                                            <i class="fas fa-key text-xs mr-1.5 group-hover:rotate-12 transition-transform"></i>
                                            ¿Olvidaste tu contraseña?
                </a>
            @endif
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-2 animate-slide-up" style="animation-delay: 0.4s;">
                                    <button 
                                        type="submit" 
                                        class="group relative w-full flex justify-center items-center py-4 px-6 text-base font-bold text-white bg-gradient-to-r from-coffee-600 via-coffee-700 to-coffee-600 rounded-xl hover:from-coffee-700 hover:to-coffee-800 focus:outline-none focus:ring-4 focus:ring-coffee-500/50 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl shadow-lg overflow-hidden bg-[length:200%_100%] hover:bg-[position:100%_0]"
                                    >
                                        <span class="absolute inset-0 w-3 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                                        <i class="fas fa-sign-in-alt mr-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                        Iniciar Sesión
                                    </button>
                                </div>

                                <!-- Register Link -->
                                @if (Route::has('register'))
                                    <div class="text-center pt-4 animate-slide-up" style="animation-delay: 0.5s;">
                                        <p class="text-sm text-gray-600">
                                            ¿No tienes una cuenta?
                                            <a 
                                                href="{{ route('register') }}" 
                                                class="font-bold text-coffee-600 hover:text-coffee-700 transition-colors duration-200 inline-flex items-center group"
                                            >
                                                Regístrate aquí
                                                <i class="fas fa-arrow-right ml-1 text-xs group-hover:translate-x-1 transition-transform"></i>
                                            </a>
                                        </p>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes float-delayed {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-25px) rotate(-5deg);
            }
        }

        @keyframes float-slow {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-15px) rotate(3deg);
            }
        }

        @keyframes float-delayed-slow {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-18px) rotate(-3deg);
            }
        }

        @keyframes bounce-in {
            0% {
                opacity: 0;
                transform: scale(0.3) translateY(-50px);
            }
            50% {
                transform: scale(1.05) translateY(0);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slide-in-left {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slide-in-right {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes ping-slow {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            75%, 100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 0.3;
            }
            50% {
                opacity: 0.5;
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-delayed {
            animation: float-delayed 8s ease-in-out infinite;
            animation-delay: 1s;
        }

        .animate-float-slow {
            animation: float-slow 10s ease-in-out infinite;
            animation-delay: 2s;
        }

        .animate-float-delayed-slow {
            animation: float-delayed-slow 7s ease-in-out infinite;
            animation-delay: 1.5s;
        }

        .animate-bounce-in {
            animation: bounce-in 0.8s ease-out;
        }

        .animate-slide-in-left {
            animation: slide-in-left 0.8s ease-out;
        }

        .animate-slide-in-right {
            animation: slide-in-right 0.8s ease-out;
        }

        .animate-slide-up {
            animation: slide-up 0.6s ease-out both;
        }

        .animate-fade-in {
            animation: fade-in 0.8s ease-out;
        }

        .animate-fade-in-delayed {
            animation: fade-in-up 0.8s ease-out;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }

        .animate-fade-in-delayed-2 {
            animation: fade-in-up 0.8s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out;
        }

        .animate-ping-slow {
            animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .animate-pulse-slow-delayed {
            animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            animation-delay: 2s;
        }

        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }
    </style>

    <script>
        // Toggle password visibility with smooth animation
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Smooth icon transition
                    eyeIcon.style.transform = 'scale(0) rotate(180deg)';
                    setTimeout(() => {
                        if (type === 'password') {
                            eyeIcon.classList.remove('fa-eye-slash');
                            eyeIcon.classList.add('fa-eye');
                        } else {
                            eyeIcon.classList.remove('fa-eye');
                            eyeIcon.classList.add('fa-eye-slash');
                        }
                        eyeIcon.style.transform = 'scale(1) rotate(0deg)';
                    }, 150);
                });
            }

            // Add focus animations to inputs
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-coffee-500/50');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-coffee-500/50');
                });
            });

            // Add stagger animation to form elements
            const formElements = document.querySelectorAll('.animate-slide-up');
            formElements.forEach((el, index) => {
                el.style.animationDelay = `${(index + 1) * 0.1}s`;
            });
        });
    </script>
</x-guest-layout>
