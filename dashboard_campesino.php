<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'campesino') {
    header('Location: login.php');
    exit();
}

$view = isset($_GET['view']) ? $_GET['view'] : 'historial';
$allowed_views = ['historial', 'pagos', 'facturas', 'analisis'];

if (!in_array($view, $allowed_views)) {
    $view = 'historial';
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroCafé - Campesino</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #228B22 0%, #32CD32 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header .user-info {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-item {
            display: block;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            padding-left: 2rem;
        }

        .menu-item i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .content-header {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-area {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-height: 500px;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-header {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-coffee"></i> AgroCafé</h2>
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <?php echo $_SESSION['user_name']; ?>
                </div>
            </div>
            <div class="sidebar-menu">
                <a href="?view=historial" class="menu-item <?php echo $view == 'historial' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> Historial de Ventas
                </a>
                <a href="?view=pagos" class="menu-item <?php echo $view == 'pagos' ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card"></i> Estado de Pagos
                </a>
                <a href="?view=facturas" class="menu-item <?php echo $view == 'facturas' ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice"></i> Mis Facturas
                </a>
                <a href="?view=analisis" class="menu-item <?php echo $view == 'analisis' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Análisis de Precios
                </a>
            </div>
        </nav>

        <main class="main-content">
            <div class="content-header">
                <h1>
                    <?php
                    $titles = [
                        'historial' => 'Historial de Ventas',
                        'pagos' => 'Estado de Pagos',
                        'facturas' => 'Mis Facturas',
                        'analisis' => 'Análisis de Precios'
                    ];
                    echo $titles[$view];
                    ?>
                </h1>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>

            <div class="content-area">
                <?php include "views/campesino/{$view}.php"; ?>
            </div>
        </main>
    </div>
</body>
</html>
<?php
ob_end_flush(); 
