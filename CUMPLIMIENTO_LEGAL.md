# Cumplimiento legal — AgroCafé (Colombia)

> Esto es una revisión técnica del proyecto, no asesoría jurídica. Antes de publicar, que un abogado
> de protección de datos revise los textos de `resources/views/legal/`.

## 1. Qué quedó implementado

| # | Pedido | Dónde está |
| --- | --- | --- |
| 1 | Términos y Condiciones | `/terminos-y-condiciones` · `resources/views/legal/terms.blade.php` |
| 2 | Política de Privacidad (Ley 1581/2012) | `/politica-de-privacidad` · `legal/privacy.blade.php` |
| 3 | Política de Cookies + aviso | `/politica-de-cookies` · `legal/cookies.blade.php` · `components/cookie-notice.blade.php` |
| 4 | Dos casillas separadas con enlace | `components/consent-checkboxes.blade.php` (registro, pantalla de aceptación, crear usuario, crear cooperativa, API) |
| 5 y 10 | Registro probatorio y trazable | tabla `consent_records`, `ConsentService`, comandos `consents:verify` y `consents:evidence` |
| 6 | Reembolsos | `/politica-de-reembolsos` · `legal/refund.blade.php` |
| 7 | Solo datos necesarios | registro pide nombre, correo y contraseña |
| 8 | Derechos de las imágenes | sección 5 de este documento |
| 9 | Leyes locales | sección 6 de este documento |

Los datos de la empresa (NIT, dirección, correo de privacidad) salen de `config/legal.php` y se llenan en `.env`:

```env
LEGAL_COMPANY_NAME="AgroCafé"
LEGAL_COMPANY_NIT=
LEGAL_COMPANY_ADDRESS=
LEGAL_COMPANY_CITY=
LEGAL_CONTACT_EMAIL=contacto@agrocafe.com
LEGAL_PRIVACY_EMAIL=contacto@agrocafe.com
LEGAL_CONTACT_PHONE="+57 300 123 4567"
LEGAL_SELLS_ONLINE=false
```

## 2. Cookies: ¿hay que pedir consentimiento?

La app solo usa cookies técnicas: sesión (`config('session.cookie')`), `XSRF-TOKEN` y, si el usuario marca "Recordarme", `remember_web_*`.
No hay analítica, publicidad ni píxeles de redes sociales.

- La SIC trata las cookies como posible tratamiento de datos personales (Ley 1581). Las técnicas indispensables no requieren consentimiento previo; las de analítica, publicidad o funcionalidad opcional sí (previo, expreso, informado y demostrable).
- Por eso hay **aviso informativo** (no banner de aceptar/rechazar) y política.
- **Si algún día agregas Google Analytics, Meta Pixel, etc.**, hay que cambiar el aviso por un banner con aceptar/rechazar, no cargar esas cookies hasta que acepten y guardar la prueba en `consent_records`.
- La página carga Tailwind (`cdn.tailwindcss.com`), Font Awesome (`cdnjs`) y fuentes (`fonts.bunny.net`) desde terceros: reciben la IP del visitante. Está declarado en la política. Lo ideal es servirlos desde tu propio servidor.

## 3. Reembolsos

AgroCafé **no vende en línea**: registra compras y ventas que se pagan fuera de la plataforma. Por eso la política dice que no hay reembolsos
y que se corrigen errores de facturación por solicitud. El derecho de retracto (Ley 1480/2011, art. 47: 5 días hábiles, devolución en 30 días)
solo aplica a ventas a distancia a consumidores. Si habilitas ventas en línea, pon `LEGAL_SELLS_ONLINE=true` y la página mostrará la política de retracto y garantía.

## 4. Cómo probar la autorización (SIC / reclamos)

Cada vez que alguien acepta se guardan, **sin poder editarlos ni borrarlos**:

