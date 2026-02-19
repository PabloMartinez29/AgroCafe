<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AgroCafé - Sistema de Gestión de Café</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    
            <style>
        [x-cloak] { display: none !important; }
        .video-overlay {
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5));
        }
        .hero-background {
            background: linear-gradient(135deg, #7a5f47 0%, #5a4535 50%, #3d2f24 100%);
        }
        #heroVideo {
            background: linear-gradient(135deg, #7a5f47 0%, #5a4535 50%, #3d2f24 100%);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        .btn-login {
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background-color: #7a5f47 !important;
            color: #f5e6d3 !important;
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-register {
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            background-color: #5a4535 !important;
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
            </style>
    </head>
<body class="font-sans antialiased">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-md transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <svg class="w-10 h-10 text-coffee-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                        <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#f3f4f6" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <ellipse cx="12" cy="14" rx="4" ry="5" fill="#f3f4f6" opacity="0.3"/>
                    </svg>
                    <span class="text-2xl font-bold text-coffee-600">AgroCafé</span>
                </div>

                <!-- Menú Desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#acerca-de" class="text-gray-700 hover:text-coffee-600 transition-colors font-medium">Acerca de</a>
                    <a href="#modulos" class="text-gray-700 hover:text-coffee-600 transition-colors font-medium">Módulos</a>
                    <a href="#caracteristicas" class="text-gray-700 hover:text-coffee-600 transition-colors font-medium">Características</a>
                    <a href="{{ route('login') }}" class="btn-login px-6 py-2 border-2 border-coffee-600 text-coffee-600 rounded-lg font-medium">
                        Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-register px-6 py-2 bg-coffee-600 text-white rounded-lg font-medium shadow-md">
                            Registrarse
                        </a>
                    @endif
                </div>

                <!-- Botón Menú Móvil -->
                <button class="md:hidden text-gray-700 hover:text-coffee-600 transition-colors" id="mobileMenuBtn">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Menú Móvil -->
        <div class="hidden md:hidden bg-white border-t border-gray-200" id="mobileMenu">
            <div class="px-4 py-4 space-y-3">
                <a href="#acerca-de" class="block text-gray-700 hover:text-coffee-600 transition-colors font-medium py-2">Acerca de</a>
                <a href="#modulos" class="block text-gray-700 hover:text-coffee-600 transition-colors font-medium py-2">Módulos</a>
                <a href="#caracteristicas" class="block text-gray-700 hover:text-coffee-600 transition-colors font-medium py-2">Características</a>
                <a href="{{ route('login') }}" class="btn-login block px-6 py-2 border-2 border-coffee-600 text-coffee-600 rounded-lg font-medium text-center">
                    Iniciar Sesión
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-register block px-6 py-2 bg-coffee-600 text-white rounded-lg font-medium text-center shadow-md">
                        Registrarse
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section con Video de Fondo -->
    <section class="relative h-screen flex items-center justify-center overflow-hidden pt-20">
        <!-- Video de Fondo -->
        <div class="absolute inset-0 z-0">
            <video 
                autoplay 
                muted 
                loop 
                playsinline
                volume="0"
                class="w-full h-full object-cover"
                id="heroVideo"
                preload="auto">
                <!-- Video sin sonido - Colocado en: public/video/coffee-hero.mp4 -->
                <source src="{{ asset('video/coffee-hero.mp4') }}" type="video/mp4">
                <!-- Opcional: formato WebM para mejor compatibilidad -->
                <!-- <source src="{{ asset('video/coffee-hero.webm') }}" type="video/webm"> -->
            </video>
            <!-- Fondo de respaldo (se muestra si no hay video) -->
            <div class="absolute inset-0 hero-background" id="videoFallback" style="display: none;"></div>
            <!-- Overlay con gradiente para mejor legibilidad -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/60"></div>
        </div>

        <!-- Contenido sobre el video -->
        <div class="relative z-10 text-center px-4">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 drop-shadow-lg fade-in-up" style="animation-delay: 0.2s;">
                    <span id="typing-text"></span><span id="typing-cursor" class="inline-block w-1 h-16 bg-white ml-2 animate-pulse"></span>
                </h1>
                <p class="text-xl md:text-2xl text-white mb-8 drop-shadow-md fade-in-up" style="animation-delay: 0.4s;">
                    <span id="typing-subtitle"></span><span id="typing-subtitle-cursor" class="inline-block w-0.5 h-6 bg-white ml-1 animate-pulse"></span>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center fade-in-up" style="animation-delay: 0.6s;">
                    <a href="{{ route('login') }}" 
                       class="px-8 py-4 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-all transform hover:scale-105 shadow-xl font-semibold text-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="px-8 py-4 bg-white text-coffee-600 rounded-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl font-semibold text-lg">
                            <i class="fas fa-user-plus mr-2"></i>
                            Registrarse
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <script>
            // Asegurar que el video se reproduzca y esté silenciado
            document.addEventListener('DOMContentLoaded', function() {
                const video = document.getElementById('heroVideo');
                const fallback = document.getElementById('videoFallback');
                
                if (video) {
                    // Asegurar que el video esté completamente silenciado
                    video.muted = true;
                    video.volume = 0;
                    video.setAttribute('muted', 'muted');
                    
                    // Intentar reproducir el video
                    video.play().then(function() {
                        // Video se reproduce correctamente
                        if (fallback) {
                            fallback.style.display = 'none';
                        }
                    }).catch(function(error) {
                        // Si hay error, mostrar el fondo de respaldo
                        console.log('Error al reproducir el video:', error);
                        if (fallback) {
                            fallback.style.display = 'block';
                        }
                    });
                    
                    // Si hay error al cargar el video
                    video.addEventListener('error', function() {
                        console.log('Error al cargar el video');
                        if (fallback) {
                            fallback.style.display = 'block';
                        }
                    });
                }
            });
        </script>
    </section>

    <!-- Sección Acerca de -->
    <section id="acerca-de" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    <span class="text-coffee-600">Acerca de</span> AgroCafé
                </h2>
                <div class="w-24 h-1 bg-coffee-600 mx-auto mb-6"></div>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Sistema integral diseñado para transformar la gestión del negocio cafetero
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-coffee-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-seedling text-coffee-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Misión</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Facilitar la gestión integral de la cadena de valor del café, conectando campesinos, 
                                cooperativas y comercializadores mediante tecnología innovadora y accesible.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-coffee-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-eye text-coffee-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Visión</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Ser la plataforma líder en gestión cafetera en Colombia, impulsando la digitalización 
                                del sector y mejorando la rentabilidad de todos los actores de la cadena productiva.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-coffee-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heart text-coffee-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Valores</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Transparencia, eficiencia, innovación y compromiso con el desarrollo sostenible 
                                del sector cafetero colombiano.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="bg-gradient-to-br from-coffee-50 to-coffee-100 rounded-2xl p-8 shadow-xl">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center bg-white rounded-xl p-6 shadow-md">
                                <div class="text-4xl font-bold text-coffee-600 mb-2">100%</div>
                                <div class="text-sm text-gray-600">Digital</div>
                            </div>
                            <div class="text-center bg-white rounded-xl p-6 shadow-md">
                                <div class="text-4xl font-bold text-coffee-600 mb-2">24/7</div>
                                <div class="text-sm text-gray-600">Disponible</div>
                            </div>
                            <div class="text-center bg-white rounded-xl p-6 shadow-md">
                                <div class="text-4xl font-bold text-coffee-600 mb-2">100%</div>
                                <div class="text-sm text-gray-600">Seguro</div>
                            </div>
                            <div class="text-center bg-white rounded-xl p-6 shadow-md">
                                <div class="text-4xl font-bold text-coffee-600 mb-2">API</div>
                                <div class="text-sm text-gray-600">Integrable</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Módulos Principales -->
    <section id="modulos" class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Módulos Principales</h2>
                <div class="w-24 h-1 bg-coffee-600 mx-auto mb-6"></div>
                <p class="text-xl text-gray-600">Sistema completo con todas las herramientas que necesitas</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Módulo 1: Gestión de Compras -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-shopping-cart text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestión de Compras</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Registra y gestiona todas las compras de café a campesinos. Control total sobre precios, 
                        cantidades y estados de transacción.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Registro detallado de compras</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Control de pagos a campesinos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Generación automática de facturas</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Historial completo de transacciones</span>
                        </li>
                    </ul>
                </div>

                <!-- Módulo 2: Gestión de Ventas -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-handshake text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestión de Ventas</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Administra las ventas a cooperativas y clientes. Control completo de inventario 
                        y seguimiento de pagos en tiempo real.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            <span>Ventas a cooperativas</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            <span>Control de inventario en tiempo real</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            <span>Reportes y estadísticas avanzadas</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            <span>Seguimiento de pagos pendientes</span>
                        </li>
                    </ul>
                </div>

                <!-- Módulo 3: Control de Inventario -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-coffee-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-warehouse text-coffee-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Control de Inventario</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Gestiona tu bodega de manera inteligente. Control total sobre entradas, salidas, 
                        ajustes y devoluciones.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-coffee-500 mr-3"></i>
                            <span>Inventario en tiempo real</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-coffee-500 mr-3"></i>
                            <span>Movimientos de bodega detallados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-coffee-500 mr-3"></i>
                            <span>Reportes de stock automáticos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-coffee-500 mr-3"></i>
                            <span>Alertas de stock bajo</span>
                        </li>
                    </ul>
                </div>

                <!-- Módulo 4: Análisis de Precios -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Análisis de Precios</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Monitorea los precios del café en tiempo real. Gráficas comparativas y análisis 
                        histórico de precios por tipo de café.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                            <span>Gráficas en tiempo real</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                            <span>Análisis histórico de precios</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                            <span>Comparación entre tipos de café</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                            <span>Estadísticas y tendencias</span>
                        </li>
                    </ul>
                </div>

                <!-- Módulo 5: Gestión de Usuarios -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-users text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestión de Usuarios</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Administra usuarios, roles y permisos. Control de acceso granular para garantizar 
                        la seguridad de tu información.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-indigo-500 mr-3"></i>
                            <span>Roles y permisos personalizados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-indigo-500 mr-3"></i>
                            <span>Control de acceso por módulo</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-indigo-500 mr-3"></i>
                            <span>Gestión de campesinos y cooperativas</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-indigo-500 mr-3"></i>
                            <span>Historial de actividades</span>
                        </li>
                    </ul>
                </div>

                <!-- Módulo 6: Reportes y Estadísticas -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-amber-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Reportes y Estadísticas</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Genera reportes detallados y visualiza estadísticas completas de tu negocio. 
                        Toma decisiones basadas en datos.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-amber-500 mr-3"></i>
                            <span>Reportes personalizables</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-amber-500 mr-3"></i>
                            <span>Dashboard interactivo</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-amber-500 mr-3"></i>
                            <span>Exportación a PDF y Excel</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-amber-500 mr-3"></i>
                            <span>Análisis de rentabilidad</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Características Destacadas - Carrusel -->
    <section id="caracteristicas" class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Características Destacadas</h2>
                <div class="w-24 h-1 bg-coffee-600 mx-auto mb-6"></div>
                <p class="text-xl text-gray-600">Tecnología y funcionalidades que hacen la diferencia</p>
            </div>

            <!-- Carrusel -->
            <div class="relative" x-data="{ currentSlide: 0, slides: 6 }">
                <!-- Slides -->
                <div class="overflow-hidden rounded-xl">
                    <div class="flex transition-transform duration-500 ease-in-out" 
                         :style="`transform: translateX(-${currentSlide * 100}%)`">
                        
                        <!-- Slide 1: Interfaz Intuitiva -->
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-coffee-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-mouse-pointer text-coffee-600 text-3xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Interfaz Intuitiva</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        Diseño moderno y fácil de usar. Navega por el sistema sin complicaciones, 
                                        con una experiencia de usuario optimizada para máxima productividad.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Seguridad Avanzada -->
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-coffee-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-shield-alt text-coffee-600 text-3xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Seguridad Avanzada</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        Protección de datos de nivel empresarial. Sistema robusto con encriptación, 
                                        control de acceso granular y respaldo automático de información.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: Acceso Multiplataforma -->
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-coffee-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-mobile-alt text-coffee-600 text-3xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Acceso Multiplataforma</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        Accede desde cualquier dispositivo. Sistema web responsive y API REST para 
                                        integración con aplicaciones móviles Flutter.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 4: Tiempo Real -->
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-coffee-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-sync-alt text-coffee-600 text-3xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Tiempo Real</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        Datos actualizados al instante. Gráficas, reportes e inventario se actualizan 
                                        automáticamente sin necesidad de recargar la página.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 5: Soporte Técnico -->
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-coffee-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-headset text-coffee-600 text-3xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Soporte Técnico</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        Equipo de soporte dedicado. Asistencia técnica profesional para resolver 
                                        cualquier duda o inconveniente de manera rápida y eficiente.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 6: Escalabilidad -->
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-coffee-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-chart-line text-coffee-600 text-3xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Escalabilidad</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        Crece con tu negocio. Sistema diseñado para adaptarse desde pequeños 
                                        productores hasta grandes cooperativas cafeteras.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controles del Carrusel -->
                <div class="flex items-center justify-center mt-8 space-x-4">
                    <button 
                        @click="currentSlide = (currentSlide - 1 + slides) % slides"
                        class="w-12 h-12 bg-coffee-600 text-white rounded-full hover:bg-coffee-700 transition-colors flex items-center justify-center shadow-lg">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <!-- Indicadores -->
                    <div class="flex space-x-2">
                        <button 
                            @click="currentSlide = 0"
                            class="w-3 h-3 rounded-full transition-all"
                            :class="currentSlide === 0 ? 'bg-coffee-600 w-8' : 'bg-gray-300'">
                        </button>
                        <button 
                            @click="currentSlide = 1"
                            class="w-3 h-3 rounded-full transition-all"
                            :class="currentSlide === 1 ? 'bg-coffee-600 w-8' : 'bg-gray-300'">
                        </button>
                        <button 
                            @click="currentSlide = 2"
                            class="w-3 h-3 rounded-full transition-all"
                            :class="currentSlide === 2 ? 'bg-coffee-600 w-8' : 'bg-gray-300'">
                        </button>
                        <button 
                            @click="currentSlide = 3"
                            class="w-3 h-3 rounded-full transition-all"
                            :class="currentSlide === 3 ? 'bg-coffee-600 w-8' : 'bg-gray-300'">
                        </button>
                        <button 
                            @click="currentSlide = 4"
                            class="w-3 h-3 rounded-full transition-all"
                            :class="currentSlide === 4 ? 'bg-coffee-600 w-8' : 'bg-gray-300'">
                        </button>
                        <button 
                            @click="currentSlide = 5"
                            class="w-3 h-3 rounded-full transition-all"
                            :class="currentSlide === 5 ? 'bg-coffee-600 w-8' : 'bg-gray-300'">
                        </button>
                    </div>

                    <button 
                        @click="currentSlide = (currentSlide + 1) % slides"
                        class="w-12 h-12 bg-coffee-600 text-white rounded-full hover:bg-coffee-700 transition-colors flex items-center justify-center shadow-lg">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Beneficios -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aplicación Móvil</h3>
                    <p class="text-gray-600">Accede desde cualquier dispositivo con nuestra API para Flutter</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Reportes en Tiempo Real</h3>
                    <p class="text-gray-600">Visualiza estadísticas y reportes actualizados al instante</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Seguro y Confiable</h3>
                    <p class="text-gray-600">Sistema robusto con control de acceso y respaldo de datos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Profesional - Colores Café y Blanco -->
    <footer class="bg-gradient-to-b from-coffee-800 via-coffee-700 to-coffee-900 text-white">
        <!-- Sección Principal del Footer -->
        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- Información de la Empresa -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                            <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#f3f4f6" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            <ellipse cx="12" cy="14" rx="4" ry="5" fill="#f3f4f6" opacity="0.3"/>
                        </svg>
                        <h3 class="text-2xl font-bold text-white">AgroCafé</h3>
                    </div>
                    <p class="text-white/90 mb-6 leading-relaxed">
                        Sistema de gestión integral para la compra y venta de café. 
                        Transformamos la gestión cafetera con tecnología de vanguardia.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://facebook.com" 
                           target="_blank"
                           class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all transform hover:scale-110"
                           title="Facebook">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="https://instagram.com" 
                           target="_blank"
                           class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all transform hover:scale-110"
                           title="Instagram">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="https://twitter.com" 
                           target="_blank"
                           class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all transform hover:scale-110"
                           title="Twitter">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="https://linkedin.com" 
                           target="_blank"
                           class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-all transform hover:scale-110"
                           title="LinkedIn">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Enlaces Rápidos -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6 pb-2 border-b border-white/20">Enlaces Rápidos</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('login') }}" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Iniciar Sesión
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li>
                                <a href="{{ route('register') }}" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                    <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                                    Registrarse
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#acerca-de" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Acerca de
                            </a>
                        </li>
                        <li>
                            <a href="#modulos" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Módulos
                            </a>
                        </li>
                        <li>
                            <a href="#caracteristicas" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Características
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Módulos -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6 pb-2 border-b border-white/20">Módulos</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="#modulos" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-shopping-cart text-xs mr-2"></i>
                                Compras
                            </a>
                        </li>
                        <li>
                            <a href="#modulos" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-handshake text-xs mr-2"></i>
                                Ventas
                            </a>
                        </li>
                        <li>
                            <a href="#modulos" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-warehouse text-xs mr-2"></i>
                                Inventario
                            </a>
                        </li>
                        <li>
                            <a href="#modulos" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chart-line text-xs mr-2"></i>
                                Análisis de Precios
                            </a>
                        </li>
                        <li>
                            <a href="#modulos" class="text-white/90 hover:text-white transition-colors flex items-center group">
                                <i class="fas fa-chart-bar text-xs mr-2"></i>
                                Reportes
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contacto -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6 pb-2 border-b border-white/20">Contacto</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-white mr-3 mt-1"></i>
                            <div>
                                <p class="text-white/70 text-sm">Email</p>
                                <a href="mailto:contacto@agrocafe.com" class="text-white hover:text-white/80 transition-colors">
                                    contacto@agrocafe.com
                                </a>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone text-white mr-3 mt-1"></i>
                            <div>
                                <p class="text-white/70 text-sm">Teléfono</p>
                                <a href="tel:+573001234567" class="text-white hover:text-white/80 transition-colors">
                                    +57 300 123 4567
                                </a>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-white mr-3 mt-1"></i>
                            <div>
                                <p class="text-white/70 text-sm">Ubicación</p>
                                <p class="text-white">Colombia</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock text-white mr-3 mt-1"></i>
                            <div>
                                <p class="text-white/70 text-sm">Horario</p>
                                <p class="text-white">Lun - Vie: 8:00 AM - 6:00 PM</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Barra Inferior -->
        <div class="border-t border-white/20">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <p class="text-white/90 text-sm mb-4 md:mb-0">
                        © {{ date('Y') }} <span class="text-white font-semibold">AgroCafé</span>. Todos los derechos reservados.
                    </p>
                    <div class="flex flex-wrap items-center gap-6 text-sm text-white/90">
                        <a href="#" class="hover:text-white transition-colors">Política de Privacidad</a>
                        <span class="text-white/50">|</span>
                        <a href="#" class="hover:text-white transition-colors">Términos de Servicio</a>
                        <span class="text-white/50">|</span>
                        <a href="#" class="hover:text-white transition-colors">Soporte</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Animación de escritura (Typing Effect) para "AgroCafe" y subtítulo
        document.addEventListener('DOMContentLoaded', function() {
            // Efecto de typing para "AgroCafé"
            const typingElement = document.getElementById('typing-text');
            const cursorElement = document.getElementById('typing-cursor');
            
            if (typingElement && cursorElement) {
                const text = 'AgroCafé';
                let index = 0;
                
                function typeText() {
                    if (index < text.length) {
                        typingElement.textContent += text.charAt(index);
                        index++;
                        setTimeout(typeText, 150); // Velocidad de escritura
                    } else {
                        // Una vez completado, iniciar el subtítulo
                        setTimeout(startSubtitle, 500);
                    }
                }
                
                // Iniciar la animación después de un pequeño delay
                setTimeout(typeText, 500);
            }
            
            // Efecto de typing para el subtítulo
            function startSubtitle() {
                const subtitleElement = document.getElementById('typing-subtitle');
                const subtitleCursor = document.getElementById('typing-subtitle-cursor');
                
                if (subtitleElement && subtitleCursor) {
                    const subtitleText = 'Sistema de Gestión Integral para la Compra y Venta de Café';
                    let subtitleIndex = 0;
                    
                    function typeSubtitle() {
                        if (subtitleIndex < subtitleText.length) {
                            subtitleElement.textContent += subtitleText.charAt(subtitleIndex);
                            subtitleIndex++;
                            setTimeout(typeSubtitle, 80); // Velocidad más rápida para el subtítulo
                        } else {
                            // Una vez completado, el cursor sigue parpadeando automáticamente
                            subtitleCursor.style.animation = 'pulse 1s infinite';
                        }
                    }
                    
                    // Iniciar el subtítulo
                    typeSubtitle();
                }
            }
            
            // Navbar scroll effect
            const navbar = document.getElementById('navbar');
            if (navbar) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('shadow-lg');
                        navbar.classList.remove('bg-white/95');
                        navbar.classList.add('bg-white');
                    } else {
                        navbar.classList.remove('shadow-lg');
                        navbar.classList.add('bg-white/95');
                        navbar.classList.remove('bg-white');
                    }
                });
            }
            
            // Menú móvil toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    const icon = mobileMenuBtn.querySelector('i');
                    if (icon) {
                        if (mobileMenu.classList.contains('hidden')) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        } else {
                            icon.classList.remove('fa-bars');
                            icon.classList.add('fa-times');
                        }
                    }
                });
                
                // Cerrar menú al hacer clic en un enlace
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuBtn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        }
                    });
                });
            }
            
            // Smooth scroll para todos los enlaces de anclaje
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href !== '') {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            const offsetTop = target.offsetTop - 80; // Ajuste para el navbar fijo
                            window.scrollTo({
                                top: offsetTop,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
            
            // Auto-play del carrusel de características
            setInterval(() => {
                if (window.Alpine) {
                    const carouselElement = document.querySelector('[x-data*="currentSlide"]');
                    if (carouselElement) {
                        const data = Alpine.$data(carouselElement);
                        if (data && data.slides) {
                            data.currentSlide = (data.currentSlide + 1) % data.slides;
                        }
                    }
                }
            }, 5000);
        });
    </script>
    </body>
</html>
