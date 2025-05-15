<?php


    //Incluir la conexión a la base de datos
    require 'db.php';

    //Verificar si el formulario ha sido enviado
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //Obtener los datos del formulario
        $username = trim($_POST['nombre']);
        $password = trim($_POST['email']);
        $email = $_POST['password'];


        //Validacion del lado del servidor
        $errors = [];

        //Validar nombre
        if(strlen($nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
        }
        //Validar email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Por favor, ingrese un email válido';
        }
        //Validar contraseña
        $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
        if(!preg_match($passwordPattern, $password)){
            $errors[] = 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número';
        }

        //Verificar si el email ya esta registrado
        $stmt = $conn->prepare("SELECT email FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $errors[] = 'El correo electrónico ya está registrado.';
        }
        $stmt->close();

        //Si no hay errores, proceder con el registro
        if(empty($errors)) {
            //Encriptar la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            //Insertar el usuario en la base de datos
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $email, $hashedPassword);

            if($stmt->execute()) {
                //Redireccionar al usuario a la página de login despues del registro exitoso
                header('Location: login.php?success=Usuario registrado correctamente');
                exit();
            } else {
                $errors[] = 'Error al registrar el usuario' .$con->error;
            }

            $stmt->close();
        } 

        if(!empty($errors)) {
            //Si hay errores, mostrarlos en la página de registro
            $errors = implode('<br>', $errors);
        }
    } 
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .error { color:#0f4444;font-size: 0.875rem;}
        .input-focus {transition: all 0.3s ease;}
        .input-focus:focus { border-color:blue; box-shadow: 0 0 0 3px rgba(59, 130, 24, 0.25);}
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Crear Cuenta</h2>

        <?php if(!isset($error_message)) : ?>
            <div class="mb-4 text-center text-red-600 ">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>


        <form action="register.php" method="POST" ID="register-form" class="space-y-6">
            <div>
                <label for="nombre" class="block  text-sm font-medium text-gray-700 font-semibold">Nombre</label>
                <input 
                type="text" 
                name="nombre" 
                id="nombre"
                placeholder="Nombre" 
                required 
                class="input-focus w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p id="nombreError" class="error hidden"></p>
            </div>
            <div>
                <label for="email" class="block  text-sm font-medium text-gray-700 font-semibold">Email</label>
                <input 
                type="email" 
                name="email" 
                id="email" 

            </div>
    </div>
    
</body>
</html>