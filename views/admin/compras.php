<?php
require_once 'config/database.php';

// Iniciar búfer de salida
ob_start();

// Procesar formulario
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $data = [
                'campesino_id' => intval($_POST['campesino_id']),
                'tipo_cafe_id' => intval($_POST['tipo_cafe_id']),
                'cantidad' => floatval($_POST['cantidad']),
                'precio_kg' => floatval($_POST['precio_kg']),
                'fecha_compra' => $_POST['fecha_compra'],
                'estado' => 'completada',
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            
            $cajaAbierta = fetchOne("SELECT * FROM cajas WHERE estado = 'abierto' LIMIT 1");
            if ($cajaAbierta) {
                $total = $data['cantidad'] * $data['precio_kg'];
                if ($cajaAbierta['saldo_disponible'] >= $total) {
                    if (insertRecord('compras', $data)) {
                        // Registrar precio histórico
                        $precioHistorico = [
                            'tipo_cafe_id' => $data['tipo_cafe_id'],
                            'precio' => $data['precio_kg'],
                            'fecha_precio' => $data['fecha_compra'],
                            'tipo_operacion' => 'compra'
                        ];
                        insertRecord('precios_historicos', $precioHistorico);
                        
                        // Actualizar caja
                        $nuevoSaldo = $cajaAbierta['saldo_disponible'] - $total;
                        $sqlUpdate = "UPDATE cajas SET saldo_disponible = ?, kilos_comprados = kilos_comprados + ? WHERE id = ?";
                        executeQuery($sqlUpdate, [$nuevoSaldo, $data['cantidad'], $cajaAbierta['id']]);
                        
                        $success = "Compra registrada exitosamente. Saldo actualizado.";
                        header("Location: dashboard_admin.php?view=compras&success=true");
                        ob_end_clean();
                        exit();
                    } else {
                        $error = "Error al registrar la compra en la base de datos";
                    }
                } else {
                    $error = "Saldo insuficiente en la caja. Saldo disponible: $" . number_format($cajaAbierta['saldo_disponible'], 0, ',', '.') . ", Total compra: $" . number_format($total, 0, ',', '.');
                }
            } else {
                $error = "No hay una caja abierta";
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
                'estado' => $_POST['estado'],
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            
            if (updateRecord('compras', $data, 'id = ?', [$id])) {
                $success = "Compra actualizada exitosamente";
            } else {
                $error = "Error al actualizar la compra";
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id']);
            
            $sql = "DELETE FROM compras WHERE id = ?";
            if (executeQuery($sql, [$id])) {
                $success = "Compra eliminada exitosamente";
            } else {
                $error = "Error al eliminar la compra";
            }
            break;
    }
}

// Obtener compras con detalles MEJORADO - incluye tipo de procesamiento
$compras = fetchAll("SELECT c.*, u.nombre as campesino_nombre, tc.nombre as cafe_nombre, tc.tipo_procesamiento
                     FROM compras c 
                     JOIN usuarios u ON c.campesino_id = u.id 
                     JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id 
                     WHERE c.estado != 'cancelada'
                     ORDER BY c.fecha_compra DESC");

// Obtener campesinos y tipos de café para los formularios
$campesinos = fetchAll("SELECT id, nombre FROM usuarios WHERE rol = 'campesino' AND activo = 1 ORDER BY nombre");

// MEJORADO: Obtener tipos de café organizados por procesamiento
$tiposCafeCompletos = fetchAll("SELECT id, nombre, precio_base, tipo_procesamiento FROM tipos_cafe WHERE activo = 1 ORDER BY tipo_procesamiento, nombre");

// Organizar tipos de café por procesamiento para JavaScript
$tiposPorProcesamiento = [];
foreach ($tiposCafeCompletos as $tipo) {
    $procesamiento = $tipo['tipo_procesamiento'] ?? 'normal';
    $tiposPorProcesamiento[$procesamiento][] = $tipo;
}

// Obtener compra específica para editar
$editCompra = null;
if (isset($_GET['edit'])) {
    $editCompra = fetchOne("SELECT * FROM compras WHERE id = ?", [$_GET['edit']]);
}
?>

<!-- Modal de Notificación -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> Compra registrada exitosamente. Saldo actualizado.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Mostrar modal si hay mensaje -->
<?php if ($success || $error || isset($_GET['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new bootstrap.Modal(document.getElementById('notificationModal')).show();
            <?php
            if (isset($_GET['success'])) {
                echo "window.history.replaceState({}, document.title, window.location.pathname);";
            }
            ?>
        });
    </script>
<?php endif; ?>

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

    /* ESTILOS PARA PROCESAMIENTO SIN EMOJIS */
    .processing-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .processing-normal {
        background: #007bff;
        color: white;
    }

    .processing-mojado {
        background: #17a2b8;
        color: white;
    }

    .processing-seco {
        background: #fd7e14;
        color: white;
    }

    .processing-pasilla {
        background: #6f42c1;
        color: white;
    }

    /* FILTRO NORMAL IGUAL QUE OTROS CAMPOS */
    .filter-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 2px solid #8B4513;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

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
    
    <!-- FILTRO NORMAL SIN EMOJIS -->
    <div class="filter-section">
        <h5 style="color: #8B4513; margin-bottom: 1rem;">
            <i class="fas fa-filter"></i> Filtrar por Tipo de Procesamiento
        </h5>
        <div class="form-group">
            <label>Selecciona el tipo de procesamiento del café:</label>
            <select id="filtro-procesamiento" onchange="filtrarTiposCafe()">
                <option value="">-- Seleccionar tipo de procesamiento --</option>
                <option value="normal">Normal</option>
                <option value="mojado">Mojado</option>
                <option value="seco">Seco</option>
                <option value="pasilla">Pasilla</option>
            </select>
        </div>
    </div>
    
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
                <select name="tipo_cafe_id" id="tipo-cafe-select" required onchange="updatePrecio(this)" disabled>
                    <option value="">Primero selecciona el tipo de procesamiento</option>
                    <?php if ($editCompra): ?>
                        <?php foreach ($tiposCafeCompletos as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>" 
                                    data-precio="<?php echo $tipo['precio_base']; ?>"
                                    data-procesamiento="<?php echo $tipo['tipo_procesamiento'] ?? 'normal'; ?>"
                                    <?php echo ($editCompra['tipo_cafe_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
        <div class="form-group">
            <label>Observaciones:</label>
            <textarea name="observaciones" rows="3" placeholder="Ej: Calidad del café, condiciones de pago, etc."><?php echo $editCompra ? htmlspecialchars($editCompra['observaciones'] ?? '') : ''; ?></textarea>
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
            <th>Procesamiento</th>
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
                    <td>
                        <span class="processing-badge processing-<?php echo $compra['tipo_procesamiento'] ?? 'normal'; ?>">
                            <?php echo ucfirst($compra['tipo_procesamiento'] ?? 'Normal'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($compra['cafe_nombre']); ?></td>
                    <td><?php echo number_format($compra['cantidad'], 2); ?></td>
                    <td>$<?php echo number_format($compra['precio_kg'], 0, ',', '.'); ?></td>
                    <td>$<?php echo number_format($compra['cantidad'] * $compra['precio_kg'], 0, ',', '.'); ?></td>
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
                <td colspan="10" style="text-align: center; padding: 2rem; color: #666;">
                    No hay compras registradas
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
// DATOS DE TIPOS DE CAFÉ ORGANIZADOS POR PROCESAMIENTO
const tiposPorProcesamiento = <?php echo json_encode($tiposPorProcesamiento); ?>;

function showPurchaseForm() {
    document.getElementById('purchase-form').style.display = 'block';
}

function hidePurchaseForm() {
    document.getElementById('purchase-form').style.display = 'none';
    if (window.location.href.includes('edit=')) {
        window.location.href = '?view=compras';
    }
}

// FUNCIÓN: Filtrar tipos de café por procesamiento
function filtrarTiposCafe() {
    const filtro = document.getElementById('filtro-procesamiento').value;
    const selectTipoCafe = document.getElementById('tipo-cafe-select');
    
    // Limpiar opciones actuales
    selectTipoCafe.innerHTML = '<option value="">Seleccionar tipo de café</option>';
    
    if (filtro && tiposPorProcesamiento[filtro]) {
        // Habilitar el select
        selectTipoCafe.disabled = false;
        
        // Agregar opciones filtradas
        tiposPorProcesamiento[filtro].forEach(function(tipo) {
            const option = document.createElement('option');
            option.value = tipo.id;
            option.textContent = tipo.nombre;
            option.setAttribute('data-precio', tipo.precio_base);
            selectTipoCafe.appendChild(option);
        });
    } else {
        // Deshabilitar el select si no hay filtro
        selectTipoCafe.disabled = true;
        selectTipoCafe.innerHTML = '<option value="">Primero selecciona el tipo de procesamiento</option>';
    }
    
    // Limpiar precio y total
    document.querySelector('input[name="precio_kg"]').value = '';
    calcularTotal();
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
    
    // Si estamos editando, configurar el filtro automáticamente
    <?php if ($editCompra): ?>
        const tipoEditando = <?php echo $editCompra['tipo_cafe_id']; ?>;
        const selectTipoCafe = document.getElementById('tipo-cafe-select');
        const opcionSeleccionada = selectTipoCafe.querySelector('option[value="' + tipoEditando + '"]');
        
        if (opcionSeleccionada) {
            const procesamiento = opcionSeleccionada.getAttribute('data-procesamiento');
            document.getElementById('filtro-procesamiento').value = procesamiento;
            filtrarTiposCafe();
            selectTipoCafe.value = tipoEditando;
        }
    <?php endif; ?>
});
</script>
