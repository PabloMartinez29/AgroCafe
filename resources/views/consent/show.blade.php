<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-coffee-50 px-4 py-10">
        <div class="w-full max-w-xl rounded-3xl bg-white p-8 shadow-2xl sm:p-10">
            <h1 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Antes de continuar</h1>
            <p class="mt-3 text-gray-600">
                Hola, {{ auth()->user()->name }}. Para seguir usando AgroCafé necesitamos que leas y aceptes la versión vigente
                de nuestros documentos. Son dos aceptaciones separadas.
            </p>

            <form method="POST" action="{{ route('consent.store') }}" class="mt-6 space-y-6">
                @csrf

                <x-consent-checkboxes />

                <button
                    type="submit"
                    class="w-full rounded-xl bg-coffee-600 px-6 py-4 text-base font-bold text-white transition hover:bg-coffee-700 focus:outline-none focus:ring-4 focus:ring-coffee-500/50"
                >
                    Aceptar y continuar
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
                @csrf
                <button type="submit" class="text-sm text-gray-500 underline hover:text-gray-700">
                    No acepto, cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
