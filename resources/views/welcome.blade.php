<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AgroCafé - Sistema de Gestión de Café</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
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
        .hero-background {
            background: linear-gradient(135deg, #7a5f47 0%, #5a4535 50%, #3d2f24 100%);
        }
        #heroVideo,
        #heroPreview {
            background: linear-gradient(135deg, #7a5f47 0%, #5a4535 50%, #3d2f24 100%);
        }
        .hero-shade {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.62) 0%, rgba(0, 0, 0, 0.12) 36%, rgba(0, 0, 0, 0.08) 100%);
        }
        .hero-slide {
            opacity: 0;
            transition: opacity 0.9s ease;
        }
        .hero-slide.is-active,
        .hero-thumb.is-active {
            opacity: 1;
        }
        .hero-thumb {
            opacity: 0;
            transition: opacity 0.6s ease;
        }
        #heroCopy > * {
            grid-area: 1 / 1;
        }
        .hero-phrase {
            align-self: end;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }
        .hero-phrase.is-active {
            opacity: 1;
            pointer-events: auto;
        }
        .hero-word-mask {
            display: inline-block;
            overflow: hidden;
            vertical-align: top;
            padding: 0.14em 0;
            margin: -0.14em 0;
        }
        .hero-word-rise {
            display: inline-block;
        }
        .hero-wipe {
            display: block;
        }
        @keyframes heroRise {
            from {
                transform: translateY(115%);
            }
            to {
                transform: translateY(0);
            }
        }
        @keyframes heroSlideIn {
            from {
                opacity: 0;
                transform: translateX(-70px);
                filter: blur(10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
                filter: blur(0);
            }
        }
        @keyframes heroFadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes heroWipe {
            from {
                clip-path: inset(-0.2em 100% -0.2em 0);
                transform: translateX(-14px);
            }
            to {
                clip-path: inset(-0.2em 0 -0.2em 0);
                transform: translateX(0);
            }
        }
        .hero-phrase.is-active .hero-word-rise {
            animation: heroRise 0.9s cubic-bezier(0.2, 0.8, 0.2, 1) both;
            animation-delay: calc(var(--i) * 90ms + 250ms);
        }
        .hero-phrase.is-active .hero-slide-in {
            animation: heroSlideIn 1s cubic-bezier(0.2, 0.8, 0.2, 1) both;
            animation-delay: 250ms;
        }
        .hero-phrase.is-active .hero-fade-up {
            animation: heroFadeUp 0.8s ease-out both;
            animation-delay: 1000ms;
        }
        .hero-phrase.is-active .hero-wipe {
            animation: heroWipe 0.9s cubic-bezier(0.65, 0, 0.35, 1) both;
            animation-delay: calc(var(--i) * 500ms + 250ms);
        }
        .hero-progress-track {
            height: 3px;
            flex: 1 1 0%;
            transition: flex-grow 0.5s ease;
        }
        .hero-progress-track.is-current {
            flex-grow: 3;
        }
        .hero-word {
            top: 22%;
            z-index: 5;
            margin: 0;
            padding-inline: 1.5vw;
            font-size: clamp(4.5rem, 18vw, 15rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0.08em;
            text-align: center;
            color: rgba(255, 255, 255, 0.78);
            white-space: nowrap;
            user-select: none;
            opacity: 0;
            transition: opacity 0.9s ease;
            text-shadow: 0 2px 40px rgba(0, 0, 0, 0.18);
        }
        .hero-word.is-active {
            opacity: 1;
        }
        .hero-plants {
            z-index: 6;
            -webkit-mask-image:
                radial-gradient(ellipse 30% 22% at 16% 58%, #000 0%, transparent 70%),
                radial-gradient(ellipse 36% 26% at 48% 62%, #000 0%, transparent 72%),
                radial-gradient(ellipse 24% 18% at 76% 56%, #000 0%, transparent 70%),
                radial-gradient(ellipse 20% 18% at 92% 60%, #000 0%, transparent 68%),
                linear-gradient(to bottom, transparent 0%, transparent 34%, #000 48%);
            -webkit-mask-composite: source-over, source-over, source-over, source-over;
            mask-image:
                radial-gradient(ellipse 30% 22% at 16% 58%, #000 0%, transparent 70%),
                radial-gradient(ellipse 36% 26% at 48% 62%, #000 0%, transparent 72%),
                radial-gradient(ellipse 24% 18% at 76% 56%, #000 0%, transparent 70%),
                radial-gradient(ellipse 20% 18% at 92% 60%, #000 0%, transparent 68%),
                linear-gradient(to bottom, transparent 0%, transparent 34%, #000 48%);
            mask-composite: add, add, add, add;
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
        @keyframes priceShine {
            0%, 55% { transform: translateX(-130%) skewX(-18deg); }
            100% { transform: translateX(330%) skewX(-18deg); }
        }
        @keyframes priceFloat {
            0%, 100% { translate: 0 0; }
            50% { translate: 0 -10px; }
        }
        .price-title-clip { overflow: hidden; padding-bottom: 0.15em; }
        .price-bar { transform-origin: left center; }
        .price-row-featured { position: relative; overflow: hidden; }
        .price-row-featured::after {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 30%;
            background: linear-gradient(100deg, transparent, rgba(122, 95, 71, 0.16), transparent);
            pointer-events: none;
            transform: translateX(-130%) skewX(-18deg);
        }
        .price-stage.is-in .price-row-featured::after { animation: priceShine 5s ease-in-out 2.4s infinite; }
        .price-blob { pointer-events: none; will-change: translate; translate: 0 var(--py, 0px); }
        .price-blob > span { display: block; width: 100%; height: 100%; border-radius: 9999px; animation: priceFloat 7s ease-in-out infinite; }
        ::selection {
            background: #7a5f47;
            color: #fff;
        }
        html {
            scrollbar-width: thin;
            scrollbar-color: #7a5f47 #f3eee4;
        }
        @media (prefers-reduced-motion: reduce) {
            .price-row-featured::after,
            .price-blob > span {
                animation: none !important;
            }
            .hero-slide,
            .hero-thumb,
            .hero-word,
            .hero-phrase,
            .hero-progress-track {
                transition: none;
            }
            .hero-phrase.is-active .hero-word-rise,
            .hero-phrase.is-active .hero-slide-in,
            .hero-phrase.is-active .hero-fade-up,
            .hero-phrase.is-active .hero-wipe {
                animation: none;
            }
        }
            </style>
    </head>
<body class="font-sans antialiased bg-black">
    <section id="heroStage" class="sticky top-0 z-0 h-[100svh] min-h-[24rem] overflow-hidden bg-black" data-cycle="image,video,image">
            <img
                id="heroImageOne"
                class="hero-slide is-active pointer-events-none absolute inset-0 h-full w-full object-cover"
                src="{{ asset('images/hero/1.svg') }}"
                alt=""
                aria-hidden="true">
            <video
                muted
                playsinline
                class="hero-slide absolute inset-0 h-full w-full object-cover"
                id="heroVideo"
                preload="auto">
                <source src="{{ asset('video/gemini_generated_video_079d102d.mp4') }}?v=4" type="video/mp4">
            </video>
            <img
                id="heroImageTwo"
                class="hero-slide pointer-events-none absolute inset-0 h-full w-full object-cover"
                src="{{ asset('images/hero/2.svg') }}?v=3"
                alt=""
                aria-hidden="true">
            <div class="absolute inset-0 hero-background" id="videoFallback" style="display: none;"></div>
            <p id="heroMarkAgro" class="hero-word is-active pointer-events-none absolute inset-x-0" aria-hidden="true">AGRO</p>
            <img
                id="heroPlantsOne"
                class="hero-slide hero-plants is-active pointer-events-none absolute inset-0 h-full w-full object-cover"
                src="{{ asset('images/hero/1.svg') }}"
                alt=""
                aria-hidden="true">
            <p id="heroMarkCafe" class="hero-word pointer-events-none absolute inset-x-0" aria-hidden="true">CAFÉ</p>
            <img
                id="heroPlantsTwo"
                class="hero-slide hero-plants pointer-events-none absolute inset-0 h-full w-full object-cover"
                src="{{ asset('images/hero/2.svg') }}?v=3"
                alt=""
                aria-hidden="true">
            <div class="pointer-events-none absolute inset-0 z-[7] hero-shade"></div>

            <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-4 p-5 sm:p-8">
                <a href="{{ url('/') }}" id="heroBrand" class="text-lg font-semibold tracking-tight text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.6)] transition hover:text-white/80 sm:text-xl">
                    AgroCafé
                </a>

                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.65)] transition hover:text-white/80">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-full bg-white px-5 py-2.5 text-sm font-medium text-neutral-900 shadow-sm transition hover:bg-neutral-100">
                            Regístrate
                        </a>
                    @endif
                </div>
            </div>

            <div class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between gap-6 p-5 sm:p-8 lg:p-10">
                <div class="grid max-w-2xl text-white" id="heroCopy">
                    <h1 class="sr-only">AgroCafé, sistema de gestión de café</h1>

                    <div class="hero-phrase is-active" data-phrase-slide="0" data-animation="rise" data-phrase="El arte del café, nacido en nuestra tierra.">
                        <p class="text-4xl font-semibold leading-[1.05] tracking-tight sm:text-5xl lg:text-[3.2rem]">
                            @foreach (['El', 'arte', 'del', 'café,', 'nacido', 'en', 'nuestra', 'tierra.'] as $index => $word)
                                <span class="hero-word-mask"><span class="hero-word-rise" style="--i: {{ $index }}">{{ $word }}</span></span>
                            @endforeach
                        </p>
                    </div>

                    <div class="hero-phrase" data-phrase-slide="1" data-animation="slide" aria-hidden="true">
                        <p class="hero-slide-in text-4xl font-semibold leading-[1.05] tracking-tight sm:text-5xl lg:text-[3.2rem]">
                            La esencia del café colombiano, sin límites.
                        </p>
                        <p class="hero-fade-up mt-4 max-w-md text-sm leading-relaxed text-white/85 sm:text-base">
                            Conectamos el campo con nuevas oportunidades para llevar el valor de cada cosecha más lejos.
                        </p>
                    </div>

                    <div class="hero-phrase" data-phrase-slide="2" data-animation="wipe" aria-hidden="true">
                        <p class="text-4xl font-semibold leading-[1.05] tracking-tight sm:text-5xl lg:text-[3.2rem]">
                            <span class="hero-wipe" style="--i: 0">Tradición que inspira.</span>
                            <span class="hero-wipe" style="--i: 1">Calidad que trasciende.</span>
                        </p>
                    </div>
                </div>

                <div class="hidden w-[13.5rem] shrink-0 sm:block lg:w-[15.5rem]">
                    <div id="heroPreviewFrame" class="relative h-28 overflow-hidden rounded-2xl shadow-2xl ring-1 ring-white/25 lg:h-32">
                        <img
                            id="heroPreviewOne"
                            class="hero-thumb is-active absolute inset-0 h-full w-full object-cover"
                            src="{{ asset('images/hero/1.svg') }}"
                            alt=""
                            aria-hidden="true">
                        <video
                            id="heroPreview"
                            class="hero-thumb absolute inset-0 h-full w-full object-cover"
                            muted
                            loop
                            playsinline
                            preload="auto"
                            aria-hidden="true">
                            <source src="{{ asset('video/gemini_generated_video_079d102d.mp4') }}?v=4" type="video/mp4">
                        </video>
                        <img
                            id="heroPreviewTwo"
                            class="hero-thumb absolute inset-0 h-full w-full object-cover"
                            src="{{ asset('images/hero/2.svg') }}?v=3"
                            alt=""
                            aria-hidden="true">
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent px-3 pb-2.5 pt-8">
                            <p class="mb-1.5 text-[11px] font-medium tracking-wide text-white/90" id="heroClipLabel">01/03</p>
                            <div class="flex items-center gap-1" id="heroProgressBars">
                                <div class="hero-progress-track is-current min-w-0 overflow-hidden rounded-full bg-white/35">
                                    <div class="hero-progress-fill h-full w-0 rounded-full bg-white" data-slide="0"></div>
                                </div>
                                <div class="hero-progress-track min-w-0 overflow-hidden rounded-full bg-white/35">
                                    <div class="hero-progress-fill h-full w-0 rounded-full bg-white" data-slide="1"></div>
                                </div>
                                <div class="hero-progress-track min-w-0 overflow-hidden rounded-full bg-white/35">
                                    <div class="hero-progress-fill h-full w-0 rounded-full bg-white" data-slide="2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div id="pageContent" class="relative z-10 bg-white shadow-[0_-24px_60px_rgba(0,0,0,0.45)]">
    <!-- Precios actuales -->
    @php
        $fotoCafe = function (string $nombre): ?string {
            foreach (['svg', 'jpg', 'jpeg', 'png', 'webp'] as $extension) {
                if (is_file(public_path("images/cafe/{$nombre}.{$extension}"))) {
                    return asset("images/cafe/{$nombre}.{$extension}");
                }
            }

            return null;
        };
        $fotoVerde = $fotoCafe('verde');
        $fotoSeco = $fotoCafe('seco');
        $fotoPasilla = $fotoCafe('pasilla');
    @endphp
    <section id="acerca-de" class="price-stage relative z-10 overflow-hidden bg-gray-50 py-20" data-price-stage>
        <div class="price-blob absolute -left-24 top-10 h-72 w-72" data-parallax="0.12" aria-hidden="true">
            <span class="bg-[#d4c4a8]/50 blur-3xl"></span>
        </div>
        <div class="price-blob absolute -right-20 bottom-0 h-80 w-80" data-parallax="-0.18" aria-hidden="true">
            <span class="bg-[#b89d7a]/30 blur-3xl"></span>
        </div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 lg:grid-cols-2">
            <div>
                <div class="price-title-clip">
                    <h2 class="price-title text-4xl font-bold text-gray-900 md:text-5xl">Precios actuales</h2>
                </div>
                <div class="price-bar mt-4 h-1 w-24 bg-coffee-600"></div>
                <p class="price-line mt-6 text-lg leading-relaxed text-gray-600" style="--i: 0">
                    El precio del café no es uno solo. Cambia según el tipo de grano y según el día en que el mercado de Colombia publica la cotización.
                </p>
                <p class="price-line mt-4 leading-relaxed text-gray-600" style="--i: 1">
                    El verde es el café excelso y se paga por kilogramo. El seco es el pergamino, el precio de referencia por carga de 125 kilogramos. La pasilla también se reconoce por kilogramo.
                </p>
                <p class="price-line mt-4 leading-relaxed text-gray-600" style="--i: 2">
                    La lista de al lado muestra el mismo valor que publica la Federación Nacional de Cafeteros. Si el mercado cambia, estos precios cambian con él.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="price-badge inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-green-700 shadow-sm">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-500 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                        </span>
                        En vivo
                    </span>
                    <a href="https://federaciondecafeteros.org/" class="text-xs font-semibold text-coffee-700" target="_blank" rel="noopener noreferrer">Fuente: Federación Nacional de Cafeteros</a>
                </div>
                <p id="marketPriceStatus" class="mt-4 text-sm text-gray-600">Consultando el precio del mercado…</p>
                <p id="marketPriceNote" class="mt-1 text-xs text-gray-500"></p>
            </div>

            <div id="precios-mercado" class="price-panel overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl" aria-live="polite">
                <div class="border-b border-gray-100 px-6 py-4">
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">Lista de precios</p>
                </div>
                <ul class="divide-y divide-gray-100">
                    <li class="price-row flex items-center justify-between gap-4 px-6 py-5" style="--i: 0">
                        <div class="flex items-center gap-4">
                            <span data-cafe-foto="verde" style="--i: 0" class="price-photo block h-16 w-16 shrink-0 overflow-hidden rounded-full bg-coffee-100 ring-2 ring-coffee-200">
                                @if ($fotoVerde)
                                    <img src="{{ $fotoVerde }}" alt="Café verde" class="h-full w-full object-cover">
                                @endif
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Café verde</p>
                                <p class="text-sm text-gray-500" data-market-unit="verde">por kilogramo</p>
                                <p class="text-xs text-gray-400" data-market-date="verde"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="price-value text-2xl font-bold text-coffee-700" style="--i: 0" data-market-price="verde">—</p>
                            <p class="text-xs text-gray-500" data-market-extra="verde"></p>
                        </div>
                    </li>
                    <li class="price-row price-row-featured flex items-center justify-between gap-4 bg-coffee-50 px-6 py-5" style="--i: 1">
                        <div class="flex items-center gap-4">
                            <span data-cafe-foto="seco" style="--i: 1" class="price-photo block h-16 w-16 shrink-0 overflow-hidden rounded-full bg-coffee-200 ring-2 ring-coffee-600">
                                @if ($fotoSeco)
                                    <img src="{{ $fotoSeco }}" alt="Café seco" class="h-full w-full object-cover">
                                @endif
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Café seco</p>
                                <p class="text-sm text-gray-500" data-market-unit="seco">carga de 125 kg</p>
                                <p class="text-xs text-gray-400" data-market-date="seco"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="price-value text-2xl font-bold text-coffee-700" style="--i: 1" data-market-price="seco">—</p>
                            <p class="text-xs text-gray-500" data-market-extra="seco"></p>
                        </div>
                    </li>
                    <li class="price-row flex items-center justify-between gap-4 px-6 py-5" style="--i: 2">
                        <div class="flex items-center gap-4">
                            <span data-cafe-foto="pasilla" style="--i: 2" class="price-photo block h-16 w-16 shrink-0 overflow-hidden rounded-full bg-coffee-100 ring-2 ring-coffee-200">
                                @if ($fotoPasilla)
                                    <img src="{{ $fotoPasilla }}" alt="Pasilla" class="h-full w-full object-cover">
                                @endif
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Pasilla</p>
                                <p class="text-sm text-gray-500" data-market-unit="pasilla">por kilogramo</p>
                                <p class="text-xs text-gray-400" data-market-date="pasilla"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="price-value text-2xl font-bold text-coffee-700" style="--i: 2" data-market-price="pasilla">—</p>
                            <p class="text-xs text-gray-500" data-market-extra="pasilla"></p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Módulos Principales -->
    <section id="modulos" class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-reveal>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Módulos Principales</h2>
                <div class="w-24 h-1 bg-coffee-600 mx-auto mb-6"></div>
                <p class="text-xl text-gray-600">Sistema completo con todas las herramientas que necesitas</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Módulo 1: Gestión de Compras -->
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-reveal>
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
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-reveal>
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
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-reveal>
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
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-reveal>
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
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-reveal>
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
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-reveal>
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
            <div class="text-center mb-12" data-reveal>
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
                                Precios actuales
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('heroVideo');
            const preview = document.getElementById('heroPreview');
            const previewSlides = [
                document.getElementById('heroPreviewOne'),
                preview,
                document.getElementById('heroPreviewTwo'),
            ];
            const markSlides = [
                document.getElementById('heroMarkAgro'),
                null,
                document.getElementById('heroMarkCafe'),
            ];
            const plantsSlides = [
                document.getElementById('heroPlantsOne'),
                null,
                document.getElementById('heroPlantsTwo'),
            ];
            const fallback = document.getElementById('videoFallback');
            const progressFills = document.querySelectorAll('.hero-progress-fill');
            const clipLabel = document.getElementById('heroClipLabel');
            const imageOne = document.getElementById('heroImageOne');
            const imageTwo = document.getElementById('heroImageTwo');
            const slides = [imageOne, video, imageTwo];
            const imageHoldMs = 5000;
            let currentSlide = 0;
            let imageTimer = 0;
            let slideStarted = Date.now();

            function silence(element) {
                if (!element) {
                    return;
                }

                element.muted = true;
                element.volume = 0;
                element.defaultMuted = true;
            }

            function paintSlideProgress() {
                if (!progressFills.length) {
                    return;
                }

                let amount = Math.min(100, ((Date.now() - slideStarted) / imageHoldMs) * 100);

                if (currentSlide === 1 && video && Number.isFinite(video.duration) && video.duration > 0) {
                    amount = Math.min(100, (video.currentTime / video.duration) * 100);
                }

                progressFills.forEach(function (fill) {
                    const slideIndex = Number(fill.dataset.slide);

                    if (slideIndex < currentSlide) {
                        fill.style.width = '100%';
                    } else if (slideIndex === currentSlide) {
                        fill.style.width = amount + '%';
                    } else {
                        fill.style.width = '0%';
                    }
                });
            }

            function playFromStart() {
                const begin = function () {
                    video.play().catch(function () {
                        imageTimer = window.setTimeout(function () {
                            showSlide(2);
                        }, imageHoldMs);
                    });
                };

                if (video.currentTime > 0.05) {
                    video.addEventListener('seeked', begin, { once: true });

                    try {
                        video.currentTime = 0;
                    } catch (error) {
                        begin();
                    }

                    return;
                }

                begin();
            }

            function showSlide(index) {
                currentSlide = index;
                slideStarted = Date.now();
                window.clearTimeout(imageTimer);

                slides.forEach(function (slide, slideIndex) {
                    if (!slide) {
                        return;
                    }

                    slide.classList.toggle('is-active', slideIndex === index);
                });

                markSlides.forEach(function (mark, markIndex) {
                    if (mark) {
                        mark.classList.toggle('is-active', markIndex === index);
                    }
                });

                document.querySelectorAll('.hero-phrase').forEach(function (phrase) {
                    const isCurrent = Number(phrase.dataset.phraseSlide) === index;

                    phrase.classList.toggle('is-active', isCurrent);
                    phrase.setAttribute('aria-hidden', isCurrent ? 'false' : 'true');
                });

                plantsSlides.forEach(function (plants, plantsIndex) {
                    if (plants) {
                        plants.classList.toggle('is-active', plantsIndex === index);
                    }
                });

                previewSlides.forEach(function (slide, slideIndex) {
                    if (!slide) {
                        return;
                    }

                    slide.classList.toggle('is-active', slideIndex === index);
                });

                if (preview) {
                    if (index === 1) {
                        preview.play().catch(function () {});
                    } else {
                        preview.pause();
                    }
                }

                if (clipLabel) {
                    clipLabel.textContent = String(index + 1).padStart(2, '0') + '/03';
                }

                document.querySelectorAll('.hero-progress-track').forEach(function (track, trackIndex) {
                    track.classList.toggle('is-current', trackIndex === index);
                });

                if (index === 1 && video) {
                    if (video.readyState >= 2) {
                        playFromStart();
                    } else {
                        video.addEventListener('loadeddata', function () {
                            if (currentSlide === 1) {
                                playFromStart();
                            }
                        }, { once: true });
                    }

                    imageTimer = window.setTimeout(function () {
                        if (currentSlide === 1 && (video.ended || video.paused)) {
                            showSlide(2);
                        }
                    }, 14000);

                    return;
                }

                if (video) {
                    video.pause();
                }

                imageTimer = window.setTimeout(function () {
                    showSlide((index + 1) % slides.length);
                }, imageHoldMs);
            }

            silence(video);
            silence(preview);

            if (video) {
                video.addEventListener('ended', function () {
                    if (currentSlide === 1) {
                        showSlide(2);
                    }
                });

                video.addEventListener('error', function () {
                    if (fallback) {
                        fallback.style.display = 'block';
                    }

                    if (currentSlide === 1) {
                        showSlide(2);
                    }
                });
            }

            window.setInterval(paintSlideProgress, 100);
            showSlide(0);

            // Smooth scroll para todos los enlaces de anclaje
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href !== '') {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            const offsetTop = target.getBoundingClientRect().top + window.scrollY;
                            window.scrollTo({
                                top: offsetTop,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
            
            const formatCop = function (value) {
                return '$' + Math.round(Number(value)).toLocaleString('es-CO');
            };

            let priceStageVisible = false;
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const countUpPrice = function (element) {
                const target = Number(element.dataset.countTarget);

                if (element.dataset.counted === '1' || !Number.isFinite(target)) {
                    return;
                }

                element.dataset.counted = '1';

                if (reduceMotion) {
                    element.textContent = formatCop(target);
                    return;
                }

                const delay = (Number(element.style.getPropertyValue('--i')) || 0) * 150;
                const duration = 1400;
                const startAt = performance.now() + delay;
                element.textContent = formatCop(0);

                const step = function (now) {
                    const progress = Math.min(Math.max((now - startAt) / duration, 0), 1);
                    const eased = 1 - Math.pow(1 - progress, 4);
                    element.textContent = formatCop(Number(element.dataset.countTarget) * eased);

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };

                window.requestAnimationFrame(step);
            };

            const priceStage = document.querySelector('[data-price-stage]');

            const startPriceStage = function () {
                if (priceStageVisible || !priceStage) {
                    return;
                }

                priceStageVisible = true;
                priceStage.classList.add('is-in');
                priceStage.querySelectorAll('[data-market-price]').forEach(function (element) {
                    if (element.dataset.countTarget) {
                        countUpPrice(element);
                    }
                });
            };

            const extraRevealTargets = [
                '[x-data*="currentSlide"]',
                'section.bg-white > div > .grid > *',
                'footer .grid > *',
            ];

            extraRevealTargets.forEach(function (selector) {
                document.querySelectorAll(selector).forEach(function (element) {
                    element.setAttribute('data-reveal', '');
                });
            });

            const scrubRules = [
                ['[data-reveal]', 'rise'],
                ['.price-title', 'title'],
                ['.price-bar', 'bar'],
                ['.price-line', 'rise'],
                ['.price-badge', 'pop'],
                ['.price-panel', 'right'],
                ['.price-row', 'row'],
                ['.price-photo', 'photo'],
                ['.price-value', 'value'],
            ];

            const scrubItems = [];

            scrubRules.forEach(function (rule) {
                document.querySelectorAll(rule[0]).forEach(function (element, index) {
                    if (element.dataset.scrub) {
                        return;
                    }

                    const styleIndex = element.style.getPropertyValue('--i');
                    const order = styleIndex !== '' ? Number(styleIndex) : index % 3;
                    const extra = rule[1] === 'photo' ? 0.05 : (rule[1] === 'value' ? 0.1 : 0);

                    element.dataset.scrub = rule[1];
                    scrubItems.push({ element: element, variant: rule[1], offset: order * 0.2 + extra });
                });
            });

            const clamp01 = function (value) {
                return Math.min(Math.max(value, 0), 1);
            };

            const layoutTop = function (element) {
                let top = 0;
                let node = element;

                while (node) {
                    top += node.offsetTop;
                    node = node.offsetParent;
                }

                return top;
            };

            const backOut = function (t) {
                const c = 1.70158;

                return 1 + (c + 1) * Math.pow(t - 1, 3) + c * Math.pow(t - 1, 2);
            };

            const scrubStyles = function (variant, p, element) {
                const e = 1 - Math.pow(1 - p, 3);
                const rest = 1 - e;
                const blur = 'blur(' + (rest * 6).toFixed(2) + 'px)';

                switch (variant) {
                    case 'title':
                        return { opacity: '1', translate: '0 ' + (rest * 115).toFixed(2) + '%', rotate: (rest * 3).toFixed(2) + 'deg' };
                    case 'bar':
                        return { opacity: String(p), scale: e.toFixed(3) + ' 1' };
                    case 'pop':
                        return { opacity: String(clamp01(p * 2)), scale: String(Math.max(backOut(p), 0).toFixed(3)) };
                    case 'right':
                        return { opacity: String(p), translate: (rest * 90).toFixed(1) + 'px ' + (rest * 40).toFixed(1) + 'px', scale: (0.92 + 0.08 * e).toFixed(3), rotate: (rest * 3).toFixed(2) + 'deg', filter: blur };
                    case 'row':
                        return { opacity: String(p), translate: (rest * 70).toFixed(1) + 'px 0', filter: blur };
                    case 'photo':
                        return { opacity: String(clamp01(p * 2)), scale: String(Math.max(backOut(p), 0).toFixed(3)), rotate: (rest * -200).toFixed(1) + 'deg' };
                    case 'value':
                        return { opacity: String(p), translate: '0 ' + (rest * 18).toFixed(1) + 'px', scale: (0.85 + 0.15 * e).toFixed(3) };
                    default:
                        return { opacity: String(p), translate: '0 ' + (rest * 60).toFixed(1) + 'px', filter: blur };
                }
            };

            const scrubProps = ['opacity', 'translate', 'scale', 'rotate', 'filter'];

            const parallaxBlobs = priceStage ? priceStage.querySelectorAll('[data-parallax]') : [];

            const paintScrub = function () {
                const viewport = window.innerHeight;
                const scrollTop = window.scrollY;
                const start = viewport * 0.94;
                const span = viewport * 0.34;

                const measured = scrubItems.map(function (item) {
                    return start - (layoutTop(item.element) - scrollTop);
                });

                scrubItems.forEach(function (item, index) {
                    const raw = clamp01(measured[index] / span);
                    const p = clamp01(raw * 1.5 - item.offset);
                    const style = item.element.style;

                    if (p >= 1) {
                        scrubProps.forEach(function (prop) {
                            style.removeProperty(prop);
                        });
                    } else {
                        const values = scrubStyles(item.variant, p, item.element);

                        scrubProps.forEach(function (prop) {
                            if (values[prop] !== undefined) {
                                style.setProperty(prop, values[prop]);
                            } else {
                                style.removeProperty(prop);
                            }
                        });
                    }

                    if (item.variant === 'value' && p > 0.3) {
                        startPriceStage();
                    }
                });

                if (priceStage && parallaxBlobs.length) {
                    const rect = priceStage.getBoundingClientRect();
                    const offset = rect.top + rect.height / 2 - viewport / 2;

                    parallaxBlobs.forEach(function (blob) {
                        blob.style.setProperty('--py', (offset * Number(blob.dataset.parallax)) + 'px');
                    });
                }
            };

            if (reduceMotion) {
                scrubItems.forEach(function (item) {
                    delete item.element.dataset.scrub;
                });
                startPriceStage();
            } else {
                let scrubTicking = false;
                const requestScrub = function () {
                    if (!scrubTicking) {
                        scrubTicking = true;
                        window.requestAnimationFrame(function () {
                            scrubTicking = false;
                            paintScrub();
                        });
                    }
                };

                window.addEventListener('scroll', requestScrub, { passive: true });
                window.addEventListener('resize', requestScrub);
                window.addEventListener('load', requestScrub);
                paintScrub();
            }

            const paintMarketPrices = function (data) {
                const status = document.getElementById('marketPriceStatus');
                const note = document.getElementById('marketPriceNote');

                if (status) {
                    status.textContent = 'Mismo precio que publica la Federación Nacional de Cafeteros';
                }

                if (note) {
                    note.textContent = data.note || '';
                }

                (data.types || []).forEach(function (type) {
                    const price = document.querySelector('[data-market-price="' + type.key + '"]');
                    const unit = document.querySelector('[data-market-unit="' + type.key + '"]');
                    const extra = document.querySelector('[data-market-extra="' + type.key + '"]');
                    const date = document.querySelector('[data-market-date="' + type.key + '"]');

                    if (price && type.price !== null && type.price !== undefined) {
                        price.dataset.countTarget = String(type.price);

                        if (price.dataset.counted === '1') {
                            price.textContent = formatCop(type.price);
                        } else if (priceStageVisible) {
                            countUpPrice(price);
                        } else {
                            price.textContent = formatCop(type.price);
                        }
                    }

                    if (unit && type.unit_label) {
                        unit.textContent = type.unit_label;
                    }

                    if (extra) {
                        extra.textContent = type.price_per_kg ? formatCop(type.price_per_kg) + ' por kilogramo' : '';
                    }

                    if (date) {
                        date.textContent = type.date_label || '';
                    }
                });
            };

            const refreshMarketPrices = function () {
                fetch(@json(route('coffee-market.prices')), {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('market');
                    }

                    return response.json();
                }).then(paintMarketPrices).catch(function () {
                    const status = document.getElementById('marketPriceStatus');
                    const seco = document.querySelector('[data-market-price="seco"]');

                    if (status && seco && seco.textContent === '—') {
                        status.textContent = 'No se pudo consultar el mercado. Se reintenta solo.';
                    }
                });
            };

            refreshMarketPrices();
            window.setInterval(function () {
                if (document.hidden) {
                    return;
                }

                refreshMarketPrices();
            }, 30000);
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    refreshMarketPrices();
                }
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
