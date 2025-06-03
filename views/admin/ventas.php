<?php
require_once 'config/database.php';

// Procesar formulario
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
                
                if (insertRecord('ventas', $data)) {
                    // Registrar precio histórico
                    $precioHistorico = [
                        'tipo_cafe_id' => $data['tipo_cafe_id'],
                        'precio' => $data['precio_kg'],
                        'fecha_precio' => $data['fecha_venta'],
                        'tipo_operacion' => 'venta'
                    ];
                    insertRecord('precios_historicos', $precioHistorico);
                    
                    $success = "Venta registrada exitosamente";
                } else {
                    $error = "Error al registrar la venta";
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
                
                if (updateRecord('ventas', $data, 'id = ?', [$id])) {
                    $success = "Venta actualizada exitosamente";
                } else {
                    $error = "Error al actualizar la venta";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                
                // Eliminar directamente de la base de datos
                $sql = "DELETE FROM ventas WHERE id = ?";
                if (executeQuery($sql, [$id])) {
                    $success = "Venta eliminada exitosamente";
                } else {
                    $error = "Error al eliminar la venta";
                }
                break;
        }
    }
}

// Obtener ventas con detalles
$ventas = fetchAll("
    SELECT v.*, 
           COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
           tc.nombre as cafe_nombre,
           tc.variedad
    FROM ventas v 
    LEFT JOIN cooperativas c ON v.cooperativa_id = c.id 
    JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id 
    WHERE v.estado != 'cancelada'
    ORDER BY v.fecha_venta DESC
");

// Obtener cooperativas y tipos de café para los formularios
$cooperativas = fetchAll("SELECT id, nombre FROM cooperativas WHERE activo = 1 ORDER BY nombre");

// Verificar si hay cooperativas disponibles
if (!$cooperativas) {
    $cooperativas = [];
}
$tiposCafe = fetchAll("SELECT id, nombre, precio_base FROM tipos_cafe WHERE activo = 1 ORDER BY nombre");

// Obtener venta específica para editar
$editVenta = null;
if (isset($_GET['edit'])) {
    $editVenta = fetchOne("SELECT * FROM ventas WHERE id = ?", [$_GET['edit']]);
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

    .status-completada {
        background: #d4edda;
        color: #155724;
    }

    .status-pendiente {
        background: #fff3cd;
        color: #856404;
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
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editVenta ? 'update' : 'create'; ?>">
        <?php if ($editVenta): ?>
            <input type="hidden" name="id" value="<?php echo $editVenta['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Cliente/Cooperativa:</label>
                <select name="cooperativa_id" onchange="toggleClienteNombre(this)">
                    <option value="">Seleccionar cooperativa</option>
                    <?php if ($cooperativas): ?>
                        <?php foreach ($cooperativas as $cooperativa): ?>
                            <option value="<?php echo $cooperativa['id']; ?>" 
                                    <?php echo ($editVenta && $editVenta['cooperativa_id'] == $cooperativa['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cooperativa['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hay cooperativas registradas</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group" id="cliente-nombre-group" style="<?php echo ($editVenta && $editVenta['cooperativa_id']) ? 'display: none;' : ''; ?>">
                <label>O Cliente Individual:</label>
                <input type="text" name="cliente_nombre" placeholder="Nombre del cliente individual"
                       value="<?php echo $editVenta ? htmlspecialchars($editVenta['cliente_nombre']) : ''; ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de Café:</label>
                <select name="tipo_cafe_id" required onchange="updatePrecioVenta(this)">
                    <option value="">Seleccionar tipo</option>
                    <?php foreach ($tiposCafe as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>" 
                                data-precio="<?php echo $tipo['precio_base'] * 1.25; ?>"
                                <?php echo ($editVenta && $editVenta['tipo_cafe_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad (kg):</label>
                <input type="number" name="cantidad" step="0.01" required 
                       value="<?php echo $editVenta ? $editVenta['cantidad'] : ''; ?>"
                       onchange="calcularTotalVenta()">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Precio por kg:</label>
                <input type="number" name="precio_kg" step="0.01" required 
                       value="<?php echo $editVenta ? $editVenta['precio_kg'] : ''; ?>"
                       onchange="calcularTotalVenta()">
            </div>
            <div class="form-group">
                <label>Fecha de Venta:</label>
                <input type="date" name="fecha_venta" required 
                       value="<?php echo $editVenta ? $editVenta['fecha_venta'] : date('Y-m-d'); ?>">
            </div>
        </div>
        <?php if ($editVenta): ?>
            <div class="form-group">
                <label>Estado:</label>
                <select name="estado" required>
                    <option value="pendiente" <?php echo ($editVenta['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="completada" <?php echo ($editVenta['estado'] == 'completada') ? 'selected' : ''; ?>>Completada</option>
                </select>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Total Estimado:</label>
            <input type="text" id="total-estimado-venta" readonly style="background: #f8f9fa; font-weight: bold;">
        </div>
        <button type="submit" class="btn btn-success">
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
                    <td>$<?php echo number_format($venta['precio_kg'], 0, ',', '.'); ?></td>
                    <td>$<?php echo number_format($venta['total'], 0, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($venta['fecha_venta'])); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $venta['estado']; ?>">
                            <?php echo ucfirst($venta['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?view=ventas&edit=<?php echo $venta['id']; ?>" class="btn" style="padding: 0.5rem;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de cancelar esta venta?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $venta['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem;">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center; padding: 2rem; color: #666;">
                    No hay ventas registradas
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

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

function updatePrecioVenta(select) {
    const selectedOption = select.options[select.selectedIndex];
    const precio = selectedOption.getAttribute('data-precio');
    if (precio) {
        document.querySelector('input[name="precio_kg"]').value = precio;
        calcularTotalVenta();
    }
}

function calcularTotalVenta() {
    const cantidad = parseFloat(document.querySelector('input[name="cantidad"]').value) || 0;
    const precio = parseFloat(document.querySelector('input[name="precio_kg"]').value) || 0;
    const total = cantidad * precio;
    
    document.getElementById('total-estimado-venta').value = '$' + total.toLocaleString('es-CO');
}

// Calcular total inicial si estamos editando
document.addEventListener('DOMContentLoaded', function() {
    calcularTotalVenta();
});
</script>


