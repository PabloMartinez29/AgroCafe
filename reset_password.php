<?php
session_start();
require_once 'config/database.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$validToken = false;
$tokenInfo = null;
$error = null;
$success = false;

// Verificar si el token es válido
if (!empty($token)) {
    // Buscar token en la base de datos
    $tokenInfo = fetchOne("
        SELECT t.*, u.id as user_id, u.nombre, u.email 
        FROM password_reset_tokens t
        JOIN usuarios u ON t.user_id = u.id
        WHERE t.token = ? AND t.used = 0 AND t.expires_at > NOW() AND u.activo = 1
    ", [$token]);
    
    if ($tokenInfo) {
        $validToken = true;
    } else {
        // Verificar si el token existe pero está expirado o usado
        $expiredToken = fetchOne("
            SELECT t.*, u.nombre 
            FROM password_reset_tokens t
            JOIN usuarios u ON t.user_id = u.id
            WHERE t.token = ?
        ", [$token]);
        
        if ($expiredToken) {
            if ($expiredToken['used']) {
                $error = "Este enlace ya ha sido utilizado. Si necesitas restablecer tu contraseña nuevamente, solicita un nuevo enlace.";
            } else {
                $error = "Este enlace ha expirado. Por favor solicita un nuevo enlace de recuperación.";
            }
        } else {
            $error = "El enlace de recuperación no es válido.";
        }
    }
}

// Procesar formulario de restablecimiento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $validToken) {
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    
    // Validar contraseña
    if (empty($password)) {
        $error = "Por favor ingrese una contraseña";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } elseif ($password !== $confirmPassword) {
        $error = "Las contraseñas no coinciden";
    } else {
        // Hash de la contraseña (si tu sistema usa hash)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar contraseña (usar hash si tu sistema lo requiere, sino usar $password directamente)
        $updatePassword = updateRecord('usuarios', 
            ['password' => $password], // Cambiar a $hashedPassword si usas hash
            'id = ?', 
            [$tokenInfo['user_id']]
        );
        
        // Marcar token como usado
        $markTokenUsed = updateRecord('password_reset_tokens',
            ['used' => 1],
            'id = ?',
            [$tokenInfo['id']]
        );
        
        if ($updatePassword && $markTokenUsed) {
            $success = true;
            
            // Log del cambio exitoso
            error_log("Password successfully reset for user ID: " . $tokenInfo['user_id'] . " (" . $tokenInfo['email'] . ")");
            
            // Opcional: Invalidar todas las sesiones del usuario
            // session_destroy();
            
        } else {
            $error = "Error al actualizar la contraseña. Intente nuevamente";
            error_log("Failed to update password for user ID: " . $tokenInfo['user_id']);
        }
    }
}

// Limpiar tokens expirados (mantenimiento automático)
executeQuery("DELETE FROM password_reset_tokens WHERE expires_at < NOW() OR (used = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR))");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - CaféTrade</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
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
            padding: 1rem;
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
        
        .password-strength {
            height: 5px;
            margin-top: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            background: #e9ecef;
        }
        
        .strength-weak {
            background: linear-gradient(to right, #dc3545 30%, #e9ecef 30%);
        }
        
        .strength-medium {
            background: linear-gradient(to right, #ffc107 60%, #e9ecef 60%);
        }
        
        .strength-strong {
            background: linear-gradient(to right, #28a745 100%, #e9ecef 100%);
        }
        
        .password-match {
            font-size: 0.9rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }
        
        .match-error {
            color: #dc3545;
        }
        
        .match-success {
            color: #28a745;
        }
        
        .password-requirements {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border-left: 4px solid #8B4513;
        }
        
        .password-requirements ul {
            margin: 0.5rem 0 0 1.5rem;
            padding: 0;
        }
        
        .password-requirements li {
            margin-bottom: 0.3rem;
        }
        
        .user-info {
            background: #e8f5e8;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            border-left: 4px solid #28a745;
        }
        
        .success-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
        </a>
        
        <h2 style="text-align: center; color: #8B4513; margin-bottom: 1.5rem;">
            <i class="fas fa-lock"></i> Restablecer Contraseña
        </h2>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <p><?php echo $error; ?></p>
                <?php if (strpos($error, 'expirado') !== false || strpos($error, 'utilizado') !== false): ?>
                    <a href="forgot_password.php" class="btn" style="margin-top: 1rem;">
                        <i class="fas fa-key"></i> Solicitar Nuevo Enlace
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">¡Contraseña Actualizada!</h3>
                <p><strong>Tu contraseña ha sido restablecida exitosamente.</strong></p>
                <p>Ya puedes iniciar sesión con tu nueva contraseña.</p>
                
                <a href="login.php" class="btn" style="margin-top: 1.5rem;">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
            </div>
        <?php elseif ($validToken): ?>
            <div class="user-info">
                <i class="fas fa-user-circle" style="font-size: 2rem; color: #28a745;"></i>
                <p style="margin: 0.5rem 0 0 0;">
                    <strong>Hola <?php echo htmlspecialchars($tokenInfo['nombre']); ?></strong><br>
                    <small><?php echo htmlspecialchars($tokenInfo['email']); ?></small>
                </p>
            </div>
            
            <p style="text-align: center; margin-bottom: 1.5rem; color: #666;">
                Ingresa tu nueva contraseña a continuación:
            </p>
            
            <div class="password-requirements">
                <strong><i class="fas fa-info-circle"></i> Requisitos de contraseña:</strong>
                <ul>
                    <li>Al menos 6 caracteres de longitud</li>
                    <li>Se recomienda incluir letras y números</li>
                    <li>Evita usar información personal</li>
                </ul>
            </div>
            
            <form method="POST" id="passwordForm">
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Nueva Contraseña:
                    </label>
                    <input type="password" id="password" name="password" required minlength="6"
                           onkeyup="checkPasswordStrength(); checkPasswordMatch();"
                           placeholder="Ingresa tu nueva contraseña">
                    <div class="password-strength" id="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirmar Contraseña:
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                           onkeyup="checkPasswordMatch();"
                           placeholder="Confirma tu nueva contraseña">
                    <div class="password-match" id="password-match"></div>
                </div>
                
                <button type="submit" class="btn" id="submitBtn">
                    <i class="fas fa-save"></i>
                    Guardar Nueva Contraseña
                </button>
            </form>
        <?php else: ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>El enlace de recuperación no es válido o ha expirado.</p>
                <a href="forgot_password.php" class="btn" style="margin-top: 1rem;">
                    <i class="fas fa-key"></i> Solicitar Nuevo Enlace
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('password-strength');
            
            // Eliminar clases existentes
            strengthBar.classList.remove('strength-weak', 'strength-medium', 'strength-strong');
            
            if (password.length === 0) {
                strengthBar.style.display = 'none';
                return;
            }
            
            strengthBar.style.display = 'block';
            
            // Evaluar fortaleza
            let strength = 0;
            
            // Longitud
            if (password.length >= 6) strength += 1;
            if (password.length >= 10) strength += 1;
            
            // Complejidad
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[a-z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Asignar clase según fortaleza
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchStatus = document.getElementById('password-match');
            const submitBtn = document.getElementById('submitBtn');
            
            if (confirmPassword.length === 0) {
                matchStatus.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchStatus.textContent = '✓ Las contraseñas coinciden';
                matchStatus.classList.remove('match-error');
                matchStatus.classList.add('match-success');
                submitBtn.disabled = false;
            } else {
                matchStatus.textContent = '✗ Las contraseñas no coinciden';
                matchStatus.classList.remove('match-success');
                matchStatus.classList.add('match-error');
                submitBtn.disabled = true;
            }
        }
        
        // Prevenir envío múltiple
        document.getElementById('passwordForm')?.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        });
    </script>
</body>
</html>
