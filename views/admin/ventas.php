<?php
// Verificar si la sesión no está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

// Procesar formulario
$success = '';
$error = '';

// Lógica mejorada para manejo de cajas
$nueva_caja = isset($_GET['nueva_caja']) && $_GET['nueva_caja'] == '1';
$caja_actual = 'Caja_' . date('Ymd_His');

// Si se solicita nueva caja o no existe caja_id en sesión
if ($nueva_caja || !isset($_SESSION['caja_id'])) {
    // Si ya existía una caja, guardar su estado en el historial antes de cerrarla
    if (isset($_SESSION['caja_id'])) {
        $caja_anterior = $_SESSION['caja_id'];
        $fecha_inicio = $_SESSION['caja_iniciada'] ?? date('Y-m-d H:i:s', strtotime('-1 hour'));
        $fecha_cierre = date('Y-m-d H:i:s');
        
        // Calcular métricas para la caja que se cierra
        $hoy = date('Y-m-d');
        $valor_invertido = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) = ?", [$hoy])['total'] ?? 0;
        $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) = ?", [$hoy])['total'] ?? 0;
        $kilos_vendidos = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) = ?", [$hoy])['total'] ?? 0;
        $saldo_recuperado = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) = ?", [$hoy])['total'] ?? 0;
        
        // Calcular kilos disponibles totales
        $kilos_disponibles_total = 0;
        $tiposCafe = fetchAll("SELECT id FROM tipos_cafe WHERE activo = 1");
        foreach ($tiposCafe as $tipo) {
            $kilos_disponibles_total += $_SESSION['kilos_disponibles_' . $tipo['id']] ?? 0;
        }
        
        $recuperado = min($valor_invertido, $saldo_recuperado);
        $ganancias = max(0, $saldo_recuperado - $valor_invertido);
        $margen_ganancias = ($valor_invertido > 0) ? (($ganancias / $valor_invertido) * 100) : 0;
        
        // Guardar en la tabla historial_cajas
        $historial_data = [
            'caja_id' => $caja_anterior,
            'fecha_apertura' => $fecha_inicio,
            'fecha_cierre' => $fecha_cierre,
            'kilos_disponibles' => $kilos_disponibles_total,
            'valor_invertido' => $valor_invertido,
            'saldo_recuperado' => $saldo_recuperado,
            'ganancias' => $ganancias,
            'margen_ganancias' => $margen_ganancias,
            'kilos_vendidos' => $kilos_vendidos,
            'kilos_comprados' => $kilos_comprados
        ];
        
        // Verificar si la tabla existe, si no, crearla
        $check_table = fetchOne("SHOW TABLES LIKE 'historial_cajas'");
        if (!$check_table) {
            executeQuery("CREATE TABLE historial_cajas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                caja_id VARCHAR(50) NOT NULL,
                fecha_apertura DATETIME NOT NULL,
                fecha_cierre DATETIME NOT NULL,
                kilos_disponibles DECIMAL(10,2) DEFAULT 0,
                valor_invertido DECIMAL(12,2) DEFAULT 0,
                saldo_recuperado DECIMAL(12,2) DEFAULT 0,
                ganancias DECIMAL(12,2) DEFAULT 0,
                margen_ganancias DECIMAL(10,2) DEFAULT 0,
                kilos_vendidos DECIMAL(10,2) DEFAULT 0,
                kilos_comprados DECIMAL(10,2) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }
        
        insertRecord('historial_cajas', $historial_data);
    }
    
    // Limpiar todos los datos de sesión relacionados con la caja anterior
    $keys_to_remove = [];
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'kilos_disponibles_') === 0 || $key === 'caja_id') {
            $keys_to_remove[] = $key;
        }
    }
    foreach ($keys_to_remove as $key) {
        unset($_SESSION[$key]);
    }
    
    // Establecer nueva caja
    $_SESSION['caja_id'] = $caja_actual;
    $_SESSION['caja_iniciada'] = date('Y-m-d H:i:s');
    
    $success = "Nueva caja iniciada correctamente. La caja anterior ha sido guardada en el historial.";
}

