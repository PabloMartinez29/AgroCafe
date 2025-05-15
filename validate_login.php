<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($mail) || empty($password)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos.';
        header("Location: login.php");
        exit();
    }

    // Consulta preparada para obtener el usuario
    $stmt = $conn->prepare("SELECT id, name, password, id_role FROM users WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Obtener el rol del usuario
            $role_stmt = $conn->prepare("SELECT name FROM user_role WHERE id = ?");
            $role_stmt->bind_param("i", $user['id_role']);
            $role_stmt->execute();
            $role_result = $role_stmt->get_result();
            $role = $role_result->fetch_assoc()['name'];

            // Establecer variables de sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $role;

            // Redirigir según el rol
            if ($role === 'admin') {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
        } else {
            $_SESSION['error'] = 'Contraseña incorrecta.';
            header("Location: login.php");
        }
    } else {
        $_SESSION['error'] = 'Usuario no encontrado.';
        header("Location: login.php");
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = 'Método no permitido.';
    header("Location: login.php");
}
exit();
?>
