<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AgroCafé - Campesino')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Tailwind CSS CDN (respaldo) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto z-30 shadow-sm">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8 pb-6 border-b border-gray-200">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Grano de café - forma ovalada -->
                        <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                        <!-- Línea central característica del grano -->
                        <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <!-- Sombra para dar profundidad -->
                        <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                    </svg>
                    <h1 class="text-xl font-bold text-gray-800">AgroCafé</h1>
                </div>

                <nav class="space-y-1">
                    <a href="{{ route('peasant.dashboard') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('peasant.dashboard') ? 'bg-coffee-50 text-coffee-700 border-l-4 border-coffee-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-home w-5 text-coffee-600"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('peasant.purchases.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('peasant.purchases.*') ? 'bg-coffee-50 text-coffee-700 border-l-4 border-coffee-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-history w-5 text-coffee-600"></i>
                        <span class="font-medium">Mis Compras</span>
                    </a>
                    
                    <a href="{{ route('peasant.invoices.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('peasant.invoices.*') ? 'bg-coffee-50 text-coffee-700 border-l-4 border-coffee-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-file-invoice w-5 text-coffee-600"></i>
                        <span class="font-medium">Mis Facturas</span>
                    </a>

                    <a href="{{ route('peasant.price-analysis.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('peasant.price-analysis.*') ? 'bg-coffee-50 text-coffee-700 border-l-4 border-coffee-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-chart-line w-5 text-coffee-600"></i>
                        <span class="font-medium">Análisis de Precios</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="bg-gradient-to-r from-coffee-100 to-coffee-200 shadow-sm border-b border-coffee-300 sticky top-0 z-20">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-coffee-900">@yield('page-title', 'Dashboard')</h2>
                    
                    <!-- Menú de Usuario -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-coffee-300/50 transition-colors focus:outline-none">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-10 h-10 rounded-full object-cover border-2 border-coffee-600">
                            @else
                                <div class="w-10 h-10 bg-coffee-600 rounded-full flex items-center justify-center border-2 border-coffee-700">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                            @endif
                            <div class="text-left">
                                <p class="text-sm font-semibold text-coffee-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-coffee-700">Campesino</p>
                            </div>
                            <i class="fas fa-chevron-down text-coffee-700 text-xs transition-transform" 
                               :class="{ 'rotate-180': open }"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user-circle text-coffee-600 w-5"></i>
                                <span class="text-sm font-medium">Mi Perfil</span>
                            </a>
                            
                            <div class="border-t border-gray-200 my-2"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span class="text-sm font-medium">Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg alert-message" data-type="success">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg alert-message" data-type="error">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <p class="text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg alert-message" data-type="info">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                            <p class="text-blue-800">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        // Ocultar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>

