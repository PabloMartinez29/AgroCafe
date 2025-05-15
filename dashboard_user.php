<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está logueado y es user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario - Coffee & CVCC</title>
    <link href="[invalid url, do not cite] rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Coffee & CVCC - Usuario</a>
            <div class="ms-auto">
                <span class="navbar-text me-3">Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-outline-light">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4">Panel de Usuario</h2>
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Tu Perfil</h3>
                <?php
                $user_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT name, mail FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    echo '<p><strong>Nombre:</strong> ' . htmlspecialchars($row['name']) . '</p>';
                    echo '<p><strong>Correo Electrónico:</strong> ' . htmlspecialchars($row['mail']) . '</p>';
                } else {
                    echo '<p class="card-text">Error al cargar el perfil.</p>';
                }
                $stmt->close();
                $conn->close();
                ?>
            </div>
        </div>
    </div>
    <script src="[invalid url, do not cite] integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
