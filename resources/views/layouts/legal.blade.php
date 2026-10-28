<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ config('app.name', 'AgroCafé') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-coffee-50 font-sans text-gray-800 antialiased">
        <header class="bg-gradient-to-r from-coffee-700 to-coffee-900 text-white">
            <div class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-4 px-4 py-5">
                <a href="{{ url('/') }}" class="flex items-center gap-3 text-xl font-bold">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                        <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#f3f4f6" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    </svg>
                    AgroCafé
                </a>
                <a
                    href="{{ url('/') }}"
                    class="group inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white no-underline shadow-sm backdrop-blur-sm transition hover:bg-white hover:text-coffee-800 focus:outline-none focus:ring-4 focus:ring-white/30"
                >
                    <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 12H5" />
                        <path d="M12 19l-7-7 7-7" />
                    </svg>
                    Volver al inicio
                </a>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-4 py-10">
            <article class="rounded-3xl bg-white p-6 shadow-xl sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-wider text-coffee-600">Documento legal</p>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">@yield('title')</h1>
                <p class="mt-3 text-sm text-gray-500">
                    Versión {{ $document['version'] }} · Vigente desde
                    {{ \Illuminate\Support\Carbon::parse($document['effective_date'])->locale('es')->translatedFormat('j \d\e F \d\e Y') }}
                </p>

                <div class="mt-8 space-y-4 leading-relaxed text-gray-700 [&_a]:font-semibold [&_a]:text-coffee-600 [&_a]:underline [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_li]:ml-1 [&_ol]:list-decimal [&_ol]:space-y-2 [&_ol]:pl-6 [&_strong]:text-gray-900 [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:pl-6">
                    @yield('content')
                </div>
            </article>

            <nav class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-coffee-700" aria-label="Documentos legales">
                @foreach (['terms', 'privacy', 'cookies', 'refund'] as $key)
                    <a href="{{ route(config("legal.documents.{$key}.route")) }}" class="hover:underline {{ $document === config("legal.documents.{$key}") ? 'font-bold underline' : '' }}">
                        {{ config("legal.documents.{$key}.title") }}
                    </a>
                @endforeach
            </nav>
        </main>

        <x-cookie-notice :suppress="request()->routeIs('legal.cookies')" />
    </body>
</html>