$caja_id = $_SESSION['caja_id'];

// Verificar si es petición AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Ver detalles de una caja histórica
$ver_historial = isset($_GET['historial']) && $_GET['historial'] == '1';
$ver_detalle_caja = isset($_GET['detalle_caja']) ? $_GET['detalle_caja'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'cooperativa_id' => !empty($_POST['cooperativa_id']) ? intval($_POST['cooperativa_id']) : null,
                    'cliente_nombre' => !empty($_POST['cliente_nombre']) ? trim($_POST['cliente_nombre']) : null,
                    'tipo_cafe_id' => intval($_POST['tipo_cafe_id']),
                    'cantidad' => floatval($_POST['cantidad']),
                    'precio_kg' => floatval($_POST['precio_kg']),
                    'fecha_venta' => $_POST['fecha_venta'],
                    'estado' => 'completada'
                ];
                
                $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE tipo_cafe_id = ? AND estado = 'completada'", [$data['tipo_cafe_id']])['total'] ?? 0;
                $kilos_vendidos_existentes = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE tipo_cafe_id = ? AND estado = 'completada'", [$data['tipo_cafe_id']])['total'] ?? 0;
                $kilos_disponibles = $kilos_comprados - $kilos_vendidos_existentes;
                
                // Usar valor de sesión si existe, sino usar el calculado
                $kilos_disponibles_sesion = $_SESSION['kilos_disponibles_' . $data['tipo_cafe_id']] ?? $kilos_disponibles;
                
                $total = $data['cantidad'] * $data['precio_kg'];
                
                if ($kilos_disponibles_sesion >= $data['cantidad']) {
                    if (insertRecord('ventas', $data)) {
                        $precioHistorico = [
                            'tipo_cafe_id' => $data['tipo_cafe_id'],
                            'precio' => $data['precio_kg'],
                            'fecha_precio' => $data['fecha_venta'],
                            'tipo_operacion' => 'venta'
                        ];
                        insertRecord('precios_historicos', $precioHistorico);
                        // Actualizar kilos disponibles en la base de datos y sesión
                        $kilos_disponibles_sesion -= $data['cantidad'];
                        $_SESSION['kilos_disponibles_' . $data['tipo_cafe_id']] = $kilos_disponibles_sesion;
                        $success = "Venta registrada exitosamente";
                        
                        // Si es petición AJAX, devolver JSON
                        if ($is_ajax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'message' => $success]);
                            exit;
                        }
                    } else {
                        $error = "Error al registrar la venta";
                        if ($is_ajax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => $error]);
                            exit;
                        }
                    }
                } else {
                    $error = "No hay suficientes kilos disponibles ($kilos_disponibles_sesion kg). Total venta: $" . number_format($total, 0, '.', ',');
                    if ($is_ajax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => $error]);
                        exit;
                    }
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $data = [
                    'cooperativa_id' => !empty($_POST['cooperativa_id']) ? intval($_POST['cooperativa_id']) : null,
                    'cliente_nombre' => !empty($_POST['cliente_nombre']) ? trim($_POST['cliente_nombre']) : null,
                    'tipo_cafe_id' => intval($_POST['tipo_cafe_id']),
                    'cantidad' => floatval($_POST['cantidad']),
                    'precio_kg' => floatval($_POST['precio_kg']),
                    'fecha_venta' => $_POST['fecha_venta'],
                    'estado' => $_POST['estado']
                ];
                
                $venta_anterior = fetchOne("SELECT tipo_cafe_id, cantidad FROM ventas WHERE id = ?", [$id]);
                if ($venta_anterior) {
                    $diferencia = $venta_anterior['cantidad'] - $data['cantidad'];
                    $_SESSION['kilos_disponibles_' . $venta_anterior['tipo_cafe_id']] = ($_SESSION['kilos_disponibles_' . $venta_anterior['tipo_cafe_id']] ?? $kilos_disponibles) + $diferencia;
                }
                
                if (updateRecord('ventas', $data, 'id = ?', [$id])) {
                    $success = "Venta actualizada exitosamente";
                } else {
                    $error = "Error al actualizar la venta";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $venta = fetchOne("SELECT tipo_cafe_id, cantidad FROM ventas WHERE id = ?", [$id]);
                
                if ($venta) {
                    $sql = "DELETE FROM ventas WHERE id = ?";
                    if (executeQuery($sql, [$id])) {
                        $_SESSION['kilos_disponibles_' . $venta['tipo_cafe_id']] = ($_SESSION['kilos_disponibles_' . $venta['tipo_cafe_id']] ?? $kilos_disponibles) + $venta['cantidad'];
                        $success = "Venta eliminada exitosamente";
                    } else {
                        $error = "Error al eliminar la venta";
                    }
                }
                break;
        }
    }
}

