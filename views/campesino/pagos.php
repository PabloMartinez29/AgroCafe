<?php
require_once 'config/database.php';

// Obtener el ID del campesino logueado
$campesino_id = $_SESSION['user_id'];

// Obtener resumen de pagos del campesino
$resumenPagos = fetchOne("
    SELECT 
        COALESCE(SUM(CASE WHEN pc.estado = 'completado' THEN pc.monto END), 0) as total_pagado,
        COALESCE(SUM(CASE WHEN pc.estado = 'pendiente' OR pc.id IS NULL THEN c.total END), 0) as total_pendiente,
        COUNT(CASE WHEN pc.estado = 'completado' THEN 1 END) as pagos_recibidos,
        COUNT(CASE WHEN pc.estado = 'pendiente' OR pc.id IS NULL THEN 1 END) as pagos_pendientes
    FROM compras c
    LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id
    WHERE c.campesino_id = ? AND c.estado = 'completada'
", [$campesino_id]);

// Obtener información bancaria del campesino (simulada)
$infoBancaria = fetchOne("
    SELECT telefono, direccion 
    FROM usuarios 
    WHERE id = ?
", [$campesino_id]);

// Obtener historial de pagos del campesino
$historialPagos = fetchAll("
    SELECT pc.*, c.cantidad, c.precio_kg, c.total as monto_venta, c.fecha_compra,
           tc.nombre as tipo_cafe, tc.variedad
    FROM pagos_campesinos pc
    JOIN compras c ON pc.compra_id = c.id
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    WHERE c.campesino_id = ?
    ORDER BY pc.fecha_pago DESC
", [$campesino_id]);

// Obtener ventas pendientes de pago
$ventasPendientes = fetchAll("
    SELECT c.*, tc.nombre as tipo_cafe, tc.variedad
    FROM compras c
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id AND pc.estado = 'completado'
    WHERE c.campesino_id = ? AND c.estado = 'completada' AND pc.id IS NULL
    ORDER BY c.fecha_compra DESC
", [$campesino_id]);
?>

<style>
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

    .status-completado {
        background: #d4edda;
        color: #155724;
    }

    .status-pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .status-fallido {
        background: #f8d7da;
        color: #721c24;
    }

    .info-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border-left: 4px solid #228B22;
    }

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
    }

    .stat-card h3 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
        border-left: 4px solid #17a2b8;
    }
</style>

<h3 style="color: #228B22; margin-bottom: 2rem;">
    <i class="fas fa-credit-card"></i> Estado de Mis Pagos
</h3>

<div class="stats-grid">
    <div class="stat-card">
        <h3>$<?php echo number_format($resumenPagos['total_pagado'], 0, ',', '.'); ?></h3>
        <p><i class="fas fa-check-circle"></i> Total Pagado</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($resumenPagos['total_pendiente'], 0, ',', '.'); ?></h3>
        <p><i class="fas fa-clock"></i> Total Pendiente</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $resumenPagos['pagos_recibidos']; ?></h3>
        <p><i class="fas fa-money-bill"></i> Pagos Recibidos</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $resumenPagos['pagos_pendientes']; ?></h3>
        <p><i class="fas fa-hourglass-half"></i> Pagos Pendientes</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <div class="info-card">
        <h4 style="color: #228B22; margin-bottom: 1rem;">
            <i class="fas fa-info-circle"></i> Información de Pagos
        </h4>
        <div style="margin-bottom: 0.5rem;">
            <strong>Método Principal:</strong> Transferencia Bancaria
        </div>
        <div style="margin-bottom: 0.5rem;">
            <strong>Tiempo de Procesamiento:</strong> 1-3 días hábiles
        </div>
        <div style="margin-bottom: 0.5rem;">
            <strong>Contacto:</strong> <?php echo htmlspecialchars($infoBancaria['telefono']); ?>
        </div>
        <div>
            <strong>Dirección:</strong> <?php echo htmlspecialchars($infoBancaria['direccion']); ?>
        </div>
    </div>
    
    <div class="info-card">
        <h4 style="color: #228B22; margin-bottom: 1rem;">
            <i class="fas fa-calendar"></i> Próximos Pagos
        </h4>
        <?php if ($ventasPendientes && count($ventasPendientes) > 0): ?>
            <?php foreach (array_slice($ventasPendientes, 0, 3) as $venta): ?>
                <div style="margin-bottom: 0.5rem; padding: 0.5rem; background: white; border-radius: 5px;">
                    <strong><?php echo htmlspecialchars($venta['tipo_cafe']); ?></strong><br>
                    <small>$<?php echo number_format($venta['total'], 0, ',', '.'); ?> - <?php echo date('d/m/Y', strtotime($venta['fecha_compra'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #666;">No hay pagos pendientes</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($ventasPendientes && count($ventasPendientes) > 0): ?>
    <div class="alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Ventas Pendientes de Pago:</strong> Tienes <?php echo count($ventasPendientes); ?> venta(s) pendiente(s) de pago por un total de $<?php echo number_format($resumenPagos['total_pendiente'], 0, ',', '.'); ?>.
    </div>
<?php endif; ?>

<h4 style="color: #228B22; margin-bottom: 1rem;">
    <i class="fas fa-history"></i> Historial de Pagos Recibidos
</h4>

<?php if ($historialPagos && count($historialPagos) > 0): ?>
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha Pago</th>
                    <th>Venta</th>
                    <th>Tipo Café</th>
                    <th>Cantidad</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Referencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historialPagos as $pago): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                        <td>C<?php echo str_pad($pago['compra_id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($pago['tipo_cafe']); ?></td>
                        <td><?php echo number_format($pago['cantidad'], 2); ?> kg</td>
                        <td><?php echo ucfirst($pago['metodo_pago']); ?></td>
                        <td><strong>$<?php echo number_format($pago['monto'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="status-badge status-<?php echo $pago['estado']; ?>">
                                <?php echo ucfirst($pago['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($pago['referencia'] ?: 'N/A'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; color: #666;">
        <i class="fas fa-money-bill" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <h4>No hay pagos registrados</h4>
        <p>Aún no has recibido pagos por tus ventas.</p>
    </div>
<?php endif; ?>

<h4 style="color: #228B22; margin-bottom: 1rem; margin-top: 2rem;">
    <i class="fas fa-clock"></i> Ventas Pendientes de Pago
</h4>

<?php if ($ventasPendientes && count($ventasPendientes) > 0): ?>
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha Venta</th>
                    <th>Tipo Café</th>
                    <th>Cantidad</th>
                    <th>Precio/kg</th>
                    <th>Total</th>
                    <th>Días Pendiente</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventasPendientes as $venta): ?>
                    <?php 
                    $diasPendiente = (time() - strtotime($venta['fecha_compra'])) / (60 * 60 * 24);
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($venta['fecha_compra'])); ?></td>
                        <td><?php echo htmlspecialchars($venta['tipo_cafe']); ?></td>
                        <td><?php echo number_format($venta['cantidad'], 2); ?> kg</td>
                        <td>$<?php echo number_format($venta['precio_kg'], 0, ',', '.'); ?></td>
                        <td><strong>$<?php echo number_format($venta['total'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span style="color: <?php echo $diasPendiente > 30 ? '#dc3545' : ($diasPendiente > 15 ? '#ffc107' : '#28a745'); ?>">
                                <?php echo floor($diasPendiente); ?> días
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 2rem; background: white; border-radius: 10px; color: #666;">
        <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #28a745;"></i>
        <h4>¡Excelente!</h4>
        <p>No tienes ventas pendientes de pago.</p>
    </div>
<?php endif; ?>
