<?php
// 🔧 SOLUCIÓN 1: Rutas absolutas desde la raíz del proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/Agrocafe1/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Agrocafe1/lib/email_sender.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_manual':
                $tipoTransaccion = $_POST['tipo_transaccion'];
                $transaccionId = intval($_POST['transaccion_id']);
                
                if ($tipoTransaccion === 'venta') {
                    $transaccion = fetchOne("SELECT * FROM ventas WHERE id = ?", [$transaccionId]);
                    $numeroFactura = 'F' . str_pad($transaccionId, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
                    $facturaData = [
                        'venta_id' => $transaccionId,
                        'compra_id' => NULL,
                        'numero_factura' => $numeroFactura,
                        'fecha_factura' => $_POST['fecha_factura'],
                        'subtotal' => $transaccion['total'],
                        'impuestos' => $transaccion['total'] * 0.19,
                        'total' => $transaccion['total'] * 1.19,
                        'estado_pago' => $_POST['estado_pago'],
                        'fecha_vencimiento' => $_POST['fecha_vencimiento'],
                        'tipo_transaccion' => 'venta'
                    ];
                } else {
                    $transaccion = fetchOne("SELECT * FROM compras WHERE id = ?", [$transaccionId]);
                    $numeroFactura = 'FC' . str_pad($transaccionId, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
                    $facturaData = [
                        'venta_id' => NULL,
                        'compra_id' => $transaccionId,
                        'numero_factura' => $numeroFactura,
                        'fecha_factura' => $_POST['fecha_factura'],
                        'subtotal' => $transaccion['total'],
                        'impuestos' => 0,
                        'total' => $transaccion['total'],
                        'estado_pago' => $_POST['estado_pago'],
                        'fecha_vencimiento' => $_POST['fecha_vencimiento'],
                        'tipo_transaccion' => 'compra'
                    ];
                }
                
                if ($transaccion && insertRecord('facturas', $facturaData)) {
                    $success = "Factura creada exitosamente";
                    $showSuccessModal = true;
                } else {
                    $error = "Error al crear la factura. Verifica los datos o consulta los logs.";
                }
                break;

                
            case 'update_status':
                $facturaId = intval($_POST['factura_id']);
                $nuevoEstado = $_POST['nuevo_estado'];
                
                if (updateRecord('facturas', ['estado_pago' => $nuevoEstado], 'id = ?', [$facturaId])) {
                    $success = "Estado de factura actualizado exitosamente a: " . ucfirst($nuevoEstado);
                    $showSuccessModal = true;
                } else {
                    $error = "Error al actualizar el estado de la factura";
                }
                break;
                
            case 'delete':
                $facturaId = intval($_POST['factura_id']);
                $sql = "DELETE FROM facturas WHERE id = ?";
                if (executeQuery($sql, [$facturaId])) {
                    $success = "Factura eliminada exitosamente";
                    $showSuccessModal = true;
                } else {
                    $error = "Error al eliminar la factura";
                }
                break;
        }
    }
}