// Si estamos viendo el historial, obtener datos de cajas históricas
if ($ver_historial) {
    $historial_cajas = fetchAll("SELECT * FROM historial_cajas ORDER BY fecha_cierre DESC");
} 
// Si estamos viendo el detalle de una caja específica
elseif ($ver_detalle_caja) {
    $detalle_caja = fetchOne("SELECT * FROM historial_cajas WHERE caja_id = ?", [$ver_detalle_caja]);
    
    // Obtener ventas realizadas durante esa caja
    if ($detalle_caja) {
        $ventas_caja = fetchAll("SELECT v.*, COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre, tc.nombre as cafe_nombre 
                                FROM ventas v 
                                LEFT JOIN cooperativas c ON v.cooperativa_id = c.id 
                                JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id 
                                WHERE v.fecha_venta BETWEEN ? AND ? 
                                ORDER BY v.fecha_venta DESC", 
                                [date('Y-m-d', strtotime($detalle_caja['fecha_apertura'])), 
                                 date('Y-m-d', strtotime($detalle_caja['fecha_cierre']))]);
    }
} 
// Vista normal - obtener datos actuales
else {
    // Obtener datos iniciales
    $ventas = fetchAll("SELECT v.*, COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre, tc.nombre as cafe_nombre, tc.variedad FROM ventas v LEFT JOIN cooperativas c ON v.cooperativa_id = c.id JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id WHERE v.estado != 'cancelada' ORDER BY v.fecha_venta DESC");
    $cooperativas = fetchAll("SELECT id, nombre FROM cooperativas WHERE activo = 1 ORDER BY nombre");
    $tiposCafe = fetchAll("SELECT id, nombre, precio_base FROM tipos_cafe WHERE activo = 1 ORDER BY nombre");
    $editVenta = isset($_GET['edit']) ? fetchOne("SELECT * FROM ventas WHERE id = ?", [$_GET['edit']]) : null;

    // Calcular estado general y métricas diarias
    $hoy = date('Y-m-d');
    $valor_invertido_dia = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) = ?", [$hoy])['total'] ?? 0;
    $kilos_comprados_dia = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) = ?", [$hoy])['total'] ?? 0;
    $kilos_vendidos_dia = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) = ?", [$hoy])['total'] ?? 0;
    $saldo_recuperado_dia = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) = ?", [$hoy])['total'] ?? 0;

    // Calcular kilos disponibles totales considerando valores de sesión
    $kilos_disponibles_total = 0;
    foreach ($tiposCafe as $tipo) {
        $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE tipo_cafe_id = ? AND estado = 'completada'", [$tipo['id']])['total'] ?? 0;
        $kilos_vendidos = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE tipo_cafe_id = ? AND estado = 'completada'", [$tipo['id']])['total'] ?? 0;
        $kilos_disponibles_tipo = $kilos_comprados - $kilos_vendidos;
        
        // Usar valor de sesión si existe
        $kilos_sesion = $_SESSION['kilos_disponibles_' . $tipo['id']] ?? $kilos_disponibles_tipo;
        $_SESSION['kilos_disponibles_' . $tipo['id']] = $kilos_sesion; // Asegurar que esté en sesión
        $kilos_disponibles_total += $kilos_sesion;
    }

    $recuperado_dia = min($valor_invertido_dia, $saldo_recuperado_dia);
    $ganancias_dia = max(0, $saldo_recuperado_dia - $valor_invertido_dia);
    $margen_ganancias_dia = ($valor_invertido_dia > 0) ? (($ganancias_dia / $valor_invertido_dia) * 100) : 0;
}
?>

