@extends('layouts.legal')

@section('title', $document['title'])

@section('content')
    <p>
        Estos Términos y Condiciones regulan el uso de la plataforma web y de la API de <strong>{{ $controller['name'] }}</strong>
        (en adelante, "AgroCafé"), un sistema de gestión para la compra, venta e inventario de café. Al crear una cuenta
        o usar la plataforma aceptas estas condiciones. Si no estás de acuerdo, no la uses.
    </p>

    <h2>1. Quién ofrece la plataforma</h2>
    @include('legal._controller')

    <h2>2. Quiénes pueden usarla</h2>
    <ul>
        <li><strong>Administrador:</strong> gestiona usuarios, caja, compras, ventas, bodega, facturas, pagos y cooperativas.</li>
        <li><strong>Campesino o productor:</strong> consulta sus compras, sus facturas y el análisis de precios.</li>
        <li><strong>Visitante:</strong> puede ver la página pública y los precios de referencia sin crear cuenta.</li>
    </ul>
    <p>Para crear una cuenta debes ser mayor de 18 años y tener capacidad legal para contratar.</p>

    <h2>3. Tu cuenta</h2>
    <ul>
        <li>Debes dar datos verdaderos y mantenerlos actualizados.</li>
        <li>Tus credenciales son personales. No las compartas; eres responsable de lo que se haga con ellas.</li>
        <li>Si un administrador crea tu cuenta, deberás aceptar estos términos y la Política de Privacidad la primera vez que ingreses.</li>
        <li>Avísanos de inmediato si crees que alguien más accedió a tu cuenta.</li>
    </ul>

    <h2>4. Uso permitido</h2>
    <p>Te comprometes a usar la plataforma de forma lícita. No está permitido:</p>
    <ul>
        <li>Acceder a información de otros usuarios o intentar saltarte los permisos de tu rol.</li>
        <li>Introducir datos falsos, o datos personales de terceros sin su autorización.</li>
        <li>Interferir con el funcionamiento del sistema, probar su seguridad sin permiso escrito o extraer datos de forma masiva.</li>
        <li>Subir como foto de perfil imágenes que no sean tuyas o sobre las que no tengas derechos, o que muestren a otra persona sin su permiso.</li>
        <li>Copiar, vender o redistribuir el software, la marca o los contenidos.</li>
    </ul>

    <h2>5. Qué hace y qué no hace la plataforma</h2>
    <ul>
        <li>La plataforma <strong>registra</strong> compras, ventas, movimientos de bodega, facturas y pagos con la información que ingresa el administrador.</li>
        <li><strong>No procesa pagos en línea</strong> ni vende productos por internet. Los pagos y la entrega del café se acuerdan y realizan fuera de la plataforma (ver <a href="{{ route('legal.refund') }}">Política de Reembolsos</a>).</li>
        <li>Los precios del mercado que se muestran son <strong>de referencia</strong>, provienen de fuentes externas y pueden variar o no estar actualizados. No constituyen una oferta ni un precio garantizado.</li>
        <li>Los textos de la sección de consejos son orientativos y no reemplazan la asesoría de un técnico agrónomo.</li>
    </ul>

    <h2>6. Propiedad intelectual</h2>
    <p>
        El software, el diseño, la marca AgroCafé y los contenidos de la plataforma pertenecen a su titular o se usan con licencia, y están
        protegidos por la Ley 23 de 1982 y demás normas de propiedad intelectual. Algunas imágenes de la página pública son ilustrativas
        y pueden haber sido generadas con herramientas de inteligencia artificial. Los datos que tú ingresas siguen siendo tuyos;
        nos autorizas a tratarlos solo para prestarte el servicio.
    </p>

    <h2>7. Datos personales</h2>
    <p>
        Tratamos tus datos conforme a la <a href="{{ route('legal.privacy') }}">Política de Privacidad</a> y a la Ley 1581 de 2012.
        La autorización de tratamiento de datos se solicita en una casilla aparte de la aceptación de estos términos.
        Las <a href="{{ route('legal.cookies') }}">cookies</a> se explican en su propia política.
    </p>

    <h2>8. Disponibilidad y responsabilidad</h2>
    <ul>
        <li>Hacemos lo razonable para mantener la plataforma disponible y segura, pero no garantizamos que funcione sin interrupciones ni errores.</li>
        <li>Podemos suspender el servicio por mantenimiento, seguridad o causas ajenas a nuestro control.</li>
        <li>No respondemos por pérdidas causadas por datos incorrectos ingresados por los usuarios, por el uso indebido de credenciales, ni por fallas de internet o de terceros.</li>
        <li>Nada de lo anterior limita tus derechos irrenunciables como consumidor ni nuestra responsabilidad por dolo o culpa grave.</li>
    </ul>

    <h2>9. Suspensión y terminación</h2>
    <p>
        Puedes dejar de usar la plataforma y eliminar tu cuenta desde tu perfil en cualquier momento. Podemos suspender o cerrar una cuenta
        que incumpla estos términos o la ley. Algunos datos se conservan después del cierre cuando una norma lo exija o para poder
        demostrar tu autorización (ver Política de Privacidad).
    </p>

    <h2>10. Peticiones, quejas y reclamos</h2>
    <p>
        Escríbenos a <a href="mailto:{{ $controller['email'] }}">{{ $controller['email'] }}</a> o llámanos al {{ $controller['phone'] }}.
        Si no estás conforme con la respuesta, puedes acudir a la Superintendencia de Industria y Comercio (SIC): www.sic.gov.co.
    </p>

    <h2>11. Cambios en estos términos</h2>
    <p>
        Podemos actualizar estos términos. Cada versión tiene número y fecha de vigencia. Si el cambio es relevante, te pediremos aceptar
        la nueva versión antes de seguir usando la plataforma. Conservamos las versiones anteriores.
    </p>

    <h2>12. Ley aplicable</h2>
    <p>
        Estos términos se rigen por las leyes de la República de Colombia. Cualquier controversia se someterá a los jueces
        colombianos competentes, sin perjuicio de las acciones que correspondan ante la SIC.
    </p>
@endsection
