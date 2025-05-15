
<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Coffee & CVCC</title>
    <link href="[invalid url, do not cite] rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Coffee & CVCC - Admin</a>
            <div class="ms-auto">
                <span class="navbar-text me-3">Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-outline-light">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4">Panel de Administración</h2>
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Lista de Usuarios</h3>
                <?php
                $result = $conn->query("SELECT u.id, u.name, u.mail, r.name as role FROM users u JOIN user_role r ON u.id_role = r.id");
                if ($result->num_rows > 0) {
                    echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th></tr></thead><tbody>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr><td>' . $row['id'] . '</td><td>' . htmlspecialchars($row['name']) . '</td><td>' . htmlspecialchars($row['mail']) . '</td><td>' . $row['role'] . '</td></tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<p class="card-text">No hay usuarios registrados.</p>';
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>
    <script src="[invalid url, do not cite] integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
