@extends('layouts.legal')

@section('title', $document['title'])

@section('content')
    <p>
        Esta política explica qué datos personales recoge <strong>{{ $controller['name'] }}</strong>, para qué los usa, cuánto tiempo los guarda
        y cómo puedes ejercer tus derechos. Se expide en cumplimiento del artículo 15 de la Constitución Política, la Ley 1581 de 2012,
        el Decreto 1377 de 2013 (compilado en el Decreto 1074 de 2015) y las demás normas que los modifiquen.
    </p>

    <h2>1. Responsable del tratamiento</h2>
    @include('legal._controller')

    <h2>2. Qué datos recogemos</h2>
    <p>Solo pedimos lo necesario para prestar el servicio:</p>
    <ul>
        <li><strong>Al registrarte:</strong> nombre, correo electrónico y contraseña (se guarda cifrada, nunca en texto plano).</li>
        <li><strong>Opcionales:</strong> teléfono, dirección y foto de perfil. Puedes dejarlos vacíos.</li>
        <li><strong>Datos de la operación:</strong> compras de café, pesos, precios, facturas y pagos asociados a tu cuenta.</li>
        <li><strong>Cooperativas:</strong> nombre, NIT, teléfono, correo, dirección y representante legal.</li>
        <li><strong>Datos técnicos:</strong> dirección IP, navegador y fecha y hora de acceso, para la seguridad de la cuenta y para la prueba de tu autorización.</li>
    </ul>
    <p>
        No solicitamos datos sensibles (salud, origen étnico, datos biométricos, etc.) ni datos de menores de edad. Si crees que un menor
        creó una cuenta, escríbenos y la eliminaremos.
    </p>

    <h2>3. Para qué usamos tus datos</h2>
    <ul>
        <li>Crear y administrar tu cuenta y autenticarte.</li>
        <li>Registrar compras y ventas, generar y enviar facturas y controlar pagos y bodega.</li>
        <li>Mostrarte tu historial y el análisis de precios.</li>
        <li>Contactarte por asuntos del servicio (por ejemplo, facturas o recuperación de contraseña).</li>
        <li>Cumplir obligaciones legales, contables y tributarias.</li>
        <li>Garantizar la seguridad del sistema y probar que otorgaste tu autorización.</li>
    </ul>
    <p>No vendemos tus datos ni los usamos para publicidad de terceros.</p>

    <h2>4. Tu autorización</h2>
    <p>
        Tratamos tus datos con tu autorización previa, expresa e informada, que te pedimos en una casilla propia, sin marcar de antemano
        y separada de los Términos y Condiciones. Si un administrador crea tu cuenta, tendrás que dar tu autorización tú mismo la primera
        vez que ingreses.
    </p>
    <p>
        Para poder demostrarla (Decreto 1377 de 2013) guardamos, sin posibilidad de modificarlos: la fecha y hora, el documento y su versión,
        una huella digital del texto que viste, el texto exacto de la casilla, tu IP y navegador, el canal por el que aceptaste y, si
        lo marcó un administrador, quién fue. Puedes pedirnos copia de ese registro.
    </p>

    <h2>5. Tus derechos</h2>
    <ul>
        <li>Conocer, actualizar y rectificar tus datos.</li>
        <li>Solicitar prueba de la autorización que nos diste.</li>
        <li>Ser informado sobre el uso que se ha dado a tus datos.</li>
        <li>Presentar quejas ante la Superintendencia de Industria y Comercio (SIC) por infracciones a la ley.</li>
        <li>Revocar la autorización y/o solicitar la supresión de tus datos cuando no se respeten los principios o garantías, o cuando ya no exista un deber legal o contractual de conservarlos.</li>
        <li>Acceder de forma gratuita a tus datos personales.</li>
    </ul>

    <h2>6. Cómo ejercer tus derechos</h2>
    <p>
        Escribe a <a href="mailto:{{ $controller['privacy_email'] }}">{{ $controller['privacy_email'] }}</a> indicando tu nombre, tu correo registrado y
        lo que solicitas.
    </p>
    <ul>
        <li><strong>Consultas:</strong> las respondemos en máximo 10 días hábiles. Si necesitamos más tiempo, te lo avisamos y no pasará de 5 días hábiles adicionales.</li>
        <li><strong>Reclamos</strong> (corrección, actualización, supresión o revocatoria): los respondemos en máximo 15 días hábiles, prorrogables por 8 días hábiles más con aviso previo. Si el reclamo está incompleto, te pediremos completarlo dentro de los 5 días siguientes.</li>
        <li>Puedes eliminar tu cuenta desde tu perfil. Dejaremos constancia de la revocatoria.</li>
    </ul>
    <p>La supresión no procede cuando exista un deber legal o contractual de conservar la información.</p>

    <h2>7. Con quién compartimos los datos</h2>
    <p>
        Usamos proveedores que tratan datos en nuestro nombre (encargados): alojamiento del servidor, envío de correo electrónico y recursos
        técnicos de terceros que carga la página (tipografías, iconos y estilos desde servicios como Bunny Fonts, Cloudflare/cdnjs
        y Tailwind CSS), los cuales reciben tu IP y datos técnicos del navegador. Algunos pueden estar fuera de Colombia; por eso la casilla
        de autorización incluye la transmisión y transferencia a proveedores dentro o fuera del país. También entregaremos datos a
        autoridades cuando la ley lo exija. Fuera de esos casos no los compartimos.
    </p>

    <h2>8. Seguridad</h2>
    <p>
        Aplicamos medidas razonables: contraseñas cifradas, acceso por roles, sesiones protegidas, conexión cifrada (HTTPS) en producción y un
        registro de autorizaciones protegido contra alteraciones. Ningún sistema es 100% seguro; si ocurre un incidente que afecte tus datos,
        lo gestionaremos y lo informaremos a la SIC cuando corresponda.
    </p>

    <h2>9. Cuánto tiempo los conservamos</h2>
    <ul>
        <li>Datos de la cuenta: mientras tu cuenta esté activa y hasta que solicites su supresión.</li>
        <li>Facturas y registros contables: durante los plazos que exigen las normas comerciales y tributarias, aunque cierres tu cuenta.</li>
        <li>Registro de autorizaciones: mientras dure la relación y, después, el tiempo necesario para atender reclamaciones o requerimientos de autoridades. Se conserva incluso si eliminas tu cuenta.</li>
    </ul>

    <h2>10. Cookies</h2>
    <p>Usamos solo cookies técnicas necesarias. Lee la <a href="{{ route('legal.cookies') }}">Política de Cookies</a>.</p>

    <h2>11. Cambios en esta política</h2>
    <p>
        Cada versión tiene número y fecha de vigencia. Si cambia de forma relevante, te pediremos de nuevo tu autorización antes de seguir
        usando la plataforma. Conservamos las versiones anteriores.
    </p>

    <h2>12. Autoridad de control</h2>
    <p>
        Superintendencia de Industria y Comercio (SIC) — Delegatura para la Protección de Datos Personales: www.sic.gov.co.
    </p>
@endsection
