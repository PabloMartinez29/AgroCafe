@extends('layouts.legal')

@section('title', $document['title'])

@section('content')
    @if (! config('legal.sells_online'))
        <p>
            <strong>AgroCafé no vende productos ni cobra servicios por internet.</strong> La plataforma es una herramienta de gestión: registra
            compras de café a productores, ventas, inventario, facturas y pagos que se acuerdan y se hacen directamente entre las partes,
            fuera de la plataforma. Por eso <strong>no hay compras en línea que reembolsar</strong> ni cobros automáticos a tu tarjeta o cuenta.
        </p>

        <h2>1. Compras y ventas de café</h2>
        <ul>
            <li>Se realizan de forma presencial o por acuerdo directo entre el administrador y el productor, no a través de un comercio electrónico.</li>
            <li>Las condiciones de cada negociación (precio, peso, calidad, forma y fecha de pago) quedan en la factura y en el registro de la compra.</li>
            <li>Si encuentras un error en una factura o en un pago, escríbenos y lo revisamos; si procede, se corrige y se emite el documento correspondiente.</li>
        </ul>

        <h2>2. Uso de la plataforma</h2>
        <p>
            Crear y usar una cuenta no tiene costo. Si más adelante se ofrecen planes de pago o ventas a distancia, esta política
            se actualizará antes de cobrar, incluyendo el derecho de retracto que la ley otorga al consumidor.
        </p>
    @else
        <p>
            Esta política aplica a las compras que hagas a <strong>{{ $controller['name'] }}</strong> por internet u otros medios a distancia.
            Se rige por la Ley 1480 de 2011 (Estatuto del Consumidor).
        </p>

        <h2>1. Derecho de retracto</h2>
        <ul>
            <li>Puedes retractarte de la compra dentro de los <strong>5 días hábiles</strong> siguientes a la entrega del producto o a la contratación del servicio.</li>
            <li>Para ejercerlo escribe a <a href="mailto:{{ $controller['email'] }}">{{ $controller['email'] }}</a> indicando tu nombre y el pedido.</li>
            <li>El producto debe devolverse sin uso y en su empaque original. Los costos de transporte de la devolución corren por tu cuenta.</li>
            <li>Te devolvemos el dinero dentro de los <strong>30 días calendario</strong> siguientes a que nos informes el retracto, por el mismo medio de pago.</li>
            <li>El retracto no aplica a bienes que por su naturaleza no puedan devolverse o sean perecederos, ni a productos hechos a tu medida.</li>
        </ul>

        <h2>2. Garantía y productos defectuosos</h2>
        <p>
            Si el producto llega defectuoso, no corresponde a lo ofrecido o no cumple la calidad prometida, tienes derecho a la garantía legal:
            reparación, cambio o devolución del dinero, según el caso. Escríbenos para gestionarlo.
        </p>

        <h2>3. Cómo se hace el reembolso</h2>
        <p>Usamos el mismo medio de pago de la compra. Si el pago fue en efectivo o transferencia, te pediremos una cuenta a tu nombre.</p>
    @endif

    <h2>Contacto</h2>
    <p>
        <a href="mailto:{{ $controller['email'] }}">{{ $controller['email'] }}</a> · {{ $controller['phone'] }} · {{ $controller['schedule'] }}.
        Si no estás conforme, puedes acudir a la Superintendencia de Industria y Comercio (SIC): www.sic.gov.co.
    </p>
@endsection
