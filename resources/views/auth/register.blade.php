<x-guest-layout>
    <div class="register-page relative min-h-[100svh] overflow-x-hidden bg-gradient-to-b from-coffee-50 via-white to-coffee-50">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="register-orb absolute -right-24 -top-24 h-72 w-72 rounded-full bg-coffee-200/40 blur-3xl"></div>
            <div class="register-orb register-orb-delayed absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-coffee-100/60 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-[100svh] w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
            <div class="register-enter mb-5 flex items-center justify-between gap-3 sm:mb-8" style="--register-delay: 0ms">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-coffee-800 transition hover:text-coffee-600">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-coffee-100">
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

            <div class="grid flex-1 items-start gap-6 pb-8 lg:grid-cols-2 lg:items-center lg:gap-14">
                <section class="register-enter hidden text-center lg:block lg:text-left" style="--register-delay: 80ms">
                    <div class="mb-8 inline-flex items-center justify-center">
                        <div class="register-logo flex h-28 w-28 items-center justify-center rounded-3xl border border-coffee-100 bg-white shadow-xl">
                            <svg class="h-16 w-16 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                                <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-5xl font-extrabold tracking-tight text-gray-900 xl:text-6xl">
                        Únete a
                        <span class="block bg-gradient-to-r from-coffee-600 to-coffee-800 bg-clip-text text-transparent">AgroCafé</span>
                    </h1>
                    <p class="mt-4 max-w-md text-lg text-gray-600">
                        Crea tu cuenta y comienza a gestionar tu negocio cafetero.
                    </p>

                    <ul class="mt-8 space-y-4 text-gray-700">
                        <li class="register-feature flex items-center gap-3" style="--register-delay: 180ms">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coffee-100 text-coffee-600">
                                <i class="fas fa-user-plus"></i>
                            </span>
                            <span class="font-medium">Registro rápido y sencillo</span>
                        </li>
                        <li class="register-feature flex items-center gap-3" style="--register-delay: 260ms">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coffee-100 text-coffee-600">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <span class="font-medium">Datos seguros y protegidos</span>
                        </li>
                        <li class="register-feature flex items-center gap-3" style="--register-delay: 340ms">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coffee-100 text-coffee-600">
                                <i class="fas fa-rocket"></i>
                            </span>
                            <span class="font-medium">Acceso inmediato al sistema</span>
                        </li>
                    </ul>
                </section>

                <section class="mx-auto w-full max-w-md lg:max-w-none">
                    <div class="register-enter mb-5 text-center lg:hidden" style="--register-delay: 60ms">
                        <div class="register-logo mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-coffee-100 bg-white shadow-lg">
                            <svg class="h-9 w-9 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                                <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                            Únete a <span class="text-coffee-700">AgroCafé</span>
                        </h1>
                        <p class="mt-2 text-sm text-gray-600">Crea tu cuenta y comienza a gestionar tu negocio cafetero</p>
                    </div>

                    <div class="register-card register-enter rounded-3xl border border-coffee-100 bg-white p-5 shadow-xl sm:p-8" style="--register-delay: 120ms">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Crear cuenta</h2>
                            <p class="mt-1 text-sm text-gray-600 sm:text-base">Completa el formulario para registrarte</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="space-y-5">
                            @csrf

                            <div class="register-field space-y-2" style="--register-delay: 160ms">
                                <label for="name" class="block text-sm font-semibold text-gray-700">Nombre completo</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-user text-coffee-500/80"></i>
                                    </div>
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        value="{{ old('name') }}"
                                        required
                                        autofocus
                                        autocomplete="name"
                                        class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-4 text-gray-900 placeholder-gray-400 transition duration-200 focus:border-coffee-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-coffee-500/15"
                                        placeholder="Juan Pérez"
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            <div class="register-field space-y-2" style="--register-delay: 200ms">
                                <label for="email" class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
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
                                        autocomplete="username"
                                        class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-4 text-gray-900 placeholder-gray-400 transition duration-200 focus:border-coffee-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-coffee-500/15"
                                        placeholder="correo@ejemplo.com"
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            <div class="register-field space-y-2" style="--register-delay: 240ms">
                                <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-lock text-coffee-500/80"></i>
                                    </div>
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        required
                                        autocomplete="new-password"
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

                            <div class="register-field space-y-2" style="--register-delay: 280ms">
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirmar contraseña</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-lock text-coffee-500/80"></i>
                                    </div>
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        required
                                        autocomplete="new-password"
                                        class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-12 text-gray-900 placeholder-gray-400 transition duration-200 focus:border-coffee-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-coffee-500/15"
                                        placeholder="••••••••"
                                    />
                                    <button
                                        type="button"
                                        id="togglePasswordConfirmation"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 transition hover:scale-110 hover:text-coffee-600"
                                        aria-label="Mostrar u ocultar confirmación de contraseña"
                                    >
                                        <i class="fas fa-eye transition-transform duration-200" id="eyeIconConfirmation"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                            </div>

                            <div class="register-field" style="--register-delay: 320ms">
                                <x-consent-checkboxes />
                            </div>

                            <div class="register-field" style="--register-delay: 360ms">
                                <button
                                    type="submit"
                                    class="register-submit inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-coffee-700 px-5 py-3.5 text-base font-bold text-white shadow-lg shadow-coffee-700/20 transition duration-200 hover:-translate-y-0.5 hover:bg-coffee-800 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-coffee-500/30 active:translate-y-0"
                                >
                                    <i class="fas fa-user-plus"></i>
                                    Crear cuenta
                                </button>
                            </div>

                            <p class="register-field pt-1 text-center text-sm text-gray-600" style="--register-delay: 400ms">
                                ¿Ya tienes una cuenta?
                                <a href="{{ route('login') }}" class="font-bold text-coffee-700 transition hover:text-coffee-800">
                                    Inicia sesión aquí
                                </a>
                            </p>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <style>
        .register-enter,
        .register-field,
        .register-feature {
            opacity: 0;
            transform: translateY(16px);
            animation: registerRise 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            animation-delay: var(--register-delay, 0ms);
        }

        .register-orb {
            animation: registerOrb 10s ease-in-out infinite;
        }

        .register-orb-delayed {
            animation-delay: -4s;
        }

        .register-logo {
            animation: registerLogo 4.5s ease-in-out infinite;
        }

        @keyframes registerRise {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes registerOrb {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.7;
            }
            50% {
                transform: translate3d(0, -18px, 0) scale(1.05);
                opacity: 1;
            }
        }

        @keyframes registerLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        @media (prefers-reduced-motion: reduce) {
            .register-enter,
            .register-field,
            .register-feature,
            .register-orb,
            .register-logo {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .register-submit:hover {
                transform: none;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bindToggle = function (buttonId, inputId, iconId) {
                const button = document.getElementById(buttonId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (!button || !input || !icon) {
                    return;
                }

                button.addEventListener('click', function () {
                    const isHidden = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isHidden ? 'text' : 'password');
                    icon.classList.toggle('fa-eye', !isHidden);
                    icon.classList.toggle('fa-eye-slash', isHidden);
                });
            };

            bindToggle('togglePassword', 'password', 'eyeIcon');
            bindToggle('togglePasswordConfirmation', 'password_confirmation', 'eyeIconConfirmation');
        });
    </script>
</x-guest-layout>
