<?php
session_start();
require_once 'config/database.php';
require_once 'lib/email_sender.php';

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
        // Buscar usuario
        $user = fetchOne("SELECT id, nombre, email, activo FROM usuarios WHERE email = ?", [$email]);
        
        if (!$user) {
            $error = "No existe una cuenta con este correo electrónico";
        } elseif (!$user['activo']) {
            $error = "Esta cuenta está desactivada. Contacte al administrador";
        } else {
            // Generar token seguro
            $token = bin2hex(random_bytes(32)); 
            
            // Establecer expiración (1 hora)
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en base de datos
            $tokenData = [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expiresAt
            ];
            
            if (insertRecord('password_reset_tokens', $tokenData)) {
                // Construir URL de reset
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $currentDir = dirname($_SERVER['REQUEST_URI']);
                $resetUrl = $protocol . "://" . $host . $currentDir . "/reset_password.php?token=" . $token;
                
                // USAR EmailSender en lugar de mail()
                try {
                    $emailSender = new EmailSender();
                    $emailSent = $emailSender->enviarRecuperacionPassword($user, $resetUrl);
                    
                    if ($emailSent) {
                        $success = true;
                        error_log("Password reset email sent successfully to: " . $email);
                    } else {
                        $error = "Error al enviar el correo. Por favor contacte al administrador";
                        error_log("Failed to send password reset email to: " . $email);
                    }
                } catch (Exception $e) {
                    $error = "Error al enviar el correo: " . $e->getMessage();
                    error_log("Exception sending password reset email: " . $e->getMessage());
                }
                
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
            border-left: 4px solid #dc3545;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
            border-left: 4px solid #28a745;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #8B4513;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            text-decoration: underline;
            color: #A0522D;
        }

        .page-title {
            text-align: center;
            color: #8B4513;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 600;
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
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
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
            text-decoration: none;
        }

        .btn:hover {
            background: #A0522D;
            transform: translateY(-2px);
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .info-text {
            color: #666;
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
            line-height: 1.5;
        }
        
        .email-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        
        .description-text {
            text-align: center;
            margin-bottom: 2rem;
            color: #666;
            line-height: 1.5;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .btn-centered {
            display: inline-block;
            width: auto;
            min-width: 200px;
            padding: 1rem 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
        </a>
        
        <h2 class="page-title">
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
                <div class="email-icon">
                    <i class="fas fa-envelope-circle-check"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">¡Correo Enviado!</h3>
                <p><strong>Se ha enviado un enlace de recuperación a tu correo electrónico.</strong></p>
                <p>Por favor:</p>
                <ol style="text-align: left; margin: 1rem 0;">
                    <li>Revisa tu bandeja de entrada</li>
                    <li>Busca el correo de "CaféTrade"</li>
                    <li>Haz clic en el enlace de recuperación</li>
                    <li>Sigue las instrucciones para crear tu nueva contraseña</li>
                </ol>
                <p style="font-size: 0.9rem; margin-top: 1rem;">
                    <i class="fas fa-clock"></i> El enlace expirará en <strong>1 hora</strong>
                </p>
                <p style="font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Si no ves el correo, revisa tu carpeta de spam
                </p>
            </div>
            
            <div class="btn-container">
                <a href="login.php" class="btn btn-centered">
                    <i class="fas fa-sign-in-alt"></i> Volver al Login
                </a>
            </div>
        <?php else: ?>
            <p class="description-text">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
            
            <form method="POST" id="resetForm">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Correo Electrónico:
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="Ingresa tu correo electrónico">
                </div>
                
                <button type="submit" class="btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Enlace de Recuperación
                </button>
            </form>
            
            <p class="info-text">
                <i class="fas fa-shield-alt"></i>
                Tu información está segura. Solo enviaremos el enlace al correo registrado en tu cuenta.
            </p>
            
            <p class="info-text">
                <i class="fas fa-question-circle"></i>
                ¿No recuerdas tu correo? Contacta al administrador del sistema.
            </p>
        <?php endif; ?>
    </div>
    
    <script>
        document.getElementById('resetForm')?.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        });
    </script>
</body>
</html>
