<?php
require_once 'config/database.php';

// Obtener el ID del campesino logueado
$campesino_id = $_SESSION['user_id'];

// Obtener estadísticas reales del campesino
$stats = [];

// Ventas realizadas (compras que le hicieron al campesino)
$ventasQuery = "SELECT COUNT(*) as total FROM compras WHERE campesino_id = ? AND estado = 'completada'";
$stats['ventas_realizadas'] = fetchOne($ventasQuery, [$campesino_id])['total'];

// Total de café vendido
$cafeVendidoQuery = "SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE campesino_id = ? AND estado = 'completada'";
$stats['cafe_vendido'] = fetchOne($cafeVendidoQuery, [$campesino_id])['total'];

// Ingresos totales
$ingresosQuery = "SELECT COALESCE(SUM(total), 0) as total FROM compras WHERE campesino_id = ? AND estado = 'completada'";
$stats['ingresos_totales'] = fetchOne($ingresosQuery, [$campesino_id])['total'];

// Precio promedio por kg
$precioPromedioQuery = "SELECT COALESCE(AVG(precio_kg), 0) as promedio FROM compras WHERE campesino_id = ? AND estado = 'completada'";
$stats['precio_promedio'] = fetchOne($precioPromedioQuery, [$campesino_id])['promedio'];

// Obtener historial de ventas del campesino
$historialVentas = fetchAll("
    SELECT c.*, tc.nombre as tipo_cafe, tc.variedad,
           CASE 
               WHEN pc.estado = 'completado' THEN 'Pagado'
               WHEN pc.id IS NULL THEN 'Pendiente de Pago'
               ELSE 'Pendiente'
           END as estado_pago
    FROM compras c
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id AND pc.estado = 'completado'
    WHERE c.campesino_id = ?
    ORDER BY c.fecha_compra DESC
", [$campesino_id]);
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #228B22, #32CD32);
        color: white;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(34, 139, 34, 0.3);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card h3 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .stat-card p {
        opacity: 0.9;
        font-size: 1rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .table th, .table td {
        padding: 1rem 0.75rem;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #228B22;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-pagado {
        background: #d4edda;
        color: #155724;
    }

    .status-pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .welcome-message {
        background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border-left: 4px solid #228B22;
    }
</style>

<div class="welcome-message">
    <h3 style="color: #228B22; margin-bottom: 0.5rem;">
        <i class="fas fa-chart-line"></i> Mi Historial de Ventas
    </h3>
    <p style="margin: 0; color: #666;">
        Aquí puedes ver todas las ventas que has realizado, el estado de los pagos y tus estadísticas de ventas.
    </p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $stats['ventas_realizadas']; ?></h3>
        <p><i class="fas fa-handshake"></i> Ventas Realizadas</p>
    </div>
    <div class="stat-card">
        <h3><?php echo number_format($stats['cafe_vendido'], 1); ?> kg</h3>
        <p><i class="fas fa-coffee"></i> Café Vendido</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($stats['ingresos_totales'], 0, ',', '.'); ?></h3>
        <p><i class="fas fa-dollar-sign"></i> Ingresos Totales</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($stats['precio_promedio'], 0, ',', '.'); ?></h3>
        <p><i class="fas fa-chart-bar"></i> Precio Promedio/kg</p>
    </div>
</div>

<h3 style="color: #228B22; margin-bottom: 1rem;">
    <i class="fas fa-history"></i> Mis Ventas
</h3>

<?php if ($historialVentas && count($historialVentas) > 0): ?>
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo de Café</th>
                    <th>Variedad</th>
                    <th>Cantidad (kg)</th>
                    <th>Precio/kg</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Estado Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historialVentas as $venta): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($venta['fecha_compra'])); ?></td>
                        <td><?php echo htmlspecialchars($venta['tipo_cafe']); ?></td>
                        <td><?php echo ucfirst($venta['variedad']); ?></td>
                        <td><?php echo number_format($venta['cantidad'], 2); ?></td>
                        <td>$<?php echo number_format($venta['precio_kg'], 0, ',', '.'); ?></td>
                        <td><strong>$<?php echo number_format($venta['total'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($venta['estado']); ?>">
                                <?php echo ucfirst($venta['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $venta['estado_pago'])); ?>">
                                <?php echo $venta['estado_pago']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; color: #666;">
        <i class="fas fa-coffee" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <h4>No hay ventas registradas</h4>
        <p>Aún no tienes ventas registradas en el sistema.</p>
    </div>
<?php endif; ?>
