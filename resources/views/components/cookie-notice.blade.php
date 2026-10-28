@props(['suppress' => false])

@unless ($suppress)
<style>
    .cookie-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(3px);
    }

    .cookie-modal-overlay[hidden] {
        display: none;
    }

    .cookie-modal-overlay.is-open {
        animation: cookieOverlayIn 0.35s ease-out both;
    }

    .cookie-modal-overlay.is-open .cookie-modal-card {
        animation: cookieDrop 1.1s cubic-bezier(0.22, 0.61, 0.36, 1) both;
    }

    .cookie-modal-overlay.is-closing {
        animation: cookieOverlayOut 0.4s ease-in both;
    }

    .cookie-modal-overlay.is-closing .cookie-modal-card {
        animation: cookieLeave 0.4s ease-in both;
    }

    @keyframes cookieOverlayIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes cookieOverlayOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes cookieDrop {
        0% { opacity: 0; transform: translateY(-110vh) rotate(-4deg); }
        45% { opacity: 1; transform: translateY(0) rotate(0deg); }
        62% { transform: translateY(-34px) rotate(1deg); }
        78% { transform: translateY(0) rotate(0deg); }
        88% { transform: translateY(-12px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes cookieLeave {
        from { opacity: 1; transform: translateY(0) scale(1); }
        to { opacity: 0; transform: translateY(40px) scale(0.96); }
    }

    @media (prefers-reduced-motion: reduce) {
        .cookie-modal-overlay.is-open,
        .cookie-modal-overlay.is-open .cookie-modal-card,
        .cookie-modal-overlay.is-closing,
        .cookie-modal-overlay.is-closing .cookie-modal-card {
            animation-duration: 0.01s;
        }
    }
</style>

<div
    id="cookieNotice"
    class="cookie-modal-overlay"
    role="dialog"
    aria-modal="true"
    aria-labelledby="cookieNoticeTitle"
    aria-describedby="cookieNoticeText"
    hidden
>
    <div class="cookie-modal-card w-full max-w-md rounded-3xl bg-[#4a392e] p-6 text-white shadow-2xl sm:p-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-2xl" aria-hidden="true">🍪</div>
        <h2 id="cookieNoticeTitle" class="mt-4 text-xl font-bold">Sobre las cookies</h2>
        <p id="cookieNoticeText" class="mt-2 text-sm leading-relaxed text-white/90">
            Usamos únicamente cookies técnicas necesarias para iniciar sesión y proteger tu cuenta.
            No usamos cookies de publicidad ni de analítica, por eso no necesitamos pedirte permiso.
            <a href="{{ route('legal.cookies') }}" target="_blank" rel="noopener noreferrer" class="font-semibold underline">Más información</a>
        </p>
        <div class="mt-6 flex justify-end">
            <button type="button" id="cookieNoticeClose" class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#4a392e] hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white/40">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        var key = 'agrocafe_cookie_notice';
        var notice = document.getElementById('cookieNotice');
        var closeButton = document.getElementById('cookieNoticeClose');

        if (!notice || !closeButton) {
            return;
        }

        try {
            if (window.localStorage.getItem(key) === '1') {
                return;
            }
        } catch (error) {
            // Sin almacenamiento disponible: se muestra el aviso en cada visita.
        }

        var previouslyFocused = null;

        var open = function () {
            previouslyFocused = document.activeElement;
            notice.hidden = false;
            notice.classList.add('is-open');
            document.addEventListener('keydown', onKeydown);
            closeButton.focus({ preventScroll: true });
        };

        var close = function () {
            if (notice.classList.contains('is-closing')) {
                return;
            }

            try {
                window.localStorage.setItem(key, '1');
            } catch (error) {
                // Sin almacenamiento disponible: no se puede recordar la lectura.
            }

            var finish = function () {
                notice.hidden = true;
                notice.classList.remove('is-open', 'is-closing');
                document.removeEventListener('keydown', onKeydown);

                if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
                    previouslyFocused.focus({ preventScroll: true });
                }
            };

            notice.classList.add('is-closing');
            window.setTimeout(finish, 420);
        };

        var onKeydown = function (event) {
            if (event.key === 'Escape') {
                close();
            }

            if (event.key === 'Tab') {
                var focusable = notice.querySelectorAll('a[href], button');
                var first = focusable[0];
                var last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        };

        closeButton.addEventListener('click', close);

        window.setTimeout(open, 500);
    })();
</script>
@endunless
