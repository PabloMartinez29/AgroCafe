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
        .is-hscroll .hscroll-sticky {
            position: sticky;
            top: 0;
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .is-hscroll .hscroll-viewport { overflow: hidden; padding: 1.25rem 0 2rem; }
        .is-hscroll .hscroll-track {
            display: flex;
            width: max-content;
            max-width: none;
            margin: 0;
            gap: 2rem;
            padding-inline: max(1rem, calc((100vw - 80rem) / 2 + 1rem));
            will-change: transform;
        }
        .is-hscroll .hscroll-track > article { flex: none; width: min(88vw, 26rem); }
        .is-hscroll .hscroll-progress-track { display: block; }
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
                <source src="{{ route('hero.video') }}?v=5" type="video/mp4">
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
                            <source src="{{ route('hero.video') }}?v=5" type="video/mp4">
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

    <!-- Consejos de cosecha y venta -->
    <section id="consejos" class="bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 text-center pt-20 pb-6">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Consejos para mejorar la cosecha y las ventas</h2>
            <div class="w-24 h-1 bg-coffee-600 mx-auto mb-6"></div>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Recomendaciones prácticas sobre recolección, beneficio, secado, almacenamiento y calidad del café.
            </p>
        </div>

        <div data-hscroll>
        <div class="hscroll-sticky py-6">
            <div class="hscroll-viewport">
            <div class="hscroll-track grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto px-4">
                <article id="recoleccion" class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Cuándo y cómo recolectar el café?</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        La cereza madura es la que paga. Un fruto verde o pasado se convierte en pasilla y le baja el precio a toda la carga.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1"></i>
                            <span>Coseche solo grano maduro, rojo o amarillo según la variedad. Deje el pintón para el siguiente pase.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1"></i>
                            <span>No mezcle en el mismo costal cereza madura, verde, seca ni del suelo.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1"></i>
                            <span>Despulpe el mismo día. Si el café cosechado pasa de 8 horas al sol, empieza a fermentar.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1"></i>
                            <span>Repase el lote y recoja el fruto caído. Ahí se cría la broca y se pierde peso vendible.</span>
                        </li>
                    </ul>
                </article>

                <article id="beneficio" class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-tint text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Cómo beneficiar el café sin dañar la taza?</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        El beneficio define si el pergamino sale limpio o con sabor a vinagre. Un tanque sucio o una fermentación larga se nota el día de la compra.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-purple-500 mt-1"></i>
                            <span>Despulpe con la máquina calibrada y limpia. Un grano mordido se parte en el secado y lo clasifican como defecto.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-purple-500 mt-1"></i>
                            <span>Fermente en tanque lavado. En clima cálido suelen bastar 12 a 18 horas; en frío, hasta 24. Más tiempo saca sabor a fermento.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-purple-500 mt-1"></i>
                            <span>Lave con agua limpia hasta quitar el mucílago. El agua sucia mancha el pergamino.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-purple-500 mt-1"></i>
                            <span>Separe flotes y pasilla desde el lavado. No los devuelva al café bueno.</span>
                        </li>
                    </ul>
                </article>

                <article id="secado" class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-sun text-amber-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Cómo secar el café hasta el punto de venta?</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        El pergamino seco se reconoce entre 10 % y 12 % de humedad. Más húmedo lo castigan en la báscula; demasiado seco se quiebra y también pierde.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-amber-500 mt-1"></i>
                            <span>Tiéndalo en capa de 3 a 4 centímetros sobre marquesina, elba o patio limpio. Nunca sobre tierra.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-amber-500 mt-1"></i>
                            <span>Revuélvalo varias veces al día para que seque parejo y no se recuente.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-amber-500 mt-1"></i>
                            <span>Cúbralo si llueve. El café que se moja otra vez se mancha y vuelve a fermentar.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-amber-500 mt-1"></i>
                            <span>No lo seque junto a humo, estufa o combustible. Ese olor queda en la taza y se lo rechazan.</span>
                        </li>
                    </ul>
                </article>

                <article id="almacenamiento" class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-coffee-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-warehouse text-coffee-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Cómo almacenar el café para proteger su calidad antes de venderlo?</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        El café guardado en malas condiciones pierde taza, gana humedad y baja de precio. Guarde solo el que ya está seco.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-coffee-500 mt-1"></i>
                            <span>Use una bodega seca, ventilada y sin goteras. El café va sobre estibas, nunca en el piso.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-coffee-500 mt-1"></i>
                            <span>Empáquelo en sacos limpios. Aléjelo de abonos, venenos, gasolina, pintura y comida con olor fuerte: el grano absorbe los olores.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-coffee-500 mt-1"></i>
                            <span>Revise que no haya moho, gorgojo ni olor a reposo antes de salir a vender.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-coffee-500 mt-1"></i>
                            <span>No almacene café húmedo “para después”. La espera con humedad alta se convierte en descuento.</span>
                        </li>
                    </ul>
                </article>

                <article id="calidad" class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-award text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Qué calidad le pagan en la compra?</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        El precio no depende solo del día. También depende del factor de rendimiento, de la pasilla y de los defectos de taza.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                            <span>Menos broca, grano negro, vinagre y moho significan mejor factor y mejor pago por carga.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                            <span>Separe la pasilla. Si la mezcla con el pergamino bueno, le descuentan toda la venta.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                            <span>Evite los defectos de taza: fermento, reposo, moho y fenol. Esos sabores generan rebaja o rechazo.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                            <span>Pese en báscula y, si tiene medidor, anote la humedad. Así llega a la venta con el dato claro.</span>
                        </li>
                    </ul>
                </article>

                <article id="venta" class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-handshake text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Cómo vender según el precio del día?</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Verde, seco y pasilla no valen lo mismo. Conviene mirar el precio del mercado antes de salir de la finca.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-indigo-500 mt-1"></i>
                            <span>El seco es el precio de referencia por carga de 125 kilogramos. El verde y la pasilla se pagan por kilogramo.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-indigo-500 mt-1"></i>
                            <span>Termine el secado antes de vender. El descuento por humedad casi siempre es mayor que el valor de esperar unos días.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-indigo-500 mt-1"></i>
                            <span>Compare el precio del punto de compra con el precio del día. Lleve el café limpio, seco y bien empacado.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-indigo-500 mt-1"></i>
                            <span>Anote fecha, kilos, tipo y precio de cada venta. Así sabe si le conviene vender hoy o esperar.</span>
                        </li>
                    </ul>
                </article>
            </div>
            </div>

            <div class="hscroll-progress-track mx-auto mt-6 hidden h-1 w-48 overflow-hidden rounded-full bg-gray-200" aria-hidden="true">
                <div class="hscroll-progress h-full w-full origin-left rounded-full bg-coffee-600" style="transform: scaleX(0)"></div>
            </div>
        </div>
        </div>
    </section>

    <!-- Acerca de la plataforma -->
    <section id="plataforma" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-14" data-reveal>
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-coffee-600">Qué es AgroCafé</p>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Acerca de la plataforma</h2>
                <div class="w-24 h-1 bg-coffee-600 mx-auto mb-6"></div>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Un sistema web para llevar el negocio del café en orden: desde la compra al campesino hasta la venta, con el precio del mercado siempre a la vista.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <article class="rounded-2xl border border-gray-100 bg-gray-50 p-8" data-reveal>
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-coffee-600 text-white">
                        <i class="fas fa-seedling text-xl"></i>
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-gray-900">¿Qué es AgroCafé?</h3>
                    <p class="leading-relaxed text-gray-600">
                        AgroCafé es un sistema de gestión para la compra, venta e inventario de café. Reúne en un solo lugar la caja, las compras a campesinos, la bodega, las facturas, los pagos y las ventas.
                    </p>
                    <p class="mt-4 leading-relaxed text-gray-600">
                        Tiene un panel para el administrador y otro para el campesino, y una API para conectar una aplicación móvil al mismo sistema. Cuenta con 10 módulos y con soporte de lunes a viernes, de 8:00 AM a 6:00 PM, en <a href="mailto:contacto@agrocafe.com" class="font-semibold text-coffee-600 underline">contacto@agrocafe.com</a>.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-100 bg-gray-50 p-8" data-reveal>
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-coffee-600 text-white">
                        <i class="fas fa-bullseye text-xl"></i>
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-gray-900">¿Con qué fin se hizo?</h3>
                    <p class="leading-relaxed text-gray-600">
                        Se hizo para que el negocio cafetero deje de depender de cuadernos y hojas sueltas. Busca que cada compra, pago y factura quede registrada y que la bodega cuadre con lo que se compra y se vende.
                    </p>
                    <p class="mt-4 leading-relaxed text-gray-600">
                        También busca que el campesino pueda comprobar lo que le pagaron, con el mismo precio que publica la Federación Nacional de Cafeteros.
                    </p>
                </article>
            </div>

            <div class="mt-16 text-center" data-reveal>
                <h3 class="text-3xl font-bold text-gray-900">¿Qué impacto tiene?</h3>
                <div class="mx-auto mt-4 h-1 w-16 bg-coffee-600"></div>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <article class="rounded-2xl border border-gray-100 bg-white p-7 shadow-lg" data-reveal>
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                        <i class="fas fa-list-check text-green-600"></i>
                    </div>
                    <h4 class="mb-2 text-xl font-bold text-gray-900">Orden y menos errores</h4>
                    <p class="leading-relaxed text-gray-600">
                        Compras, pagos, bodega, caja y facturas trabajan sobre los mismos datos. No hay que repetir registros ni sumar a mano.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-100 bg-white p-7 shadow-lg" data-reveal>
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                        <i class="fas fa-handshake text-blue-600"></i>
                    </div>
                    <h4 class="mb-2 text-xl font-bold text-gray-900">Confianza con el campesino</h4>
                    <p class="leading-relaxed text-gray-600">
                        Cada productor ve sus propias compras y facturas, y puede descargar el PDF de cada una cuando lo necesite.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-100 bg-white p-7 shadow-lg" data-reveal>
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                    <h4 class="mb-2 text-xl font-bold text-gray-900">Mejores decisiones</h4>
                    <p class="leading-relaxed text-gray-600">
                        El precio del día del café verde, seco y pasilla, junto con el análisis de precios, ayuda a decidir cuándo comprar y cuándo vender.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Footer Profesional - Colores Café y Blanco -->
    <footer class="bg-gradient-to-b from-coffee-800 via-coffee-700 to-coffee-900 text-white">
        <!-- Sección Principal del Footer -->
        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-12 md:grid-cols-2 md:gap-24">
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
                        <a href="{{ route('legal.privacy') }}" class="hover:text-white transition-colors">Política de Privacidad</a>
                        <span class="text-white/50">|</span>
                        <a href="{{ route('legal.terms') }}" class="hover:text-white transition-colors">Términos y Condiciones</a>
                        <span class="text-white/50">|</span>
                        <a href="{{ route('legal.cookies') }}" class="hover:text-white transition-colors">Cookies</a>
                        <span class="text-white/50">|</span>
                        <a href="{{ route('legal.refund') }}" class="hover:text-white transition-colors">Reembolsos</a>
                        <span class="text-white/50">|</span>
                        <a href="mailto:contacto@agrocafe.com" class="hover:text-white transition-colors">Soporte</a>
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
            const firstImageHoldMs = 3000;
            const holdFor = function (index) {
                return index === 0 ? firstImageHoldMs : imageHoldMs;
            };
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

                let amount = Math.min(100, ((Date.now() - slideStarted) / holdFor(currentSlide)) * 100);

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
                }, holdFor(index));
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
                            const hscrollTarget = target.closest('[data-hscroll]') && target.tagName === 'ARTICLE' && window.hscrollScrollFor
                                ? window.hscrollScrollFor(target)
                                : null;
                            const offsetTop = hscrollTarget !== null
                                ? hscrollTarget
                                : target.getBoundingClientRect().top + window.scrollY;
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

            const hscrollSection = document.querySelector('[data-hscroll]');
            let hscrollState = null;

            if (hscrollSection && !reduceMotion) {
                const hscrollSticky = hscrollSection.querySelector('.hscroll-sticky');
                const hscrollViewport = hscrollSection.querySelector('.hscroll-viewport');
                const hscrollTrack = hscrollSection.querySelector('.hscroll-track');
                const hscrollBar = hscrollSection.querySelector('.hscroll-progress');

                hscrollSection.classList.add('is-hscroll');

                const measureHscroll = function () {
                    hscrollSection.style.height = '';
                    hscrollSticky.style.top = '';

                    const stickyHeight = hscrollSticky.offsetHeight;
                    const stickyTop = Math.min(0, window.innerHeight - stickyHeight);
                    const maxShift = Math.max(0, hscrollTrack.scrollWidth - hscrollViewport.clientWidth);

                    hscrollSticky.style.top = stickyTop + 'px';
                    hscrollSection.style.height = (stickyHeight + maxShift) + 'px';
                    hscrollState = { stickyTop: stickyTop, maxShift: maxShift };
                };

                const paintHscroll = function () {
                    if (!hscrollState) {
                        return;
                    }

                    const rect = hscrollSection.getBoundingClientRect();
                    const progress = hscrollState.maxShift > 0
                        ? clamp01((hscrollState.stickyTop - rect.top) / hscrollState.maxShift)
                        : 0;

                    hscrollTrack.style.transform = 'translate3d(' + (-progress * hscrollState.maxShift).toFixed(1) + 'px, 0, 0)';

                    if (hscrollBar) {
                        hscrollBar.style.transform = 'scaleX(' + progress.toFixed(3) + ')';
                    }
                };

                let hscrollTicking = false;
                const requestHscroll = function () {
                    if (!hscrollTicking) {
                        hscrollTicking = true;
                        window.requestAnimationFrame(function () {
                            hscrollTicking = false;
                            paintHscroll();
                        });
                    }
                };

                const remeasureHscroll = function () {
                    measureHscroll();
                    paintHscroll();
                };

                window.hscrollScrollFor = function (element) {
                    if (!hscrollState || hscrollState.maxShift <= 0) {
                        return null;
                    }

                    const trackLeft = hscrollTrack.getBoundingClientRect().left;
                    const left = element.getBoundingClientRect().left - trackLeft;
                    const padding = Math.max(16, (window.innerWidth - 1280) / 2 + 16);
                    const shift = Math.min(Math.max(left - padding, 0), hscrollState.maxShift);
                    const sectionTop = hscrollSection.getBoundingClientRect().top + window.scrollY;

                    return sectionTop - hscrollState.stickyTop + shift;
                };

                window.addEventListener('scroll', requestHscroll, { passive: true });
                window.addEventListener('resize', remeasureHscroll);
                window.addEventListener('load', remeasureHscroll);

                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(remeasureHscroll);
                }

                remeasureHscroll();
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
        });
    </script>

    <x-cookie-notice />
    </body>
</html>
