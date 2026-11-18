<x-guest-layout>
    <div class="login-page relative min-h-[100svh] overflow-x-hidden bg-gradient-to-b from-coffee-50 via-white to-coffee-50">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="login-orb absolute -right-24 -top-24 h-72 w-72 rounded-full bg-coffee-200/40 blur-3xl"></div>
            <div class="login-orb login-orb-delayed absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-coffee-100/60 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-[100svh] w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
            <div class="login-enter mb-5 flex items-center justify-between gap-3 sm:mb-8" style="--login-delay: 0ms">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-coffee-800 transition hover:text-coffee-600">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-coffee-100 transition group-hover:shadow-md">
                        <svg class="h-5 w-5 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                            <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="hidden sm:inline">AgroCafé</span>
                </a>

                <a
                    href="{{ url('/') }}"
                    class="group inline-flex items-center gap-2 rounded-full border border-coffee-200 bg-white px-4 py-2 text-sm font-semibold text-coffee-800 shadow-sm transition hover:border-coffee-300 hover:bg-coffee-50 hover:shadow-md"
                >
                    <svg class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 12H5" />
                        <path d="M12 19l-7-7 7-7" />
                    </svg>
                    Volver al inicio
                </a>
            </div>

            <div class="grid flex-1 items-center gap-6 lg:grid-cols-2 lg:gap-14">
                <section class="login-enter hidden text-center lg:block lg:text-left" style="--login-delay: 80ms">
                    <div class="mb-8 inline-flex items-center justify-center">
                        <div class="login-logo flex h-28 w-28 items-center justify-center rounded-3xl border border-coffee-100 bg-white shadow-xl transition hover:-translate-y-1 hover:shadow-2xl">
                            <svg class="h-16 w-16 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                                <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-5xl font-extrabold tracking-tight text-gray-900 xl:text-6xl">
                        Bienvenido a
                        <span class="block bg-gradient-to-r from-coffee-600 to-coffee-800 bg-clip-text text-transparent">AgroCafé</span>
                    </h1>
                    <p class="mt-4 max-w-md text-lg text-gray-600">
                        Sistema de gestión integral para la compra y venta de café.
                    </p>

                    <ul class="mt-8 space-y-4 text-gray-700">
                        <li class="login-feature flex items-center gap-3" style="--login-delay: 180ms">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coffee-100 text-coffee-600 transition group-hover:bg-coffee-200">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <span class="font-medium">Gestión de compras y ventas</span>
                        </li>
                        <li class="login-feature flex items-center gap-3" style="--login-delay: 260ms">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coffee-100 text-coffee-600">
                                <i class="fas fa-warehouse"></i>
                            </span>
                            <span class="font-medium">Control de inventario en tiempo real</span>
                        </li>
                        <li class="login-feature flex items-center gap-3" style="--login-delay: 340ms">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coffee-100 text-coffee-600">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <span class="font-medium">Análisis de precios y reportes</span>
                        </li>
                    </ul>
                </section>

                <section class="mx-auto w-full max-w-md lg:max-w-none">
                    <div class="login-enter mb-5 text-center lg:hidden" style="--login-delay: 60ms">
                        <div class="login-logo mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-coffee-100 bg-white shadow-lg">
                            <svg class="h-9 w-9 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                                <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                            Bienvenido a <span class="text-coffee-700">AgroCafé</span>
                        </h1>
                        <p class="mt-2 text-sm text-gray-600">Entra para gestionar tu negocio cafetero</p>
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <div class="login-card login-enter rounded-3xl border border-coffee-100 bg-white p-5 shadow-xl sm:p-8" style="--login-delay: 120ms">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Iniciar sesión</h2>
                            <p class="mt-1 text-sm text-gray-600 sm:text-base">Ingresa tus credenciales para acceder</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div class="login-field space-y-2" style="--login-delay: 180ms">
                                <label for="email" class="block text-sm font-semibold text-gray-700">
                                    Correo electrónico
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-envelope text-coffee-500/80"></i>
                                    </div>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email') }}"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-4 text-gray-900 placeholder-gray-400 transition duration-200 focus:border-coffee-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-coffee-500/15"
                                        placeholder="correo@ejemplo.com"
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            <div class="login-field space-y-2" style="--login-delay: 240ms">
                                <label for="password" class="block text-sm font-semibold text-gray-700">
                                    Contraseña
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-lock text-coffee-500/80"></i>
                                    </div>
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        required
                                        autocomplete="current-password"
                                        class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-12 text-gray-900 placeholder-gray-400 transition duration-200 focus:border-coffee-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-coffee-500/15"
                                        placeholder="••••••••"
                                    />
                                    <button
                                        type="button"
                                        id="togglePassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 transition hover:scale-110 hover:text-coffee-600"
                                        aria-label="Mostrar u ocultar contraseña"
                                    >
                                        <i class="fas fa-eye transition-transform duration-200" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>

                            <div class="login-field flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" style="--login-delay: 300ms">
                                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        id="remember_me"
                                        name="remember"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-coffee-600 focus:ring-coffee-500"
                                    >
                                    Recordarme
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-coffee-700 transition hover:text-coffee-800">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>

                            <div class="login-field" style="--login-delay: 360ms">
                                <button
                                    type="submit"
                                    class="login-submit inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-coffee-700 px-5 py-3.5 text-base font-bold text-white shadow-lg shadow-coffee-700/20 transition duration-200 hover:-translate-y-0.5 hover:bg-coffee-800 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-coffee-500/30 active:translate-y-0"
                                >
                                    <i class="fas fa-sign-in-alt transition-transform duration-200 group-hover:translate-x-0.5"></i>
                                    Iniciar sesión
                                </button>
                            </div>

                            @if (Route::has('register'))
                                <p class="login-field pt-1 text-center text-sm text-gray-600" style="--login-delay: 420ms">
                                    ¿No tienes una cuenta?
                                    <a href="{{ route('register') }}" class="font-bold text-coffee-700 transition hover:text-coffee-800">
                                        Regístrate aquí
                                    </a>
                                </p>
                            @endif
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <style>
        .login-enter,
        .login-field,
        .login-feature {
            opacity: 0;
            transform: translateY(16px);
            animation: loginRise 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            animation-delay: var(--login-delay, 0ms);
        }

        .login-card {
            transform-origin: center top;
        }

        .login-orb {
            animation: loginOrb 10s ease-in-out infinite;
        }

        .login-orb-delayed {
            animation-delay: -4s;
        }

        .login-logo {
            animation: loginLogo 4.5s ease-in-out infinite;
        }

        @keyframes loginRise {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes loginOrb {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.7;
            }
            50% {
                transform: translate3d(0, -18px, 0) scale(1.05);
                opacity: 1;
            }
        }

        @keyframes loginLogo {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-4px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-enter,
            .login-field,
            .login-feature,
            .login-orb,
            .login-logo {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .login-submit:hover {
                transform: none;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (!togglePassword || !passwordInput || !eyeIcon) {
                return;
            }

            togglePassword.addEventListener('click', function () {
                const isHidden = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
                eyeIcon.classList.toggle('fa-eye', !isHidden);
                eyeIcon.classList.toggle('fa-eye-slash', isHidden);
                eyeIcon.classList.add('scale-90');
                window.setTimeout(function () {
                    eyeIcon.classList.remove('scale-90');
                }, 120);
            });
        });
    </script>
</x-guest-layout>
