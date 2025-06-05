<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        // Buscar usuario en la base de datos
        $sql = "SELECT id, nombre, email, password, rol, activo FROM usuarios WHERE email = ? AND activo = 1";
        $user = fetchOne($sql, [$email]);
        
        if ($user && $user['password'] === $password) {
            // Login exitoso
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['user_name'] = $user['nombre'];
            
            // Registrar último acceso
            $updateSql = "UPDATE usuarios SET fecha_registro = CURRENT_TIMESTAMP WHERE id = ?";
            executeQuery($updateSql, [$user['id']]);
            
            // Redireccionar según el rol
            if ($user['rol'] === 'administrador') {
                header('Location: dashboard_admin.php');
            } else {
                header('Location: dashboard_campesino.php');
            }
            exit();
        } else {
            $error = "Credenciales incorrectas o usuario inactivo";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AgroCafé</title>
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
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
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
            padding: 0.75rem;
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
            margin-bottom: 1rem;
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
        }

        .btn:hover {
            background: #A0522D;
        }

        .test-users {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .test-users h4 {
            margin: 0 0 0.5rem 0;
            color: #8B4513;
        }

        .test-users p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al inicio
        </a>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                Registro exitoso. Puedes iniciar sesión ahora.
            </div>
        <?php endif; ?>
        
        <h2 style="text-align: center; color: #8B4513; margin-bottom: 1.5rem;">
            <i class="fas fa-coffee"></i> Iniciar Sesión
        </h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Correo Electrónico:
                </label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Contraseña:
                </label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i>
                Ingresar
            </button>

            <p style="text-align: center; margin-top: 0.75rem;">
                <a href="forgot_password.php" style="color: #8B4513; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-question-circle"></i> ¿Olvidaste tu contraseña?
                </a>
            </p>
        </form>
        


        <p style="text-align: center; margin-top: 1rem;">
            ¿No tienes cuenta? 
            <a href="register.php" style="color: #8B4513; text-decoration: none;">
                <i class="fas fa-user-plus"></i> Regístrate aquí
            </a>
        </p>
    </div>
</body>
</html>
