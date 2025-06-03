<?php
require_once 'config/database.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_venta_pago':
                $data = [
                    'venta_id' => intval($_POST['venta_id']),
                    'monto' => floatval($_POST['monto']),
                    'metodo_pago' => $_POST['metodo_pago'],
                    'referencia' => trim($_POST['referencia']),
                    'fecha_pago' => $_POST['fecha_pago'],
                    'estado' => 'completado'
                ];
                
                if (insertRecord('pagos', $data)) {
                    // Actualizar estado de la venta
                    updateRecord('ventas', ['estado' => 'completada'], 'id = ?', [$data['venta_id']]);
                    
                    // Crear factura automáticamente
                    $venta = fetchOne("SELECT * FROM ventas WHERE id = ?", [$data['venta_id']]);
                    if ($venta) {
                        $numeroFactura = 'F' . str_pad($data['venta_id'], 3, '0', STR_PAD_LEFT);
                        $facturaData = [
                            'venta_id' => $data['venta_id'],
                            'numero_factura' => $numeroFactura,
                            'fecha_factura' => $data['fecha_pago'],
                            'subtotal' => $venta['total'],
                            'impuestos' => $venta['total'] * 0.19,
                            'total' => $venta['total'] * 1.19,
                            'estado_pago' => 'pagada',
                            'fecha_vencimiento' => date('Y-m-d', strtotime($data['fecha_pago'] . ' +30 days'))
                        ];
                        insertRecord('facturas', $facturaData);
                    }
                    
                    $success = "Pago de venta registrado exitosamente y factura generada";
                } else {
                    $error = "Error al registrar el pago de venta";
                }
                break;
                
            case 'create_compra_pago':
                // Registrar pago a campesino por compra
                $compraId = intval($_POST['compra_id']);
                $monto = floatval($_POST['monto']);
                $metodoPago = $_POST['metodo_pago'];
                $referencia = trim($_POST['referencia']);
                $fechaPago = $_POST['fecha_pago'];
                
                // Crear registro en tabla de pagos_campesinos (nueva tabla)
                $sql = "INSERT INTO pagos_campesinos (compra_id, monto, metodo_pago, referencia, fecha_pago, estado) 
                        VALUES (?, ?, ?, ?, ?, 'completado')";
                
                if (executeQuery($sql, [$compraId, $monto, $metodoPago, $referencia, $fechaPago])) {
                    // Actualizar estado de la compra
                    updateRecord('compras', ['estado' => 'pagada'], 'id = ?', [$compraId]);
                    $success = "Pago a campesino registrado exitosamente";
                } else {
                    $error = "Error al registrar el pago a campesino";
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $data = [
                    'venta_id' => intval($_POST['venta_id']),
                    'monto' => floatval($_POST['monto']),
                    'metodo_pago' => $_POST['metodo_pago'],
                    'referencia' => trim($_POST['referencia']),
                    'fecha_pago' => $_POST['fecha_pago'],
                    'estado' => $_POST['estado']
                ];
                
                if (updateRecord('pagos', $data, 'id = ?', [$id])) {
                    $success = "Pago actualizado exitosamente";
                } else {
                    $error = "Error al actualizar el pago";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                if (updateRecord('pagos', ['estado' => 'fallido'], 'id = ?', [$id])) {
                    $success = "Pago marcado como fallido";
                } else {
                    $error = "Error al actualizar el pago";
                }
                break;
        }
    }
}

// Crear tabla de pagos_campesinos si no existe
$createTableSQL = "
CREATE TABLE IF NOT EXISTS pagos_campesinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    metodo_pago ENUM('transferencia', 'efectivo', 'cheque') NOT NULL,
    referencia VARCHAR(100),
    fecha_pago DATE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completado', 'fallido') DEFAULT 'completado',
    FOREIGN KEY (compra_id) REFERENCES compras(id)
)";
executeQuery($createTableSQL);

