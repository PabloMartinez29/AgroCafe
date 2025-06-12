<?php
session_start();
require_once 'config/database.php'; 

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header('Location: login.php');
    exit();
}
$view = isset($_GET['view']) ? $_GET['view'] : 'bienvenida';
$allowed_views = ['bienvenida', 'usuarios', 'compras', 'ventas', 'pagos', 'facturas', 'tipos-cafe', 'cooperativas', 'analisis', 'caja'];
if (!in_array($view, $allowed_views)) {
    $view = 'bienvenida';
}
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroCafé - Administrador</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background: linear-gradient(180deg, #8B4513 0%, #A0522D 100%);
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #8B4513, #A0522D);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            opacity: 0.9;
        }

        .btn {
            background: #8B4513;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.25rem;
        }

        .btn:hover {
            background: #A0522D;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
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

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #8B4513;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .news-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #8B4513;
        }

        .news-item h4 {
            color: #8B4513;
            margin-bottom: 0.5rem;
        }

        .news-item .date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
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

            .form-row {
                grid-template-columns: 1fr;
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
                    <i class="fas fa-user-shield"></i>
                    <?php echo $_SESSION['user_name']; ?>
                </div>
            </div>
            <div class="sidebar-menu">
                <a href="?view=bienvenida" class="menu-item <?php echo $view == 'bienvenida' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Bienvenida
                </a>
                <a href="?view=usuarios" class="menu-item <?php echo $view == 'usuarios' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Usuarios
                </a>
                <a href="?view=caja" class="menu-item <?php echo $view == 'caja' ? 'active' : ''; ?>">
                    <i class="fas fa-cash-register"></i> Gestión de Caja
                </a>
                <a href="?view=compras" class="menu-item <?php echo $view == 'compras' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Compras
                </a>
                <a href="?view=ventas" class="menu-item <?php echo $view == 'ventas' ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i> Ventas
                </a>
                <a href="?view=pagos" class="menu-item <?php echo $view == 'pagos' ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card"></i> Pagos
                </a>
                <a href="?view=facturas" class="menu-item <?php echo $view == 'facturas' ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice"></i> Facturas
                </a>
                <a href="?view=tipos-cafe" class="menu-item <?php echo $view == 'tipos-cafe' ? 'active' : ''; ?>">
                    <i class="fas fa-coffee"></i> Tipos de Café
                </a>
                <a href="?view=cooperativas" class="menu-item <?php echo $view == 'cooperativas' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> Cooperativas
                </a>
                <a href="?view=analisis" class="menu-item <?php echo $view == 'analisis' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Análisis de Ventas
                </a>
            </div>
        </nav>

        <main class="main-content">
            <div class="content-header">
                <h1 id="page-title">
                    <?php
                    $titles = [
                        'bienvenida' => 'Bienvenida',
                        'usuarios' => 'Gestión de Usuarios',
                        'compras' => 'Gestión de Compras',
                        'ventas' => 'Gestión de Ventas',
                        'pagos' => 'Gestión de Pagos',
                        'facturas' => 'Gestión de Facturas',
                        'tipos-cafe' => 'Tipos de Café',
                        'cooperativas' => 'Cooperativas',
                        'analisis' => 'Análisis de ventas',
                        'caja' => 'Gestión de Caja'
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
                <?php 
                $viewFile = "views/admin/{$view}.php";
 
                if ($view === 'tipos-cafe') {
                    $viewFile = "views/admin/tipos_cafe.php";
                }

                if (file_exists($viewFile)) {
                    include $viewFile;
                } else {
                    echo '<div style="text-align: center; padding: 3rem; color: #666;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: #dc3545;"></i>
                        <h3>Vista no encontrada</h3>
                        <p>La vista solicitada no existe o no se puede cargar.</p>
                    </div>';
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>