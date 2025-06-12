<?php
require_once 'config/database.php';

// Manejo del endpoint refresh
if (isset($_GET['view']) && $_GET['view'] === 'ventas' && isset($_GET['action']) && $_GET['action'] === 'refresh') {
    $hoy = date('Y-m-d');
    $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE estado = 'completada'")['total'] ?? 0;
    $kilos_vendidos = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE estado = 'completada'")['total'] ?? 0;
    $kilos_disponibles = $kilos_comprados - $kilos_vendidos;
    
    $valor_invertido_dia = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) = ?", [$hoy])['total'] ?? 0;
    $saldo_recuperado_dia = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) = ?", [$hoy])['total'] ?? 0;
    $recuperado_dia = min($valor_invertido_dia, $saldo_recuperado_dia);
    $ganancias_dia = max(0, $saldo_recuperado_dia - $valor_invertido_dia);
    $margen_ganancias_dia = ($valor_invertido_dia > 0) ? (($ganancias_dia / $valor_invertido_dia) * 100) : 0;
    $kilos_vendidos_dia = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) = ?", [$hoy])['total'] ?? 0;

    header('Content-Type: application/json');
    echo json_encode([
        'kilos_disponibles' => $kilos_disponibles,
        'valor_invertido_dia' => $valor_invertido_dia,
        'recuperado_dia' => $recuperado_dia,
        'ganancias_dia' => $ganancias_dia,
        'margen_ganancias_dia' => $margen_ganancias_dia,
        'kilos_vendidos_dia' => $kilos_vendidos_dia
    ]);
    exit;
}

// Lógica para incluir las vistas desde views/admin/
if (isset($_GET['view'])) {
    $view = $_GET['view'];
    $file = "views/admin/{$view}.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "Vista no encontrada: $file";
    }
} else {
    $default_view = 'home';
    $file = "views/admin/{$default_view}.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroCafé - Sistema de Compra y Venta de Café</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .logo i {
            margin-right: 0.5rem;
            color: #DEB887;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #DEB887;
            color: #8B4513;
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .hero {
            background: url('img/fondo.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 0;
        }

        .hero > * {
            position: relative;
            z-index: 1;
        }

        .hero-content {
            max-width: 800px;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .features {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: #8B4513;
            margin-bottom: 1rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #8B4513;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 1.1rem;
        }

        .footer {
            background: #8B4513;
            color: white;
            text-align: center;
            padding: 2rem 0;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .nav-buttons {
                flex-direction: column;
                width: 100%;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 10px;
        }

        .close:hover {
            color: #000;
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

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #8B4513;
        }

        .btn-full {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-coffee"></i>
                AgroCafé
            </div>
            <div class="nav-buttons">
                <a href="http://127.0.0.1/Agrocafe1/login.php" class="btn btn-secondary" onclick="openModal('loginModal')">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
                <a href="http://127.0.0.1/Agrocafe1/register.php" class="btn btn-primary" onclick="openModal('registerModal')">
                    <i class="fas fa-user-plus"></i>
                    Registrarse
                </a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenido a AgroCafé</h1>
            <p>La plataforma líder para la compra y venta de café de alta calidad. Conectamos productores con compradores de manera eficiente y transparente.</p>
            <div class="nav-buttons">
                <a href="http://127.0.0.1/Agrocafe1/register.php" class="btn btn-primary">
                    <i class="fas fa-rocket"></i>
                    Comenzar Ahora
                </a>
                <a href="#features" class="btn btn-secondary">
                    <i class="fas fa-info-circle"></i>
                    Conocer Más
                </a>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">¿Por qué elegir AgroCafé?</h2>
            <p class="section-subtitle">Ofrecemos las mejores herramientas para gestionar tu negocio de café</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Análisis de Precios</h3>
                    <p>Monitorea las tendencias del mercado con gráficos detallados y análisis en tiempo real.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Gestión de Ventas</h3>
                    <p>Administra tus ventas y compras de manera eficiente con nuestro sistema integrado.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3>Facturación Automática</h3>
                    <p>Genera facturas automáticamente y envíalas por correo electrónico a tus clientes.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Red de Cooperativas</h3>
                    <p>Conecta con cooperativas y amplía tu red de contactos en el sector cafetero.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Acceso Móvil</h3>
                    <p>Gestiona tu negocio desde cualquier lugar con nuestra plataforma responsive.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Seguridad Garantizada</h3>
                    <p>Tus datos están protegidos con los más altos estándares de seguridad.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 AgroCafé. Todos los derechos reservados.</p>
        </div>
    </footer>

   
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

    
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