// Obtener pagos de ventas con detalles
$pagosVentas = fetchAll("
    SELECT p.*, v.total as venta_total,
           COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
           tc.nombre as tipo_cafe,
           'venta' as tipo_transaccion
    FROM pagos p
    JOIN ventas v ON p.venta_id = v.id
    LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
    JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
    WHERE p.estado != 'fallido'
    ORDER BY p.fecha_pago DESC
");

// Obtener pagos a campesinos
$pagosCampesinos = fetchAll("
    SELECT pc.*, c.total as compra_total,
           u.nombre as campesino_nombre,
           tc.nombre as tipo_cafe,
           'compra' as tipo_transaccion
    FROM pagos_campesinos pc
    JOIN compras c ON pc.compra_id = c.id
    JOIN usuarios u ON c.campesino_id = u.id
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    WHERE pc.estado != 'fallido'
    ORDER BY pc.fecha_pago DESC
");

// Obtener ventas pendientes de pago
$ventasPendientes = fetchAll("
    SELECT v.id, v.total,
           COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
           tc.nombre as tipo_cafe,
           v.fecha_venta
    FROM ventas v
    LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
    JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
    LEFT JOIN pagos p ON v.id = p.venta_id AND p.estado = 'completado'
    WHERE v.estado = 'completada' AND p.id IS NULL
    ORDER BY v.fecha_venta DESC
");

// Obtener compras pendientes de pago
$comprasPendientes = fetchAll("
    SELECT c.id, c.total,
           u.nombre as campesino_nombre,
           tc.nombre as tipo_cafe,
           c.fecha_compra
    FROM compras c
    JOIN usuarios u ON c.campesino_id = u.id
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id AND pc.estado = 'completado'
    WHERE c.estado = 'completada' AND pc.id IS NULL
    ORDER BY c.fecha_compra DESC
");
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

    .btn-info {
        background: #17a2b8;
    }

    .btn-info:hover {
        background: #138496;
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

    .alert {
        padding: 0.75rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: bold;
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

    .tabs {
        display: flex;
        margin-bottom: 1rem;
        border-bottom: 2px solid #ddd;
    }

    .tab {
        padding: 1rem 2rem;
        background: #f8f9fa;
        border: none;
        cursor: pointer;
        border-bottom: 3px solid transparent;
    }

    .tab.active {
        background: white;
        border-bottom-color: #8B4513;
        color: #8B4513;
        font-weight: bold;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if (isset($success)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<h3 style="margin-bottom: 2rem;">Gestión de Pagos</h3>

<!-- Tabs -->
<div class="tabs">
    <button class="tab active" onclick="showTab('ventas')">
        <i class="fas fa-handshake"></i> Pagos de Ventas
    </button>
    <button class="tab" onclick="showTab('compras')">
        <i class="fas fa-user"></i> Pagos a Campesinos
    </button>
</div>

<!-- Tab Pagos de Ventas -->
<div id="ventas-tab" class="tab-content active">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h4>Pagos de Ventas (Cooperativas/Clientes)</h4>
        <button class="btn" onclick="showVentaPaymentForm()">
            <i class="fas fa-plus"></i> Nuevo Pago de Venta
        </button>
    </div>

    <div id="venta-payment-form" style="display: none; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h4 style="margin-bottom: 1rem;">Registrar Pago de Venta</h4>
        <form method="POST">
            <input type="hidden" name="action" value="create_venta_pago">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Venta Asociada:</label>
                    <select name="venta_id" required onchange="updateMontoVenta(this)">
                        <option value="">Seleccionar venta</option>
                        <?php foreach ($ventasPendientes as $venta): ?>
                            <option value="<?php echo $venta['id']; ?>" 
                                    data-monto="<?php echo $venta['total']; ?>">
                                V<?php echo str_pad($venta['id'], 3, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($venta['cliente_nombre']); ?> - $<?php echo number_format($venta['total'], 0, ',', '.'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Método de Pago:</label>
                    <select name="metodo_pago" required>
                        <option value="">Seleccionar método</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Monto:</label>
                    <input type="number" name="monto" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Fecha de Pago:</label>
                    <input type="date" name="fecha_pago" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Referencia/Comprobante:</label>
                <input type="text" name="referencia" placeholder="Número de referencia o comprobante">
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-danger" onclick="hideVentaPaymentForm()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Venta</th>
                <th>Cliente</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pagosVentas): ?>
                <?php foreach ($pagosVentas as $pago): ?>
                    <tr>
                        <td>PV<?php echo str_pad($pago['id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td>V<?php echo str_pad($pago['venta_id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($pago['cliente_nombre']); ?></td>
                        <td><?php echo ucfirst($pago['metodo_pago']); ?></td>
                        <td>$<?php echo number_format($pago['monto'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $pago['estado']; ?>">
                                <?php echo ucfirst($pago['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                        <td><?php echo htmlspecialchars($pago['referencia']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                        No hay pagos de ventas registrados
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Tab Pagos a Campesinos -->
<div id="compras-tab" class="tab-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h4>Pagos a Campesinos</h4>
        <button class="btn btn-info" onclick="showCompraPaymentForm()">
            <i class="fas fa-plus"></i> Nuevo Pago a Campesino
        </button>
    </div>

    <div id="compra-payment-form" style="display: none; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h4 style="margin-bottom: 1rem;">Registrar Pago a Campesino</h4>
        <form method="POST">
            <input type="hidden" name="action" value="create_compra_pago">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Compra Asociada:</label>
                    <select name="compra_id" required onchange="updateMontoCompra(this)">
                        <option value="">Seleccionar compra</option>
                        <?php foreach ($comprasPendientes as $compra): ?>
                            <option value="<?php echo $compra['id']; ?>" 
                                    data-monto="<?php echo $compra['total']; ?>">
                                C<?php echo str_pad($compra['id'], 3, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($compra['campesino_nombre']); ?> - $<?php echo number_format($compra['total'], 0, ',', '.'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Método de Pago:</label>
                    <select name="metodo_pago" required>
                        <option value="">Seleccionar método</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Monto:</label>
                    <input type="number" name="monto" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Fecha de Pago:</label>
                    <input type="date" name="fecha_pago" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Referencia/Comprobante:</label>
                <input type="text" name="referencia" placeholder="Número de referencia o comprobante">
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-danger" onclick="hideCompraPaymentForm()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Compra</th>
                <th>Campesino</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pagosCampesinos): ?>
                <?php foreach ($pagosCampesinos as $pago): ?>
                    <tr>
                        <td>PC<?php echo str_pad($pago['id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td>C<?php echo str_pad($pago['compra_id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($pago['campesino_nombre']); ?></td>
                        <td><?php echo ucfirst($pago['metodo_pago']); ?></td>
                        <td>$<?php echo number_format($pago['monto'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $pago['estado']; ?>">
                                <?php echo ucfirst($pago['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                        <td><?php echo htmlspecialchars($pago['referencia']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                        No hay pagos a campesinos registrados
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function showTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Mostrar tab seleccionado
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

function showVentaPaymentForm() {
    document.getElementById('venta-payment-form').style.display = 'block';
}

function hideVentaPaymentForm() {
    document.getElementById('venta-payment-form').style.display = 'none';
}

function showCompraPaymentForm() {
    document.getElementById('compra-payment-form').style.display = 'block';
}

function hideCompraPaymentForm() {
    document.getElementById('compra-payment-form').style.display = 'none';
}

function updateMontoVenta(select) {
    const selectedOption = select.options[select.selectedIndex];
    const monto = selectedOption.getAttribute('data-monto');
    if (monto) {
        document.querySelector('#venta-payment-form input[name="monto"]').value = monto;
    }
}

function updateMontoCompra(select) {
    const selectedOption = select.options[select.selectedIndex];
    const monto = selectedOption.getAttribute('data-monto');
    if (monto) {
        document.querySelector('#compra-payment-form input[name="monto"]').value = monto;
    }
}
</script>
