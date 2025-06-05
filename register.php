<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    
    if (empty($nombre) || empty($email) || empty($telefono) || empty($direccion) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } else {
        
        $checkEmail = "SELECT id FROM usuarios WHERE email = ?";
        $existingUser = fetchOne($checkEmail, [$email]);
        
        if ($existingUser) {
            $error = "El correo electrónico ya está registrado";
        } else {
           
            $userData = [
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'password' => $password, 
                'rol' => 'campesino'
            ];
            
            if (insertRecord('usuarios', $userData)) {
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error = "Error al registrar el usuario. Intente nuevamente.";
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
    <title>Registro - CaféTrade</title>
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
            padding: 2rem 0;
        }
        
        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
    
            
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

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al inicio
        </a>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success">
                <?php echo $success; ?>
                <br><a href="login.php" style="color: #155724;">Ir al login</a>
            </div>
        <?php endif; ?>
        
        <h2 style="text-align: center; color: #8B4513; margin-bottom: 1.5rem;">
            <i class="fas fa-user-plus"></i> Registro de Campesino
        </h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-user"></i> Nombre Completo:
                </label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Correo Electrónico:
                </label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">
                        <i class="fas fa-phone"></i> Teléfono:
                    </label>
                    <input type="tel" id="telefono" name="telefono" required 
                           value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="direccion">
                        <i class="fas fa-map-marker-alt"></i> Dirección:
                    </label>
                    <input type="text" id="direccion" name="direccion" required 
                           value="<?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row ml">
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Contraseña:
                    </label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirmar Contraseña:
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
            </div>
            

            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i>
                Registrarse
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            ¿Ya tienes cuenta? 
            <a href="login.php" style="color: #8B4513; text-decoration: none;">
                <i class="fas fa-sign-in-alt"></i> Inicia sesión aquí
            </a>
        </p>
    </div>
</body>
</html>
