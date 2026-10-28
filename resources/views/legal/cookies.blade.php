@extends('layouts.legal')

@section('title', $document['title'])

@section('content')
    <p>
        Una cookie es un pequeño archivo que el sitio guarda en tu navegador. En AgroCafé usamos <strong>únicamente cookies técnicas
        necesarias</strong> para que puedas iniciar sesión y para proteger tu cuenta. <strong>No usamos cookies de publicidad, de analítica
        ni de redes sociales.</strong>
    </p>

    <h2>1. Cookies que usamos</h2>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[32rem] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b-2 border-gray-200 text-gray-900">
                    <th class="py-2 pr-4">Cookie</th>
                    <th class="py-2 pr-4">Para qué sirve</th>
                    <th class="py-2 pr-4">Duración</th>
                    <th class="py-2">Tipo</th>
                </tr>
            </thead>
            <tbody class="align-top">
                <tr class="border-b border-gray-100">
                    <td class="py-2 pr-4"><code>{{ config('session.cookie') }}</code></td>
                    <td class="py-2 pr-4">Mantiene tu sesión iniciada.</td>
                    <td class="py-2 pr-4">{{ config('session.lifetime') }} minutos sin actividad</td>
                    <td class="py-2">Necesaria, propia</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="py-2 pr-4"><code>XSRF-TOKEN</code></td>
                    <td class="py-2 pr-4">Protege los formularios contra solicitudes falsificadas (CSRF).</td>
                    <td class="py-2 pr-4">{{ config('session.lifetime') }} minutos</td>
                    <td class="py-2">Necesaria, propia</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="py-2 pr-4"><code>remember_web_…</code></td>
                    <td class="py-2 pr-4">Solo si marcas "Recordarme" al iniciar sesión: evita volver a escribir tus datos.</td>
                    <td class="py-2 pr-4">Hasta 400 días</td>
                    <td class="py-2">Funcional, propia, bajo tu decisión</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>
        Además, guardamos en tu navegador (almacenamiento local, no es una cookie) la marca <code>agrocafe_cookie_notice</code> para no volver a mostrarte
        el aviso de cookies.
    </p>

    <h2>2. ¿Necesitamos pedirte consentimiento?</h2>
    <p>
        Como solo usamos cookies técnicas indispensables para el servicio que tú solicitas, no te pedimos aceptarlas antes de cargarlas:
        te informamos con un aviso y con esta política. La información que contienen se trata conforme a la
        <a href="{{ route('legal.privacy') }}">Política de Privacidad</a>, que sí autorizas de forma expresa.
        Si algún día añadimos cookies de analítica, publicidad o de terceros, te mostraremos antes un banner con las opciones
        de aceptar o rechazar y no las cargaremos hasta que aceptes.
    </p>

    <h2>3. Recursos de terceros</h2>
    <p>
        La página carga tipografías, iconos y estilos desde servicios externos (Bunny Fonts, cdnjs y Tailwind CSS). Esos servicios reciben
        tu dirección IP y datos técnicos del navegador para entregarte los archivos. Más detalle en la Política de Privacidad.
        Los enlaces a redes sociales solo te llevan a esos sitios; no instalamos sus botones ni sus cookies.
    </p>

    <h2>4. Cómo borrar o bloquear cookies</h2>
    <p>
        Puedes eliminarlas o bloquearlas desde la configuración de tu navegador. Si bloqueas las cookies necesarias no podrás iniciar sesión.
    </p>

    <h2>5. Contacto</h2>
    <p>Dudas sobre esta política: <a href="mailto:{{ $controller['privacy_email'] }}">{{ $controller['privacy_email'] }}</a>.</p>
@endsection
