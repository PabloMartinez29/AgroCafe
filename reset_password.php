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
        $error = "El enlace de recuperación no es válido o ha expirado";
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
        // Actualizar contraseña
        $updatePassword = updateRecord('usuarios', 
            ['password' => $password], 
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
        } else {
            $error = "Error al actualizar la contraseña. Intente nuevamente";
        }
    }
}

// Limpiar tokens expirados (mantenimiento)
executeQuery("DELETE FROM password_reset_tokens WHERE expires_at < NOW() AND used = 0");
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
        
        .password-strength {
            height: 5px;
            margin-top: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .strength-weak {
            background: #dc3545;
            width: 30%;
        }
        
        .strength-medium {
            background: #ffc107;
            width: 60%;
        }
        
        .strength-strong {
            background: #28a745;
            width: 100%;
        }
        
        .password-match {
            font-size: 0.9rem;
            margin-top: 0.5rem;
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
        }
        
        .password-requirements ul {
            margin: 0.5rem 0 0 1.5rem;
            padding: 0;
        }
        
        .password-requirements li {
            margin-bottom: 0.3rem;
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
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <p>¡Tu contraseña ha sido actualizada exitosamente!</p>
                <p>Ahora puedes iniciar sesión con tu nueva contraseña.</p>
                <a href="login.php" class="btn" style="margin-top: 1rem;">
                    <i class="fas fa-sign-in-alt"></i> Ir a Iniciar Sesión
                </a>
            </div>
        <?php elseif ($validToken): ?>
            <p style="text-align: center; margin-bottom: 1.5rem;">
                Hola <strong><?php echo htmlspecialchars($tokenInfo['nombre']); ?></strong>, 
                ingresa tu nueva contraseña a continuación.
            </p>
            
            <div class="password-requirements">
                <strong><i class="fas fa-info-circle"></i> Requisitos de contraseña:</strong>
                <ul>
                    <li>Al menos 6 caracteres de longitud</li>
                    <li>Se recomienda incluir letras mayúsculas y minúsculas</li>
                    <li>Se recomienda incluir números y símbolos</li>
                </ul>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Nueva Contraseña:
                    </label>
                    <input type="password" id="password" name="password" required minlength="6"
                           onkeyup="checkPasswordStrength(); checkPasswordMatch();">
                    <div class="password-strength" id="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirmar Contraseña:
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                           onkeyup="checkPasswordMatch();">
                    <div class="password-match" id="password-match"></div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    Guardar Nueva Contraseña
                </button>
            </form>
        <?php else: ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>El enlace de recuperación no es válido o ha expirado.</p>
                <p>Por favor solicita un nuevo enlace de recuperación.</p>
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
            
            if (confirmPassword.length === 0) {
                matchStatus.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchStatus.textContent = '✓ Las contraseñas coinciden';
                matchStatus.classList.remove('match-error');
                matchStatus.classList.add('match-success');
            } else {
                matchStatus.textContent = '✗ Las contraseñas no coinciden';
                matchStatus.classList.remove('match-success');
                matchStatus.classList.add('match-error');
            }
        }
    </script>
</body>
</html>
