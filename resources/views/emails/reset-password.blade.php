<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - AgroCafé</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f0e8;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #8b6b4f 0%, #6b523f 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-header .icon {
            font-size: 48px;
            color: #ffffff;
            margin-bottom: 15px;
        }
        .email-body {
            padding: 40px 30px;
            color: #4a392e;
            line-height: 1.6;
        }
        .email-body h2 {
            color: #6b523f;
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .email-body p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #5a4535;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 15px 40px;
            background-color: #8b6b4f;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .reset-button:hover {
            background-color: #6b523f;
        }
        .email-footer {
            background-color: #f5f0e8;
            padding: 30px;
            text-align: center;
            color: #706f6c;
            font-size: 14px;
            border-top: 1px solid #e8ddd0;
        }
        .email-footer p {
            margin: 10px 0;
            color: #706f6c;
        }
        .warning-box {
            background-color: #fff8e1;
            border-left: 4px solid #ffa726;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            color: #e65100;
            font-size: 14px;
        }
        .link-fallback {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f0e8;
            border-radius: 4px;
            word-break: break-all;
        }
        .link-fallback p {
            margin: 0;
            font-size: 12px;
            color: #706f6c;
        }
        .link-fallback a {
            color: #8b6b4f;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">☕</div>
            <h1>AgroCafé</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>Restablecer Contraseña</h2>
            
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en AgroCafé.</p>
            
            <p>Si solicitaste este cambio, haz clic en el botón de abajo para crear una nueva contraseña:</p>

            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">
                    Restablecer Contraseña
                </a>
            </div>

            <div class="warning-box">
                <p><strong>⚠️ Importante:</strong> Este enlace expirará en {{ $count }} minutos por seguridad.</p>
            </div>

            <p>Si no solicitaste el restablecimiento de contraseña, puedes ignorar este correo. Tu contraseña actual seguirá siendo válida.</p>

            <div class="link-fallback">
                <p><strong>Si el botón no funciona, copia y pega este enlace en tu navegador:</strong></p>
                <p><a href="{{ $url }}">{{ $url }}</a></p>
            </div>

            <p>Si tienes problemas, por favor contacta al administrador del sistema.</p>

            <p>Saludos,<br>
            <strong>Equipo AgroCafé</strong></p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>AgroCafé</strong> - Sistema de Gestión de Café</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} AgroCafé. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>

