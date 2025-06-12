<?php
require_once 'config/database.php';


$stats = [];


$campesinosQuery = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'campesino' AND activo = 1";
$stats['campesinos'] = fetchOne($campesinosQuery)['total'];


$comprasQuery = "SELECT COUNT(*) as total FROM compras WHERE MONTH(fecha_compra) = MONTH(CURRENT_DATE()) AND YEAR(fecha_compra) = YEAR(CURRENT_DATE())";
$stats['compras_mes'] = fetchOne($comprasQuery)['total'];


$ventasQuery = "SELECT COUNT(*) as total FROM ventas WHERE estado = 'completada'";
$stats['ventas_total'] = fetchOne($ventasQuery)['total'];


$ingresosQuery = "SELECT SUM(total) as ingresos FROM ventas WHERE estado = 'completada'";
$ingresos = fetchOne($ingresosQuery)['ingresos'];
$stats['ingresos'] = $ingresos ? number_format($ingresos, 0, ',', '.') : '0';

// Obtener noticias recientes//
$noticiasQuery = "
    SELECT 'compra' as tipo, c.fecha_compra as fecha, u.nombre as campesino, tc.nombre as cafe, c.total
    FROM compras c 
    JOIN usuarios u ON c.campesino_id = u.id 
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id 
    WHERE c.estado = 'completada'
    ORDER BY c.fecha_compra DESC 
    LIMIT 3
";
$noticias = fetchAll($noticiasQuery);
?>

<style>
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
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card h3 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-card p {
        opacity: 0.9;
    }

    .news-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border-left: 4px solid #8B4513;
        transition: box-shadow 0.3s ease;
    }

    .news-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

    .welcome-message {
        background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border-left: 4px solid #28a745;
    }
</style>

<div class="welcome-message">
    <h3 style="color: #28a745; margin-bottom: 0.5rem;">
        <i class="fa-solid fa-mug-saucer"></i> ¡Bienvenido al Sistema AgroCafé!
    </h3>
    <p style="margin: 0; color: #666;">
        Gestiona eficientemente las compras, ventas y análisis de precios del café. 
        Aquí tienes un resumen de la actividad reciente del sistema.
    </p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $stats['campesinos']; ?></h3>
        <p><i class="fas fa-users"></i> Campesinos Registrados</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['compras_mes']; ?></h3>
        <p><i class="fas fa-shopping-cart"></i> Compras Este Mes</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['ventas_total']; ?></h3>
        <p><i class="fas fa-handshake"></i> Ventas Realizadas</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo $stats['ingresos']; ?></h3>
        <p><i class="fas fa-dollar-sign"></i> Ingresos Totales</p>
    </div>
</div>

<h3 style="color: #8B4513; margin-bottom: 1rem;">
    <i class="fas fa-newspaper"></i> Actividad Reciente
</h3>

<?php if ($noticias && count($noticias) > 0): ?>
    <?php foreach ($noticias as $noticia): ?>
        <div class="news-item">
            <h4>
                <i class="fas fa-shopping-cart"></i>
                Nueva Compra Registrada - <?php echo htmlspecialchars($noticia['cafe']); ?>
            </h4>
            <div class="date">
                <i class="fas fa-calendar"></i>
                <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
            </div>
            <p>
                Compra realizada al campesino <strong><?php echo htmlspecialchars($noticia['campesino']); ?></strong> 
                por un valor de <strong>$<?php echo number_format($noticia['total'], 0, ',', '.'); ?></strong>.
            </p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="news-item">
        <h4><i class="fas fa-info-circle"></i> Sistema Iniciado</h4>
        <div class="date"><?php echo date('d/m/Y'); ?></div>
        <p>El sistema AgroCafé está funcionando correctamente. Comienza registrando compras y ventas para ver la actividad aquí.</p>
    </div>
<?php endif; ?>

<div class="news-item" style="border-left-color: #17a2b8;">
    <h4><i class="fas fa-chart-line"></i> Análisis de Mercado</h4>
    <div class="date"><?php echo date('d/m/Y'); ?></div>
    <p>Los precios del café se mantienen estables. Revisa la sección de análisis para obtener información detallada sobre las tendencias del mercado.</p>
</div>
