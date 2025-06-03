<?php
session_start();
require_once 'config/database.php';

// Crear tabla de tokens de restablecimiento si no existe
$createTableSQL = "
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX (token),
    INDEX (expires_at)
)";
executeQuery($createTableSQL);

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Por favor ingrese su correo electrónico";
    } else {
 
        $user = fetchOne("SELECT id, nombre, email, activo FROM usuarios WHERE email = ?", [$email]);
        
        if (!$user) {
            $error = "No existe una cuenta con este correo electrónico";
        } elseif (!$user['activo']) {
            $error = "Esta cuenta está desactivada. Contacte al administrador";
        } else {

            $token = bin2hex(random_bytes(32)); 
            

            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            

            $tokenData = [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expiresAt
            ];
            
            if (insertRecord('password_reset_tokens', $tokenData)) {

                $resetUrl = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
                

                $success = true;
                $resetLink = $resetUrl;
                

                
                $to = $user['email'];
                $subject = "Recuperación de contraseña - CaféTrade";
                $message = "
                <html>
                <head>
                    <title>Recuperación de contraseña</title>
                </head>
                <body>
                    <h2>Recuperación de contraseña - CaféTrade</h2>
                    <p>Hola {$user['nombre']},</p>
                    <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                    <p><a href='{$resetUrl}'>Restablecer mi contraseña</a></p>
                    <p>Este enlace expirará en 1 hora.</p>
                    <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                    <p>Saludos,<br>Equipo CaféTrade</p>
                </body>
                </html>
                ";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: noreply@cafetrade.com" . "\r\n";
                
                mail($to, $subject, $message, $headers);
                
            } else {
                $error = "Error al procesar la solicitud. Intente nuevamente";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - CaféTrade</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #8B4513;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #8B4513;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8B4513;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: #8B4513;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #A0522D;
            transform: translateY(-2px);
        }
        
        .reset-link {
            word-break: break-all;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            border: 1px dashed #ddd;
            font-family: monospace;
        }
        
        .info-text {
            color: #666;
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
        </a>
        
        <h2 style="text-align: center; color: #8B4513; margin-bottom: 1.5rem;">
            <i class="fas fa-key"></i> Recuperar Contraseña
        </h2>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <p>Se ha enviado un enlace de recuperación a tu correo electrónico.</p>
                <p>Por favor revisa tu bandeja de entrada y sigue las instrucciones.</p>
                

                <div class="reset-link">
                    <strong>Enlace de recuperación (solo para desarrollo):</strong><br>
                    <a href="<?php echo $resetLink; ?>" target="_blank"><?php echo $resetLink; ?></a>
                </div>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-bottom: 2rem;">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Correo Electrónico:
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="Ingresa tu correo electrónico">
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Enlace de Recuperación
                </button>
            </form>
            
            <p class="info-text">
                <i class="fas fa-info-circle"></i>
                Si no recuerdas tu correo electrónico, contacta al administrador del sistema.
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