// Obtener facturas de cooperativas (ventas)
$facturasCooperativas = fetchAll("
    SELECT f.*, v.cantidad, v.precio_kg, v.total as venta_total,
           COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
           COALESCE(c.email, '') as cliente_email,
           COALESCE(c.telefono, '') as cliente_telefono,
           tc.nombre as tipo_cafe, tc.variedad,
           NULL as campesino_nombre, NULL as campesino_email,
           CASE 
               WHEN v.cooperativa_id IS NOT NULL THEN 'Cooperativa'
               ELSE 'Cliente Individual'
           END as tipo_cliente
    FROM facturas f
    JOIN ventas v ON f.venta_id = v.id
    LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
    JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
    WHERE f.tipo_transaccion = 'venta'
    ORDER BY f.fecha_factura DESC
");

// Obtener facturas de campesinos (compras)
$facturasCampesinos = fetchAll("
    SELECT f.*, comp.cantidad, comp.precio_kg, comp.total as venta_total,
           u.nombre as cliente_nombre,
           u.email as cliente_email,
           u.telefono as cliente_telefono,
           tc.nombre as tipo_cafe, tc.variedad,
           u.nombre as campesino_nombre, u.email as campesino_email,
           'Campesino' as tipo_cliente
    FROM facturas f
    JOIN compras comp ON f.compra_id = comp.id
    JOIN usuarios u ON comp.campesino_id = u.id
    JOIN tipos_cafe tc ON comp.tipo_cafe_id = tc.id
    WHERE f.tipo_transaccion = 'compra'
    ORDER BY f.fecha_factura DESC
");

$facturas = array_merge($facturasCooperativas, $facturasCampesinos);

$ventasSinFactura = fetchAll("
    SELECT v.id, v.total, v.fecha_venta,
           COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
           tc.nombre as tipo_cafe
    FROM ventas v
    LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
    JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
    LEFT JOIN facturas f ON v.id = f.venta_id
    WHERE f.id IS NULL AND v.estado = 'completada'
    ORDER BY v.fecha_venta DESC
");


$comprasSinFactura = fetchAll("
    SELECT c.id, c.total, c.fecha_compra,
           u.nombre as campesino_nombre,
           tc.nombre as tipo_cafe
    FROM compras c
    JOIN usuarios u ON c.campesino_id = u.id
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    LEFT JOIN facturas f ON c.id = f.compra_id
    WHERE f.id IS NULL AND c.estado = 'completada'
    ORDER BY c.fecha_compra DESC
");

// Estadísticas
$totalFacturas = count($facturas);
$facturasPagadas = count(array_filter($facturas, function($f) { return $f['estado_pago'] == 'pagada'; }));
$facturasPendientes = count(array_filter($facturas, function($f) { return $f['estado_pago'] == 'pendiente'; }));
$montoTotal = array_sum(array_column($facturas, 'total'));
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
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: #A0522D;
        transform: translateY(-1px);
    }

    .btn-success {
        background: #28a745;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-info {
        background: #17a2b8;
    }

    .btn-info:hover {
        background: #138496;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
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
        color: #8B4513;
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

    .status-pagada {
        background: #d4edda;
        color: #155724;
    }

    .status-pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .status-vencida {
        background: #f8d7da;
        color: #721c24;
    }

    .tipo-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .tipo-cooperativa {
        background: #8B4513;
        color: white;
    }

    .tipo-campesino {
        background: #28a745;
        color: white;
    }

    .alert {
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
        border-left: 4px solid;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left-color: #28a745;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left-color: #dc3545;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
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
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
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
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #8B4513;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #8B4513, #A0522D);
        color: white;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
    }

    .stat-card h4 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .form-container {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border: 1px solid #ddd;
    }

    .section-divider {
        border-top: 3px solid #8B4513;
        margin: 3rem 0 2rem 0;
        padding-top: 2rem;
    }

    .section-title {
        color: #8B4513;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .success-modal {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        text-align: center;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .success-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        animation: bounce 1s ease-in-out;
    }

    @keyframes bounce {
        0%, 20%, 60%, 100% { transform: translateY(0); }
        40% { transform: translateY(-20px); }
        80% { transform: translateY(-10px); }
    }

    .success-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .success-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .success-btn-primary {
        background: white;
        color: #28a745;
    }

    .success-btn-primary:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    .success-btn-secondary {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 2px solid white;
    }

    .success-btn-secondary:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h3><i class="fas fa-file-invoice"></i> Gestión de Facturas</h3>
    <button class="btn btn-success" onclick="showCreateFacturaForm()">
        <i class="fas fa-plus"></i> Nueva Factura
    </button>
</div>

<!-- Estadísticas -->
<div class="stats-cards">
    <div class="stat-card">
        <h4><?php echo $totalFacturas; ?></h4>
        <p><i class="fas fa-file-invoice"></i> Total Facturas</p>
    </div>
    <div class="stat-card">
        <h4><?php echo $facturasPagadas; ?></h4>
        <p><i class="fas fa-check-circle"></i> Pagadas</p>
    </div>
    <div class="stat-card">
        <h4><?php echo $facturasPendientes; ?></h4>
        <p><i class="fas fa-clock"></i> Pendientes</p>
    </div>
    <div class="stat-card">
        <h4>$<?php echo number_format($montoTotal, 0, ',', '.'); ?></h4>
        <p><i class="fas fa-dollar-sign"></i> Monto Total</p>
    </div>
</div>

<!-- Sección Campesinos -->
<div class="section-title">
    <i class="fas fa-user" style="color: #28a745;"></i>
    Facturas de Campesinos (<?php echo count($facturasCampesinos); ?>)
</div>

<div style="background: white; border-radius: 10px; overflow: hidden; margin-bottom: 2rem;">
    <table class="table">
        <thead>
            <tr>
                <th>Factura</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($facturasCampesinos): ?>
                <?php foreach ($facturasCampesinos as $factura): ?>
                    <tr>
                        <td><strong><?php echo $factura['numero_factura']; ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($factura['cliente_nombre']); ?>
                            <br><span class="tipo-badge tipo-campesino">Campesino</span>
                        </td>
                        <td><?php echo htmlspecialchars($factura['tipo_cafe']); ?></td>
                        <td><?php echo number_format($factura['cantidad'], 2); ?> kg</td>
                        <td><strong>$<?php echo number_format($factura['total'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="status-badge status-<?php echo $factura['estado_pago']; ?>">
                                <?php echo ucfirst($factura['estado_pago']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($factura['fecha_factura'])); ?></td>
                        <td>
                            <a href="generate_pdf.php?factura_id=<?php echo $factura['id']; ?>" class="btn btn-primary" style="padding: 0.5rem;" title="Descargar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            
                            <button onclick="openEmailModal(<?php echo $factura['id']; ?>, '<?php echo htmlspecialchars($factura['campesino_email'] ?: $factura['cliente_email']); ?>')" 
                                    class="btn btn-info" style="padding: 0.5rem;" title="Enviar por Email">
                                <i class="fas fa-envelope"></i>
                            </button>
                            
                            <button onclick="openStatusModal(<?php echo $factura['id']; ?>, '<?php echo $factura['estado_pago']; ?>')" 
                                    class="btn btn-warning" style="padding: 0.5rem;" title="Cambiar Estado">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('¿Está seguro de eliminar esta factura?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="factura_id" value="<?php echo $factura['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 0.5rem;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                        <i class="fas fa-user" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <br>No hay facturas de campesinos
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Divisor -->
<div class="section-divider"></div>

<!-- Sección Cooperativas -->
<div class="section-title">
    <i class="fas fa-building" style="color: #8B4513;"></i>
    Facturas de Cooperativas (<?php echo count($facturasCooperativas); ?>)
</div>

<div style="background: white; border-radius: 10px; overflow: hidden;">
    <table class="table">
        <thead>
            <tr>
                <th>Factura</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($facturasCooperativas): ?>
                <?php foreach ($facturasCooperativas as $factura): ?>
                    <tr>
                        <td><strong><?php echo $factura['numero_factura']; ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($factura['cliente_nombre']); ?>
                            <br><span class="tipo-badge tipo-cooperativa">Cooperativa</span>
                        </td>
                        <td><?php echo htmlspecialchars($factura['tipo_cafe']); ?></td>
                        <td><?php echo number_format($factura['cantidad'], 2); ?> kg</td>
                        <td><strong>$<?php echo number_format($factura['total'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="status-badge status-<?php echo $factura['estado_pago']; ?>">
                                <?php echo ucfirst($factura['estado_pago']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($factura['fecha_factura'])); ?></td>
                        <td>
                            <a href="generate_pdf.php?factura_id=<?php echo $factura['id']; ?>" class="btn btn-primary" style="padding: 0.5rem;" title="Descargar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            
                            <button onclick="openEmailModal(<?php echo $factura['id']; ?>, '<?php echo htmlspecialchars($factura['cliente_email']); ?>')" 
                                    class="btn btn-info" style="padding: 0.5rem;" title="Enviar por Email">
                                <i class="fas fa-envelope"></i>
                            </button>
                            
                            <button onclick="openStatusModal(<?php echo $factura['id']; ?>, '<?php echo $factura['estado_pago']; ?>')" 
                                    class="btn btn-warning" style="padding: 0.5rem;" title="Cambiar Estado">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('¿Está seguro de eliminar esta factura?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="factura_id" value="<?php echo $factura['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 0.5rem;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                        <i class="fas fa-building" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <br>No hay facturas de cooperativas
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Formulario para crear factura manual -->
<div id="create-factura-form" class="form-container" style="display: none;">
    <h4 style="margin-bottom: 1.5rem; color: #8B4513;">
        <i class="fas fa-plus"></i> Crear Nueva Factura
    </h4>
    <form method="POST" id="create-factura-form-inner">
        <input type="hidden" name="action" value="create_manual">
        
        <div class="form-group">
            <label>Tipo de Transacción:</label>
            <select name="tipo_transaccion" id="tipo_transaccion" required onchange="updateTransaccionOptions()">
                <option value="">Seleccionar tipo</option>
                <option value="venta">Venta (Cooperativa)</option>
                <option value="compra">Compra (Campesino)</option>
            </select>
        </div>

        <div class="form-group" id="transaccion_container">
            <label id="transaccion_label">Transacción Asociada:</label>
            <select name="transaccion_id" id="transaccion_id" required>
                <option value="">Seleccionar transacción</option>
            </select>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Fecha Factura:</label>
                <input type="date" name="fecha_factura" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Fecha Vencimiento:</label>
                <input type="date" name="fecha_vencimiento" required value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Estado de Pago:</label>
            <select name="estado_pago" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
                <option value="vencida">Vencida</option>
            </select>
        </div>
        
        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Crear Factura
            </button>
            <button type="button" class="btn btn-danger" onclick="hideCreateFacturaForm()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>

<!-- Modal para enviar email -->
<div id="emailModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEmailModal()">×</span>
        <h3 style="color: #8B4513; margin-bottom: 1rem;">
            <i class="fas fa-envelope"></i> Enviar Factura por Email
        </h3>
        <form id="emailForm">
            <input type="hidden" name="factura_id" id="modal_factura_id">
            
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="modal_email" required>
            </div>
            
            <button type="submit" class="btn btn-success" id="sendEmailBtn">
                <i class="fas fa-paper-plane"></i> Enviar
            </button>
            <button type="button" class="btn" onclick="closeEmailModal()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </form>
    </div>
</div>

<!-- Modal para cambiar estado -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeStatusModal()">×</span>
        <h3 style="color: #8B4513; margin-bottom: 1rem;">
            <i class="fas fa-edit"></i> Cambiar Estado de Factura
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="factura_id" id="status_factura_id">
            
            <div class="form-group">
                <label for="nuevo_estado">Nuevo Estado:</label>
                <select name="nuevo_estado" id="nuevo_estado" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                    <option value="vencida">Vencida</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Actualizar
            </button>
            <button type="button" class="btn" onclick="closeStatusModal()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </form>
    </div>
</div>

<!-- Modal de éxito -->
<div id="successModal" class="modal">
    <div class="modal-content success-modal">
        <span class="close" onclick="closeSuccessModal()" style="color: white;">×</span>
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 style="margin-bottom: 1rem;">¡Operación Exitosa!</h2>
        <p id="successMessage" style="font-size: 1.1rem; margin-bottom: 2rem;"></p>
        <div class="success-buttons">
            <button onclick="closeSuccessModal()" class="success-btn success-btn-primary">
                <i class="fas fa-check"></i> Entendido
            </button>
            <button onclick="window.location.reload()" class="success-btn success-btn-secondary">
                <i class="fas fa-sync"></i> Actualizar Página
            </button>
        </div>
    </div>
</div>

<script>
function showCreateFacturaForm() {
    document.getElementById('create-factura-form').style.display = 'block';
    document.getElementById('create-factura-form').scrollIntoView({ behavior: 'smooth' });
}

function hideCreateFacturaForm() {
    document.getElementById('create-factura-form').style.display = 'none';
}

function openEmailModal(facturaId, email) {
    document.getElementById('modal_factura_id').value = facturaId;
    document.getElementById('modal_email').value = email;
    document.getElementById('emailModal').style.display = 'block';
}

function closeEmailModal() {
    document.getElementById('emailModal').style.display = 'none';
}

function openStatusModal(facturaId, estadoActual) {
    document.getElementById('status_factura_id').value = facturaId;
    document.getElementById('nuevo_estado').value = estadoActual;
    document.getElementById('statusModal').style.display = 'block';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').style.display = 'block';
}

function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
}

// 🔧 AJAX con ruta absoluta
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const sendBtn = document.getElementById('sendEmailBtn');
    const originalText = sendBtn.innerHTML;
    
    // Mostrar loading
    sendBtn.innerHTML = '<span class="spinner"></span> Enviando...';
    sendBtn.disabled = true;
    
    // 🔧 RUTA ABSOLUTA
    fetch('/Agrocafe1/send_email_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Restaurar botón
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
        
        // Cerrar modal de email
        closeEmailModal();
        
        // Mostrar resultado
        if (data.success) {
            showSuccessModal(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        // Restaurar botón
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
        
        console.error('Error:', error);
        alert('Error al enviar el email. Inténtalo de nuevo.');
    });
});

window.onclick = function(event) {
    const emailModal = document.getElementById('emailModal');
    const statusModal = document.getElementById('statusModal');
    const successModal = document.getElementById('successModal');
    
    if (event.target == emailModal) {
        emailModal.style.display = 'none';
    }
    if (event.target == statusModal) {
        statusModal.style.display = 'none';
    }
    if (event.target == successModal) {
        closeSuccessModal();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Mostrar modal de éxito si hay mensaje
    <?php if (isset($showSuccessModal) && $showSuccessModal && isset($success)): ?>
        showSuccessModal('<?php echo addslashes($success); ?>');
    <?php endif; ?>
    
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

const ventasSinFactura = <?php echo json_encode($ventasSinFactura); ?>;
const comprasSinFactura = <?php echo json_encode($comprasSinFactura); ?>;

function updateTransaccionOptions() {
    const tipo = document.getElementById('tipo_transaccion').value;
    const transaccionSelect = document.getElementById('transaccion_id');
    const transaccionLabel = document.getElementById('transaccion_label');
    
    transaccionSelect.innerHTML = '<option value="">Seleccionar transacción</option>';
    
    if (tipo === 'venta') {
        transaccionLabel.textContent = 'Venta Asociada:';
        ventasSinFactura.forEach(venta => {
            const option = document.createElement('option');
            option.value = venta.id;
            option.textContent = `V${String(venta.id).padStart(3, '0')} - ${venta.cliente_nombre} - $${parseInt(venta.total).toLocaleString('es-CO')}`;
            transaccionSelect.appendChild(option);
        });
    } else if (tipo === 'compra') {
        transaccionLabel.textContent = 'Compra Asociada:';
        comprasSinFactura.forEach(compra => {
            const option = document.createElement('option');
            option.value = compra.id;
            option.textContent = `C${String(compra.id).padStart(3, '0')} - ${compra.campesino_nombre} - $${parseInt(compra.total).toLocaleString('es-CO')}`;
            transaccionSelect.appendChild(option);
        });
    }
}
</script>
