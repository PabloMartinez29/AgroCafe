<ul>
    <li><strong>Nombre:</strong> {{ $controller['name'] }}</li>
    @if ($controller['nit'])
        <li><strong>NIT:</strong> {{ $controller['nit'] }}</li>
    @endif
    @if ($controller['address'])
        <li><strong>Domicilio:</strong> {{ $controller['address'] }}, {{ $controller['city'] }}</li>
    @else
        <li><strong>País:</strong> {{ $controller['city'] }}</li>
    @endif
    <li><strong>Correo:</strong> <a href="mailto:{{ $controller['privacy_email'] }}">{{ $controller['privacy_email'] }}</a></li>
    <li><strong>Teléfono:</strong> {{ $controller['phone'] }}</li>
    <li><strong>Horario de atención:</strong> {{ $controller['schedule'] }}</li>
</ul>
