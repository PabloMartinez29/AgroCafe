<?php
require_once 'config/database.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'campesino_id' => intval($_POST['campesino_id']),
                    'tipo_cafe_id' => intval($_POST['tipo_cafe_id']),
                    'cantidad' => floatval($_POST['cantidad']),
                    'precio_kg' => floatval($_POST['precio_kg']),
                    'fecha_compra' => $_POST['fecha_compra'],
                    'estado' => 'completada'
                ];
                
                if (insertRecord('compras', $data)) {
                    // Registrar precio histórico
                    $precioHistorico = [
                        'tipo_cafe_id' => $data['tipo_cafe_id'],
                        'precio' => $data['precio_kg'],
                        'fecha_precio' => $data['fecha_compra'],
                        'tipo_operacion' => 'compra'
                    ];
                    insertRecord('precios_historicos', $precioHistorico);
                    
                    $success = "Compra registrada exitosamente";
                } else {
                    $error = "Error al registrar la compra";
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $data = [
                    'campesino_id' => intval($_POST['campesino_id']),
                    'tipo_cafe_id' => intval($_POST['tipo_cafe_id']),
                    'cantidad' => floatval($_POST['cantidad']),
                    'precio_kg' => floatval($_POST['precio_kg']),
                    'fecha_compra' => $_POST['fecha_compra'],
                    'estado' => $_POST['estado']
                ];
                
                if (updateRecord('compras', $data, 'id = ?', [$id])) {
                    $success = "Compra actualizada exitosamente";
                } else {
                    $error = "Error al actualizar la compra";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                
                // Eliminar directamente sin validaciones de dependencias
                $sql = "DELETE FROM compras WHERE id = ?";
                if (executeQuery($sql, [$id])) {
                    $success = "Compra eliminada exitosamente";
                } else {
                    $error = "Error al eliminar la compra";
                }
                break;
        }
    }
}

// Obtener compras con detalles
$compras = fetchAll("SELECT c.*, u.nombre as campesino_nombre, tc.nombre as cafe_nombre 
                     FROM compras c 
                     JOIN usuarios u ON c.campesino_id = u.id 
                     JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id 
                     WHERE c.estado != 'cancelada'
                     ORDER BY c.fecha_compra DESC");

// Obtener campesinos y tipos de café para los formularios
$campesinos = fetchAll("SELECT id, nombre FROM usuarios WHERE rol = 'campesino' AND activo = 1 ORDER BY nombre");
$tiposCafe = fetchAll("SELECT id, nombre, precio_base FROM tipos_cafe WHERE activo = 1 ORDER BY nombre");

// Obtener compra específica para editar
$editCompra = null;
if (isset($_GET['edit'])) {
    $editCompra = fetchOne("SELECT * FROM compras WHERE id = ?", [$_GET['edit']]);
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
    <h3>Gestión de Compras</h3>
    <button class="btn" onclick="showPurchaseForm()">
        <i class="fas fa-plus"></i> Nueva Compra
    </button>
</div>

<div id="purchase-form" style="display: <?php echo $editCompra ? 'block' : 'none'; ?>; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
    <h4 style="margin-bottom: 1rem;">
        <?php echo $editCompra ? 'Editar' : 'Registrar Nueva'; ?> Compra
    </h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editCompra ? 'update' : 'create'; ?>">
        <?php if ($editCompra): ?>
            <input type="hidden" name="id" value="<?php echo $editCompra['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Campesino:</label>
                <select name="campesino_id" required>
                    <option value="">Seleccionar campesino</option>
                    <?php foreach ($campesinos as $campesino): ?>
                        <option value="<?php echo $campesino['id']; ?>" 
                                <?php echo ($editCompra && $editCompra['campesino_id'] == $campesino['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($campesino['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tipo de Café:</label>
                <select name="tipo_cafe_id" required onchange="updatePrecio(this)">
                    <option value="">Seleccionar tipo</option>
                    <?php foreach ($tiposCafe as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>" 
                                data-precio="<?php echo $tipo['precio_base']; ?>"
                                <?php echo ($editCompra && $editCompra['tipo_cafe_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Cantidad (kg):</label>
                <input type="number" name="cantidad" step="0.01" required 
                       value="<?php echo $editCompra ? $editCompra['cantidad'] : ''; ?>"
                       onchange="calcularTotal()">
            </div>
            <div class="form-group">
                <label>Precio por kg:</label>
                <input type="number" name="precio_kg" step="0.01" required 
                       value="<?php echo $editCompra ? $editCompra['precio_kg'] : ''; ?>"
                       onchange="calcularTotal()">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Fecha de Compra:</label>
                <input type="date" name="fecha_compra" required 
                       value="<?php echo $editCompra ? $editCompra['fecha_compra'] : date('Y-m-d'); ?>">
            </div>
            <?php if ($editCompra): ?>
                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado" required>
                        <option value="pendiente" <?php echo ($editCompra['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="completada" <?php echo ($editCompra['estado'] == 'completada') ? 'selected' : ''; ?>>Completada</option>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Total Estimado:</label>
            <input type="text" id="total-estimado" readonly style="background: #f8f9fa; font-weight: bold;">
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> <?php echo $editCompra ? 'Actualizar' : 'Guardar'; ?>
        </button>
        <button type="button" class="btn btn-danger" onclick="hidePurchaseForm()">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Campesino</th>
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
        <?php if ($compras): ?>
            <?php foreach ($compras as $compra): ?>
                <tr>
                    <td>C<?php echo str_pad($compra['id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($compra['campesino_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($compra['cafe_nombre']); ?></td>
                    <td><?php echo number_format($compra['cantidad'], 2); ?></td>
                    <td>$<?php echo number_format($compra['precio_kg'], 0, ',', '.'); ?></td>
                    <td>$<?php echo number_format($compra['total'], 0, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($compra['fecha_compra'])); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $compra['estado']; ?>">
                            <?php echo ucfirst($compra['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?view=compras&edit=<?php echo $compra['id']; ?>" class="btn" style="padding: 0.5rem;" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta compra?\n\nEsta acción no se puede deshacer.')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $compra['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem;" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center; padding: 2rem; color: #666;">
                    No hay compras registradas
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function showPurchaseForm() {
    document.getElementById('purchase-form').style.display = 'block';
}

function hidePurchaseForm() {
    document.getElementById('purchase-form').style.display = 'none';
    if (window.location.href.includes('edit=')) {
        window.location.href = '?view=compras';
    }
}

function updatePrecio(select) {
    const selectedOption = select.options[select.selectedIndex];
    const precio = selectedOption.getAttribute('data-precio');
    if (precio) {
        document.querySelector('input[name="precio_kg"]').value = precio;
        calcularTotal();
    }
}

function calcularTotal() {
    const cantidad = parseFloat(document.querySelector('input[name="cantidad"]').value) || 0;
    const precio = parseFloat(document.querySelector('input[name="precio_kg"]').value) || 0;
    const total = cantidad * precio;
    
    document.getElementById('total-estimado').value = '$' + total.toLocaleString('es-CO');
}

// Calcular total inicial si estamos editando
document.addEventListener('DOMContentLoaded', function() {
    calcularTotal();
});
</script>