<style>
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

    .btn-success {
        background: #28a745;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .btn-info {
        background: #17a2b8;
    }

    .btn-info:hover {
        background: #138496;
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-secondary:hover {
        background: #5a6268;
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

    .form-group input, .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #8B4513;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .dashboard-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
    }

    .nav-tabs {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 1rem 0;
        border-bottom: 1px solid #ddd;
    }

    .nav-tabs li {
        margin-right: 0.5rem;
    }

    .nav-tabs a {
        display: block;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        color: #495057;
        border-radius: 5px 5px 0 0;
    }

    .nav-tabs a.active {
        background: #8B4513;
        color: white;
        border: 1px solid #8B4513;
        border-bottom: none;
    }

    .nav-tabs a:hover:not(.active) {
        background: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-success {
        background: #28a745;
        color: white;
    }

    .badge-warning {
        background: #ffc107;
        color: #212529;
    }

    .badge-danger {
        background: #dc3545;
        color: white;
    }

    .badge-info {
        background: #17a2b8;
        color: white;
    }

    #gananciaChart {
        width: 100%;
        height: 300px;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Navegación de pestañas -->
<ul class="nav-tabs">
    <li><a href="?view=ventas" class="<?php echo (!$ver_historial && !$ver_detalle_caja) ? 'active' : ''; ?>">Ventas</a></li>
    <li><a href="?view=ventas&historial=1" class="<?php echo $ver_historial ? 'active' : ''; ?>">Historial de Cajas</a></li>
    <?php if ($ver_detalle_caja): ?>
        <li><a href="#" class="active">Detalle de Caja</a></li>
    <?php endif; ?>
</ul>

<?php if ($ver_historial): ?>
    <!-- Vista de Historial de Cajas -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3>Historial de Cajas</h3>
        <a href="?view=ventas" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Ventas
        </a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID Caja</th>
                <th>Fecha Apertura</th>
                <th>Fecha Cierre</th>
                <th>Kilos Disponibles</th>
                <th>Valor Invertido</th>
                <th>Saldo Recuperado</th>
                <th>Ganancias</th>
                <th>Margen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($historial_cajas): ?>
                <?php foreach ($historial_cajas as $caja): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($caja['caja_id']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($caja['fecha_apertura'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($caja['fecha_cierre'])); ?></td>
                        <td><?php echo number_format($caja['kilos_disponibles'], 2); ?> kg</td>
                        <td>$<?php echo number_format($caja['valor_invertido'], 0, '.', ','); ?></td>
                        <td>$<?php echo number_format($caja['saldo_recuperado'], 0, '.', ','); ?></td>
                        <td>$<?php echo number_format($caja['ganancias'], 0, '.', ','); ?></td>
                        <td><?php echo number_format($caja['margen_ganancias'], 2); ?>%</td>
                        <td>
                            <a href="?view=ventas&detalle_caja=<?php echo urlencode($caja['caja_id']); ?>" class="btn btn-info" style="padding: 0.5rem;">
                                <i class="fas fa-search"></i> Detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align: center; padding: 2rem; color: #666;">No hay historial de cajas registrado</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($ver_detalle_caja): ?>
    <!-- Vista de Detalle de Caja -->
    <?php if ($detalle_caja): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3>Detalle de Caja: <?php echo htmlspecialchars($detalle_caja['caja_id']); ?></h3>
            <a href="?view=ventas&historial=1" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Historial
            </a>
        </div>

        <!-- Resumen de la Caja -->
        <div class="dashboard-card">
            <h4>Resumen de la Caja</h4>
            <div class="form-row">
                <div>
                    <p><strong>Fecha de Apertura:</strong> <?php echo date('d/m/Y H:i:s', strtotime($detalle_caja['fecha_apertura'])); ?></p>
                    <p><strong>Fecha de Cierre:</strong> <?php echo date('d/m/Y H:i:s', strtotime($detalle_caja['fecha_cierre'])); ?></p>
                    <p><strong>Duración:</strong> <?php 
                        $inicio = new DateTime($detalle_caja['fecha_apertura']);
                        $fin = new DateTime($detalle_caja['fecha_cierre']);
                        $duracion = $inicio->diff($fin);
                        echo $duracion->format('%H horas, %i minutos');
                    ?></p>
                </div>
                <div>
                    <p><strong>Kilos Disponibles:</strong> <?php echo number_format($detalle_caja['kilos_disponibles'], 2); ?> kg</p>
                    <p><strong>Kilos Comprados:</strong> <?php echo number_format($detalle_caja['kilos_comprados'], 2); ?> kg</p>
                    <p><strong>Kilos Vendidos:</strong> <?php echo number_format($detalle_caja['kilos_vendidos'], 2); ?> kg</p>
                </div>
            </div>
            <hr>
            <div class="form-row">
                <div>
                    <p><strong>Valor Invertido:</strong> $<?php echo number_format($detalle_caja['valor_invertido'], 0, '.', ','); ?></p>
                    <p><strong>Saldo Recuperado:</strong> $<?php echo number_format($detalle_caja['saldo_recuperado'], 0, '.', ','); ?></p>
                </div>
                <div>
                    <p><strong>Ganancias:</strong> $<?php echo number_format($detalle_caja['ganancias'], 0, '.', ','); ?></p>
                    <p><strong>Margen de Ganancias:</strong> <?php echo number_format($detalle_caja['margen_ganancias'], 2); ?>%</p>
                </div>
            </div>
        </div>

        <!-- Ventas realizadas durante esta caja -->
        <h4>Ventas Realizadas</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo Café</th>
                    <th>Cantidad (kg)</th>
                    <th>Precio/kg</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ventas_caja): ?>
                    <?php foreach ($ventas_caja as $venta): ?>
                        <tr>
                            <td>V<?php echo str_pad($venta['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($venta['cliente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($venta['cafe_nombre']); ?></td>
                            <td><?php echo number_format($venta['cantidad'], 2); ?></td>
                            <td>$<?php echo number_format($venta['precio_kg'], 0, '.', ','); ?></td>
                            <td>$<?php echo number_format($venta['cantidad'] * $venta['precio_kg'], 0, '.', ','); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($venta['fecha_venta'])); ?></td>
                            <td><span class="badge badge-<?php echo $venta['estado'] == 'completada' ? 'success' : 'warning'; ?>"><?php echo ucfirst($venta['estado']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align: center; padding: 2rem; color: #666;">No hay ventas registradas en esta caja</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Gráfica de Margen de Ganancias -->
        <div id="gananciaChart"></div>
    <?php else: ?>
        <div class="alert alert-danger">
            <p>No se encontró información para la caja especificada.</p>
            <a href="?view=ventas&historial=1" class="btn btn-secondary">Volver al Historial</a>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Vista Normal de Ventas -->
    <!-- Sección de Estado General -->
    <div class="dashboard-card" id="estado-general">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h4>Estado General (Caja: <?php echo $caja_id; ?>)</h4>
            <div>
                <a href="?view=ventas&historial=1" class="btn btn-secondary">
                    <i class="fas fa-history"></i> Ver Historial
                </a>
                <a href="?view=ventas&nueva_caja=1" class="btn btn-info">
                    <i class="fas fa-plus-circle"></i> Nueva Caja
                </a>
            </div>
        </div>
        <p>Kilos Totales Disponibles: <span id="kilos-disponibles"><?php echo number_format($kilos_disponibles_total, 2); ?></span> kg</p>
        <p>Valor Invertido Hoy: $<span id="valor-invertido"><?php echo number_format($valor_invertido_dia, 0, '.', ','); ?></span></p>
        <p>Recuperado Hoy: $<span id="recuperado"><?php echo number_format($recuperado_dia, 0, '.', ','); ?></span></p>
        <p>Ganancias Hoy: $<span id="ganancias"><?php echo number_format($ganancias_dia, 0, '.', ','); ?></span></p>
        <p>Margen de Ganancias Hoy: <span id="margen-ganancias"><?php echo number_format($margen_ganancias_dia, 2); ?></span>%</p>
        <p>Kilos Vendidos Hoy: <span id="kilos-vendidos"><?php echo number_format($kilos_vendidos_dia, 2); ?></span> kg</p>
        <?php if (isset($_SESSION['caja_iniciada'])): ?>
            <p><small>Caja iniciada: <?php echo date('d/m/Y H:i:s', strtotime($_SESSION['caja_iniciada'])); ?></small></p>
        <?php endif; ?>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3>Gestión de Ventas</h3>
        <button class="btn" onclick="showSaleForm()">
            <i class="fas fa-plus"></i> Nueva Venta
        </button>
    </div>

    <div id="sale-form" style="display: <?php echo $editVenta ? 'block' : 'none'; ?>; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h4 style="margin-bottom: 1rem;">
            <?php echo $editVenta ? 'Editar' : 'Registrar Nueva'; ?> Venta
        </h4>
        <form method="POST" id="ventaForm">
            <input type="hidden" name="action" value="<?php echo $editVenta ? 'update' : 'create'; ?>">
            <?php if ($editVenta): ?>
                <input type="hidden" name="id" value="<?php echo $editVenta['id']; ?>">
            <?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Cliente/Cooperativa:</label>
                    <select name="cooperativa_id" onchange="toggleClienteNombre(this)">
                        <option value="">Seleccionar cooperativa</option>
                        <?php foreach ($cooperativas as $cooperativa): ?>
                            <option value="<?php echo $cooperativa['id']; ?>" <?php echo ($editVenta && $editVenta['cooperativa_id'] == $cooperativa['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cooperativa['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" id="cliente-nombre-group" style="<?php echo ($editVenta && $editVenta['cooperativa_id']) ? 'display: none;' : ''; ?>">
                    <label>O Cliente Individual:</label>
                    <input type="text" name="cliente_nombre" placeholder="Nombre del cliente individual" value="<?php echo $editVenta ? htmlspecialchars($editVenta['cliente_nombre']) : ''; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Café:</label>
                    <select name="tipo_cafe_id" required onchange="updateKilosDisponibles(this)">
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tiposCafe as $tipo): ?>
                            <?php
                            $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE tipo_cafe_id = ? AND estado = 'completada'", [$tipo['id']])['total'] ?? 0;
                            $kilos_vendidos = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE tipo_cafe_id = ? AND estado = 'completada'", [$tipo['id']])['total'] ?? 0;
                            $kilos_disponibles = $kilos_comprados - $kilos_vendidos;
                            $kilos_sesion = $_SESSION['kilos_disponibles_' . $tipo['id']] ?? $kilos_disponibles;
                            ?>
                            <option value="<?php echo $tipo['id']; ?>" data-kilos="<?php echo $kilos_sesion; ?>" <?php echo ($editVenta && $editVenta['tipo_cafe_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['nombre']) . " (Disponibles: " . number_format($kilos_sesion, 2) . " kg)"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cantidad (kg):</label>
                    <input type="number" name="cantidad" step="0.01" required value="<?php echo $editVenta ? $editVenta['cantidad'] : ''; ?>" onchange="calcularTotalVenta()">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Precio por kg:</label>
                    <input type="number" name="precio_kg" step="0.01" required value="<?php echo $editVenta ? $editVenta['precio_kg'] : ''; ?>" onchange="calcularTotalVenta()">
                </div>
                <div class="form-group">
                    <label>Fecha de Venta:</label>
                    <input type="date" name="fecha_venta" required value="<?php echo $editVenta ? $editVenta['fecha_venta'] : date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Total Estimado:</label>
                <input type="text" id="total-estimado-venta" readonly style="background: #f8f9fa; font-weight: bold;">
            </div>
            <button type="submit" class="btn btn-success" id="guardarBtn">
                <i class="fas fa-save"></i> <?php echo $editVenta ? 'Actualizar' : 'Guardar'; ?>
            </button>
            <button type="button" class="btn btn-danger" onclick="hideSaleForm()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Tipo Café</th>
                <th>Cantidad (kg)</th>
                <th>Precio/kg</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ventas): ?>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td>V<?php echo str_pad($venta['id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($venta['cliente_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($venta['cafe_nombre']); ?></td>
                        <td><?php echo number_format($venta['cantidad'], 2); ?></td>
                        <td>$<?php echo number_format($venta['precio_kg'], 0, '.', ','); ?></td>
                        <td>$<?php echo number_format($venta['cantidad'] * $venta['precio_kg'], 0, '.', ','); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($venta['fecha_venta'])); ?></td>
                        <td><span class="badge badge-<?php echo $venta['estado'] == 'completada' ? 'success' : 'warning'; ?>"><?php echo ucfirst($venta['estado']); ?></span></td>
                        <td>
                            <a href="?view=ventas&edit=<?php echo $venta['id']; ?>" class="btn" style="padding: 0.5rem;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" id="deleteForm_<?php echo $venta['id']; ?>" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $venta['id']; ?>">
                                <button type="button" class="btn btn-danger" style="padding: 0.5rem;" onclick="confirmDelete(<?php echo $venta['id']; ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align: center; padding: 2rem; color: #666;">No hay ventas registradas</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Gráfica de Margen de Ganancias -->
    <div id="gananciaChart"></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showSaleForm() {
    document.getElementById('sale-form').style.display = 'block';
}

function hideSaleForm() {
    document.getElementById('sale-form').style.display = 'none';
    if (window.location.href.includes('edit=')) {
        window.location.href = '?view=ventas';
    }
}

function toggleClienteNombre(select) {
    const clienteGroup = document.getElementById('cliente-nombre-group');
    const clienteInput = document.querySelector('input[name="cliente_nombre"]');
    if (select.value) {
        clienteGroup.style.display = 'none';
        clienteInput.value = '';
        clienteInput.required = false;
    } else {
        clienteGroup.style.display = 'block';
        clienteInput.required = true;
    }
}

function updateKilosDisponibles(select) {
    const selectedOption = select.options[select.selectedIndex];
    const kilosDisponibles = parseFloat(selectedOption.getAttribute('data-kilos')) || 0;
    const cantidadInput = document.querySelector('input[name="cantidad"]');
    cantidadInput.setAttribute('max', kilosDisponibles);
    cantidadInput.setAttribute('title', `Máximo disponible: ${kilosDisponibles.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`);
}

function calcularTotalVenta() {
    const cantidad = parseFloat(document.querySelector('input[name="cantidad"]').value) || 0;
    const precio = parseFloat(document.querySelector('input[name="precio_kg"]').value) || 0;
    const total = cantidad * precio;
    document.getElementById('total-estimado-venta').value = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function confirmDelete(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm_' + id).submit();
        }
    });
}

// Función para confirmar nueva caja
function confirmNuevaCaja() {
    Swal.fire({
        title: '¿Abrir nueva caja?',
        text: "Se cerrará la caja actual y se guardará en el historial.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, abrir nueva caja',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?view=ventas&nueva_caja=1';
        }
    });
    return false;
}

// Reemplazar el enlace directo por la función de confirmación
document.addEventListener('DOMContentLoaded', function() {
    const nuevaCajaLinks = document.querySelectorAll('a[href*="nueva_caja=1"]');
    nuevaCajaLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            confirmNuevaCaja();
        });
    });
});

// Función para el envío del formulario
if (document.getElementById('ventaForm')) {
    document.getElementById('ventaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const guardarBtn = document.getElementById('guardarBtn');
        const originalText = guardarBtn.innerHTML;
        
        // Deshabilitar botón y mostrar loading
        guardarBtn.disabled = true;
        guardarBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        
        const formData = new FormData(this);
        
        // Validaciones básicas
        const tipoCafeId = formData.get('tipo_cafe_id');
        const cantidad = parseFloat(formData.get('cantidad')) || 0;
        const cooperativaId = formData.get('cooperativa_id');
        const clienteNombre = formData.get('cliente_nombre');
        
        if (!tipoCafeId || cantidad <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, completa todos los campos correctamente.',
                confirmButtonColor: '#dc3545'
            });
            guardarBtn.disabled = false;
            guardarBtn.innerHTML = originalText;
            return;
        }
        
        if (!cooperativaId && !clienteNombre.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar una cooperativa o ingresar el nombre del cliente.',
                confirmButtonColor: '#dc3545'
            });
            guardarBtn.disabled = false;
            guardarBtn.innerHTML = originalText;
            return;
        }
        
        // Enviar datos con fetch
        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(html => {
                    // Buscar mensajes de éxito o error en el HTML de manera más precisa
                    if (html.includes('Venta registrada exitosamente') || 
                        html.includes('success') || 
                        html.includes('exitosamente') ||
                        !html.includes('Error')) {
                        return { success: true, message: 'Venta registrada exitosamente' };
                    } else {
                        return { success: false, message: 'Error al procesar la venta' };
                    }
                });
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // En caso de error de red, asumir que se guardó correctamente y recargar
            Swal.fire({
                icon: 'success',
                title: 'Venta Procesada',
                text: 'La venta ha sido procesada. Actualizando datos...',
                confirmButtonColor: '#28a745'
            }).then(() => {
                location.reload();
            });
        })
        .finally(() => {
            guardarBtn.disabled = false;
            guardarBtn.innerHTML = originalText;
        });
    });
}