- titular (tipo, id, nombre, correo; se conservan aunque borre su cuenta),
- documento (`terms` / `privacy`), versión y huella SHA-256 del texto que vio,
- texto exacto de la casilla,
- fecha y hora, IP y navegador,
- canal: `web_register`, `api_register`, `web_accept`, `api_accept`, `admin_assisted`, `account_deleted`,
- quién la capturó (si fue un administrador),
- huella del registro anterior y propia (cadena: alterar o borrar un registro la rompe).

Casos:

- **Registro propio:** el usuario marca las dos casillas (sin marcar de antemano). Una casilla sin marcar bloquea el registro.
- **Cuenta creada por un administrador:** el admin marca las casillas ("confirmo que informé al titular…") y queda como `admin_assisted`, **pero no vale como autorización del titular**: la primera vez que la persona ingrese, el middleware `consent` la lleva a `/aceptar-politicas` y solo entonces queda registrada su propia aceptación.
- **Usuarios antiguos o cambio de versión:** el mismo middleware los bloquea hasta que acepten la versión vigente (para publicar una versión nueva, sube `version` y `effective_date` en `config/legal.php`).
- **Eliminar cuenta:** se registra la revocatoria y la prueba se mantiene.
- **API móvil:** `POST /api/register` exige `accept_terms` y `accept_privacy`; `POST /api/login` devuelve `consents_pending`; mientras haya pendientes, el resto de la API responde 403 hasta llamar a `POST /api/consents`.

Comandos:

```bash
php artisan consents:evidence persona@correo.com          # tabla con la evidencia + verificación
php artisan consents:evidence persona@correo.com --json   # evidencia completa para entregar
php artisan consents:verify                               # revisa toda la cadena
```

Recomendaciones para que aguante un requerimiento:

1. Haz copia de seguridad de la base de datos y guarda fuera del servidor la huella que imprime `consents:verify` (así se detecta si borran los últimos registros).
2. Dale al usuario de la base de datos de la aplicación permisos que no incluyan `UPDATE` ni `DELETE` sobre `consent_records`.
3. Conserva los archivos `resources/views/legal/*.blade.php` en git: la huella guardada permite demostrar qué texto exacto aceptó cada persona.
4. **Brecha conocida:** la SIC también evalúa si se puede comprobar *la identidad* de quien aceptó. Hoy el correo no se verifica (el modelo `User` no implementa `MustVerifyEmail`). Cuando puedas, activa la verificación de correo para reforzar la prueba. No lo activé porque bloquearía a todas las cuentas existentes.
5. Los registros anteriores a esta versión no existen: esas personas quedan "sin autorización" hasta que ingresen y acepten.

## 5. Imágenes y video

No encontré licencias, créditos ni metadatos de autoría en ningún archivo (los JPEG incrustados en los SVG no traen EXIF, IPTC ni C2PA). **No puedo verificar el origen desde el código**: lo tienes que confirmar tú con la fuente original.

| Archivo | Dónde se usa | Qué es | Riesgo / qué hacer |
| --- | --- | --- | --- |
| `public/images/hero/1.svg` | Carrusel principal | SVG con una foto JPEG incrustada (cafetal al atardecer) | Origen desconocido. Si es de banco de imágenes, guarda la licencia; si es IA, ver abajo |
| `public/images/hero/2.svg` | Carrusel principal | SVG con JPEG incrustado (café secándose). Tiene la marca del codificador `Lavc` (FFmpeg), típica de un fotograma sacado de un video | Posible fotograma del video de IA. Tratar como IA hasta confirmar |
| `public/images/cafe/pasilla.svg`, `seco.svg`, `verde.svg` | Fotos de la lista de precios | SVG con JPEG incrustado | Origen desconocido, confirmar |
| `public/video/gemini_generated_video_079d102d.mp4` | Video del carrusel | El nombre indica que lo generó Gemini (Google) | IA. Revisar términos de uso de Google para uso comercial |
| `public/video/coffee-hero.mp4` | No se usa en ningún lado (30 MB) | Desconocido | Bórralo si no lo necesitas, o documenta su licencia |
| `public/favicon.svg` | Favicon y logo | Vector simple de 3 formas | Parece hecho por ti; es lo único que se ve propio |
| Iconos Font Awesome 6.5.1 (CDN) | Toda la web | Licencia gratuita (iconos CC BY 4.0, fuentes SIL OFL, código MIT) | Está bien cargado desde el CDN oficial, que conserva la atribución |
| Fuente Figtree (Bunny Fonts) | Toda la web | SIL OFL | Uso libre |

Sobre las imágenes de IA en Colombia (Dirección Nacional de Derecho de Autor, resoluciones de 2024-2025): solo las personas naturales pueden ser autoras, y un *prompt* no basta; las salidas de una IA **no se pueden registrar** ni se protegen como obra. Consecuencias:

- Puedes usarlas, pero **no tienes derecho exclusivo**: cualquiera podría copiarlas.
- El uso comercial depende de los términos de la herramienta que las generó (guarda captura/URL de esos términos y la fecha de generación).
- Evita imágenes de IA con rostros reales, marcas o logos de terceros.
- Es recomendable decirlo: los Términos (sección 6) ya indican que algunas imágenes son ilustrativas y pueden ser de IA. Si quieres ser más específico, añade un pie de foto.

Para cada imagen que **no** sea de IA, guarda: autor, fuente (URL), licencia, fecha de descarga y prueba de compra o licencia. Si no la puedes demostrar, reemplázala por una propia o por una de un banco con licencia clara (Unsplash/Pexels con atribución cuando la licencia lo pida).

## 6. Normas colombianas revisadas y qué falta de tu parte

Normas: Constitución art. 15 (hábeas data), Ley 1581 de 2012 y Decreto 1377 de 2013 (compilado en el Decreto 1074 de 2015),
Circular Única de la SIC (Título V), Ley 1480 de 2011 (consumidor), Ley 527 de 1999 (mensajes de datos como prueba), Ley 23 de 1982 y Decisión Andina 351 de 1993 (derecho de autor).

**Ya cumplido en el código:** autorización previa, expresa e informada con casillas sin marcar y separadas; prueba conservada de cada autorización; política de tratamiento con finalidades, derechos, canales y plazos de consultas y reclamos (10 y 15 días hábiles); datos mínimos; sin datos sensibles ni de menores; revocatoria y supresión; seguridad básica (contraseñas con hash, roles).
Además corregí un hueco serio: `POST /api/register` permitía crearse como `admin` enviando `role`; ahora siempre crea `peasant`.

**Te toca a ti (no se puede resolver con código):**

1. Completar NIT, razón social y dirección en `.env`; si no tienes NIT ni domicilio visibles, la política queda incompleta.
2. Definir quién atiende consultas y reclamos de datos personales y responder en los plazos.
3. **RNBD:** solo deben inscribirse las sociedades y entidades sin ánimo de lucro con activos totales superiores a 100.000 UVT (y entidades públicas); las personas naturales no. Verifica si es tu caso. Aunque no estés obligado, igual debes cumplir todo lo demás.
4. Firmar contratos o cláusulas de encargo con los proveedores que tratan datos por ti (hosting, correo SMTP) y revisar si están fuera de Colombia (la casilla ya cubre transmisión y transferencia, pero conviene el contrato).
5. Usar HTTPS en producción (`APP_ENV=production` ya fuerza el esquema) y tener un procedimiento para incidentes de seguridad (aviso a la SIC cuando corresponda).
6. Tu `.env` tiene credenciales reales de Gmail para enviar correo: no las compartas ni las subas a git, y rótalas si se expusieron.
7. Subir las versiones de los documentos cada vez que cambien, y conservar el historial en git.
8. Si empiezas a vender en línea, a cobrar o a usar analítica/publicidad, volver a revisar reembolsos, cookies y términos.