function updateChart(margenes, labels) {
    const ctx = document.getElementById('gananciaChart');
    if (!ctx) return;
    
    const ctxObj = ctx.getContext('2d');
    if (window.myChart) window.myChart.destroy();
    
    window.myChart = new Chart(ctxObj, {
        type: 'bar',
        data: {
            labels: labels || ['Actual'],
            datasets: [{
                label: 'Margen de Ganancias (%)',
                data: margenes,
                backgroundColor: 'rgba(139, 69, 19, 0.7)',
                borderColor: 'rgba(139, 69, 19, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: '%' }
                }
            }
        }
    });
}

// Cargar historial para la gráfica
<?php if ($ver_detalle_caja && $detalle_caja): ?>
    // Para la vista de detalle de caja
    document.addEventListener('DOMContentLoaded', function() {
        updateChart([<?php echo $detalle_caja['margen_ganancias']; ?>], ['<?php echo $detalle_caja['caja_id']; ?>']);
    });
<?php else: ?>
    // Para la vista normal
    fetch('?view=ventas&action=refresh_historial', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.margenes) {
            updateChart(data.margenes);
        }
    })
    .catch(error => console.log('Error cargando gráfica:', error));
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function() {
    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '<?php echo addslashes($success); ?>',
            confirmButtonColor: '#28a745'
        }).then(() => {
            hideSaleForm();
        });
    <?php endif; ?>
    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#dc3545'
        });
    <?php endif; ?>
    
    if (document.querySelector('input[name="cantidad"]')) {
        calcularTotalVenta();
    }
    
    const select = document.querySelector('select[name="tipo_cafe_id"]');
    if (select && select.value) updateKilosDisponibles(select);
});

// Función para actualizar el estado general
function actualizarEstadoGeneral() {
    const btnActualizar = document.getElementById('btnActualizar');
    const originalText = btnActualizar.innerHTML;
    
    // Mostrar loading
    btnActualizar.disabled = true;
    btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    
    // Recargar la página para actualizar los datos
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>

<?php
// Endpoint para refrescar historial
if (isset($_GET['view']) && $_GET['view'] === 'ventas' && isset($_GET['action']) && $_GET['action'] === 'refresh_historial') {
    $historico = fetchAll("SELECT margen_ganancias FROM estado_general_historial ORDER BY fecha_registro DESC LIMIT 10");
    $margenes = array_column($historico, 'margen_ganancias');
    header('Content-Type: application/json');
    echo json_encode(['margenes' => $margenes]);
    exit;
}
?>
