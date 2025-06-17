<?php
require_once 'config/database.php';

// Inicializar variables de alerta desde la sesión
$show_alert = false;
$alert_type = '';
$alert_title = '';
$alert_message = '';

// Verificar si hay una alerta pendiente en la sesión
if (isset($_SESSION['pending_alert'])) {
    $show_alert = true;
    $alert_type = $_SESSION['pending_alert']['type'];
    $alert_title = $_SESSION['pending_alert']['title'];
    $alert_message = $_SESSION['pending_alert']['message'];
    
    // Limpiar la alerta de la sesión después de obtenerla
    unset($_SESSION['pending_alert']);
}

// Procesar formulario de ajuste y eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'ajuste') {
        $data = [
            'tipo_cafe_id' => intval($_POST['tipo_cafe_id']),
            'cantidad' => floatval($_POST['cantidad']),
            'tipo_movimiento' => 'ajuste',
            'motivo' => $_POST['motivo'],
            'fecha_movimiento' => date('Y-m-d H:i:s')
        ];
        
        // Agregar usuario_id si la columna existe
        if (isset($_SESSION['user_id'])) {
            $data['usuario_id'] = $_SESSION['user_id'];
        }
        
        if (insertRecord('inventario_movimientos', $data)) {
            // Guardar alerta en sesión y redirigir
            $_SESSION['pending_alert'] = [
                'type' => 'success',
                'title' => '¡Ajuste Registrado!',
                'message' => 'El movimiento de inventario ha sido guardado correctamente.'
            ];
        } else {
            // Guardar alerta de error en sesión y redirigir
            $_SESSION['pending_alert'] = [
                'type' => 'error',
                'title' => 'Error en el Ajuste',
                'message' => 'No se pudo guardar el movimiento. Inténtalo nuevamente.'
            ];
        }
        
        // Redirigir para evitar reenvío del formulario
        header('Location: ' . $_SERVER['PHP_SELF'] . '?view=bodega');
        exit;
    }
    
    // FUNCIONALIDAD DE ELIMINACIÓN
    elseif ($_POST['action'] == 'eliminar') {
        $tipo_cafe_id = intval($_POST['tipo_cafe_id']);
        
        if ($tipo_cafe_id <= 0) {
            $_SESSION['pending_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'ID de tipo de café inválido'
            ];
        } else {
            try {
                // Obtener nombre del café antes de eliminarlo
                $cafe_info = fetchOne("SELECT nombre FROM tipos_cafe WHERE id = ?", [$tipo_cafe_id]);
                $nombre_cafe = $cafe_info ? $cafe_info['nombre'] : 'Desconocido';
                
                // Verificar si hay transacciones relacionadas
                $compras = fetchOne("SELECT COUNT(*) as total FROM compras WHERE tipo_cafe_id = ?", [$tipo_cafe_id]);
                $ventas = fetchOne("SELECT COUNT(*) as total FROM ventas WHERE tipo_cafe_id = ?", [$tipo_cafe_id]);
                $movimientos = fetchOne("SELECT COUNT(*) as total FROM inventario_movimientos WHERE tipo_cafe_id = ?", [$tipo_cafe_id]);
                
                $total_transacciones = $compras['total'] + $ventas['total'] + $movimientos['total'];
                
                if ($total_transacciones > 0) {
                    // Si hay transacciones, marcar como inactivo
                    $result = executeQuery("UPDATE tipos_cafe SET activo = 0 WHERE id = ?", [$tipo_cafe_id]);
                    if ($result) {
                        $_SESSION['pending_alert'] = [
                            'type' => 'warning',
                            'title' => 'Registro Desactivado',
                            'message' => "El tipo de café '<strong>$nombre_cafe</strong>' ha sido desactivado.<br><br>Se mantiene el historial de <strong>$total_transacciones transacciones</strong>.<br><br>El registro ya no aparecerá en nuevas operaciones."
                        ];
                    } else {
                        $_SESSION['pending_alert'] = [
                            'type' => 'error',
                            'title' => 'Error al Desactivar',
                            'message' => "No se pudo desactivar el tipo de café '<strong>$nombre_cafe</strong>'. Inténtalo nuevamente."
                        ];
                    }
                } else {
                    // Si no hay transacciones, eliminar completamente
                    $result = executeQuery("DELETE FROM tipos_cafe WHERE id = ?", [$tipo_cafe_id]);
                    if ($result) {
                        $_SESSION['pending_alert'] = [
                            'type' => 'success',
                            'title' => '¡Eliminación Exitosa!',
                            'message' => "El tipo de café '<strong>$nombre_cafe</strong>' ha sido eliminado completamente del sistema.<br><br><i class='fas fa-check-circle'></i> No tenía transacciones asociadas."
                        ];
                    } else {
                        $_SESSION['pending_alert'] = [
                            'type' => 'error',
                            'title' => 'Error en la Eliminación',
                            'message' => "No se pudo eliminar el tipo de café '<strong>$nombre_cafe</strong>'. Inténtalo nuevamente."
                        ];
                    }
                }
            } catch (Exception $e) {
                error_log("Error al eliminar tipo_cafe_id $tipo_cafe_id: " . $e->getMessage());
                $_SESSION['pending_alert'] = [
                    'type' => 'error',
                    'title' => 'Error Inesperado',
                    'message' => "Ocurrió un problema al procesar la eliminación:<br><br><em>" . $e->getMessage() . "</em>"
                ];
            }
        }
        
        // Redirigir para evitar reenvío del formulario
        header('Location: ' . $_SERVER['PHP_SELF'] . '?view=bodega');
        exit;
    }
}

// Verificar si existe la columna tipo_procesamiento
$check_column = fetchOne("SHOW COLUMNS FROM tipos_cafe LIKE 'tipo_procesamiento'");
$has_processing = !empty($check_column);

// Obtener inventario actual (adaptado según si existe la columna)
if ($has_processing) {
    $inventario_query = "
        SELECT 
            tc.id,
            tc.nombre,
            tc.variedad,
            tc.tipo_procesamiento,
            tc.precio_base,
            COALESCE(SUM(CASE WHEN c.estado = 'completada' THEN c.cantidad ELSE 0 END), 0) as kilos_comprados,
            COALESCE(SUM(CASE WHEN v.estado = 'completada' THEN v.cantidad ELSE 0 END), 0) as kilos_vendidos,
            COALESCE(SUM(CASE WHEN im.tipo_movimiento = 'ajuste' THEN im.cantidad ELSE 0 END), 0) as ajustes,
            (COALESCE(SUM(CASE WHEN c.estado = 'completada' THEN c.cantidad ELSE 0 END), 0) - 
             COALESCE(SUM(CASE WHEN v.estado = 'completada' THEN v.cantidad ELSE 0 END), 0) +
             COALESCE(SUM(CASE WHEN im.tipo_movimiento = 'ajuste' THEN im.cantidad ELSE 0 END), 0)) as stock_actual
        FROM tipos_cafe tc
        LEFT JOIN compras c ON tc.id = c.tipo_cafe_id
        LEFT JOIN ventas v ON tc.id = v.tipo_cafe_id
        LEFT JOIN inventario_movimientos im ON tc.id = im.tipo_cafe_id
        WHERE tc.activo = 1
        GROUP BY tc.id, tc.nombre, tc.variedad, tc.tipo_procesamiento, tc.precio_base
        ORDER BY tc.tipo_procesamiento, tc.nombre
    ";
} else {
    $inventario_query = "
        SELECT 
            tc.id,
            tc.nombre,
            tc.variedad,
            'normal' as tipo_procesamiento,
            tc.precio_base,
            COALESCE(SUM(CASE WHEN c.estado = 'completada' THEN c.cantidad ELSE 0 END), 0) as kilos_comprados,
            COALESCE(SUM(CASE WHEN v.estado = 'completada' THEN v.cantidad ELSE 0 END), 0) as kilos_vendidos,
            COALESCE(SUM(CASE WHEN im.tipo_movimiento = 'ajuste' THEN im.cantidad ELSE 0 END), 0) as ajustes,
            (COALESCE(SUM(CASE WHEN c.estado = 'completada' THEN c.cantidad ELSE 0 END), 0) - 
             COALESCE(SUM(CASE WHEN v.estado = 'completada' THEN v.cantidad ELSE 0 END), 0) +
             COALESCE(SUM(CASE WHEN im.tipo_movimiento = 'ajuste' THEN im.cantidad ELSE 0 END), 0)) as stock_actual
        FROM tipos_cafe tc
        LEFT JOIN compras c ON tc.id = c.tipo_cafe_id
        LEFT JOIN ventas v ON tc.id = v.tipo_cafe_id
        LEFT JOIN inventario_movimientos im ON tc.id = im.tipo_cafe_id
        WHERE tc.activo = 1
        GROUP BY tc.id, tc.nombre, tc.variedad, tc.precio_base
        ORDER BY tc.nombre
    ";
}

$inventario = fetchAll($inventario_query);

// Calcular totales
$total_kilos = 0;
$total_valor = 0;
foreach ($inventario as $item) {
    $total_kilos += $item['stock_actual'];
    $total_valor += $item['stock_actual'] * $item['precio_base'];
}
$total_tipos = count($inventario);

// Obtener movimientos recientes
$movimientos = fetchAll("
    SELECT im.*, tc.nombre as cafe_nombre
    FROM inventario_movimientos im
    JOIN tipos_cafe tc ON im.tipo_cafe_id = tc.id
    ORDER BY im.fecha_movimiento DESC
    LIMIT 20
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Bodega</title>
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .stock-alto { 
            background-color: #d4edda; 
            color: #155724; 
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .stock-medio { 
            background-color: #fff3cd; 
            color: #856404; 
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .stock-bajo { 
            background-color: #f8d7da; 
            color: #721c24; 
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .stock-agotado { 
            background-color: #f5c6cb; 
            color: #721c24; 
            font-weight: bold; 
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        
        .processing-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .processing-normal { background: #007bff; color: white; }
        .processing-mojado { background: #17a2b8; color: white; }
        .processing-seco { background: #fd7e14; color: white; }
        .processing-pasilla { background: #6f42c1; color: white; }
        
        .dashboard-stats {
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
        
        .filter-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 2px solid #8B4513;
        }

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

        .btn-danger {
            background: #dc3545;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
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
            padding: 0.75rem;
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

        .alert {
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            .table {
                font-size: 0.9rem;
            }
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }

        /* Personalización de SweetAlert2 */
        .swal2-popup {
            border-radius: 15px;
        }
        
        .swal2-title {
            font-size: 1.5rem;
        }
        
        .swal2-content {
            font-size: 1rem;
        }
    </style>
</head>
<body>

<!-- Estado General -->
<div class="dashboard-stats">
    <div class="stat-card">
        <h3><?php echo number_format($total_kilos, 2); ?></h3>
        <p><i class="fas fa-weight"></i> Total Kilos</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($total_valor, 0, '.', ','); ?></h3>
        <p><i class="fas fa-dollar-sign"></i> Valor Inventario</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_tipos; ?></h3>
        <p><i class="fas fa-coffee"></i> Tipos de Café</p>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h3><i class="fas fa-warehouse"></i> Inventario de Bodega</h3>
    <button class="btn" onclick="showAjusteForm()">
        <i class="fas fa-plus"></i> Registrar Ajuste
    </button>
</div>

<!-- Filtros (solo si existe la columna tipo_procesamiento) -->
<?php if ($has_processing): ?>
<div class="filter-section">
    <h5 style="color: #8B4513; margin-bottom: 1rem;">
        <i class="fas fa-filter"></i> Filtros
    </h5>
    <div class="form-row">
        <div class="form-group">
            <label>Filtrar por Procesamiento:</label>
            <select id="filtro-procesamiento" onchange="filtrarInventario()">
                <option value="">Todos los procesamientos</option>
                <option value="normal">Normal</option>
                <option value="mojado">Mojado</option>
                <option value="seco">Seco</option>
                <option value="pasilla">Pasilla</option>
            </select>
        </div>
        <div class="form-group">
            <label>Buscar por Nombre:</label>
            <input type="text" id="buscar-nombre" placeholder="Buscar tipo de café..." onkeyup="filtrarInventario()">
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Formulario de Ajuste -->
<div id="ajuste-form" style="display: none; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
    <h4 style="margin-bottom: 1rem;">Registrar Ajuste de Inventario</h4>
    <form method="POST">
        <input type="hidden" name="action" value="ajuste">
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de Café:</label>
                <select name="tipo_cafe_id" required>
                    <option value="">Seleccionar tipo</option>
                    <?php foreach ($inventario as $item): ?>
                        <option value="<?php echo $item['id']; ?>">
                            <?php echo htmlspecialchars($item['nombre']); ?> (Stock: <?php echo number_format($item['stock_actual'], 2); ?> kg)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad (+ para entrada, - para salida):</label>
                <input type="number" name="cantidad" step="0.01" required placeholder="Ej: 10.5 o -5.2">
            </div>
        </div>
        <div class="form-group">
            <label>Motivo del Ajuste:</label>
            <textarea name="motivo" rows="3" required placeholder="Ej: Merma por humedad, Error en conteo, Devolución de cliente..."></textarea>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Registrar Ajuste
        </button>
        <button type="button" class="btn btn-danger" onclick="hideAjusteForm()">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </form>
</div>

<!-- Tabla de Inventario -->
<table class="table" id="tabla-inventario">
    <thead>
        <tr>
            <th>Tipo de Café</th>
            <?php if ($has_processing): ?>
                <th>Procesamiento</th>
            <?php endif; ?>
            <th>Variedad</th>
            <th>Stock Actual</th>
            <th>Precio Base</th>
            <th>Valor Stock</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($inventario): ?>
            <?php foreach ($inventario as $item): ?>
                <?php
                $stock = $item['stock_actual'];
                $stock_class = '';
                $stock_text = '';
                
                if ($stock <= 0) {
                    $stock_class = 'stock-agotado';
                    $stock_text = 'AGOTADO';
                } elseif ($stock <= 10) {
                    $stock_class = 'stock-bajo';
                    $stock_text = 'BAJO';
                } elseif ($stock <= 50) {
                    $stock_class = 'stock-medio';
                    $stock_text = 'MEDIO';
                } else {
                    $stock_class = 'stock-alto';
                    $stock_text = 'ALTO';
                }
                ?>
                <tr class="fila-inventario" data-procesamiento="<?php echo $item['tipo_procesamiento']; ?>" data-nombre="<?php echo strtolower($item['nombre']); ?>">
                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                    <?php if ($has_processing): ?>
                        <td>
                            <span class="processing-badge processing-<?php echo $item['tipo_procesamiento']; ?>">
                                <?php echo ucfirst($item['tipo_procesamiento']); ?>
                            </span>
                        </td>
                    <?php endif; ?>
                    <td><?php echo ucfirst($item['variedad']); ?></td>
                    <td><?php echo number_format($stock, 2); ?> kg</td>
                    <td>$<?php echo number_format($item['precio_base'], 0, '.', ','); ?></td>
                    <td>$<?php echo number_format($stock * $item['precio_base'], 0, '.', ','); ?></td>
                    <td>
                        <span class="<?php echo $stock_class; ?>">
                            <?php echo $stock_text; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-small" 
                                onclick="confirmarEliminacion(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?>')"
                                title="Eliminar tipo de café">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?php echo $has_processing ? '8' : '7'; ?>" style="text-align: center; padding: 2rem; color: #666;">
                    No hay tipos de café registrados
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Movimientos Recientes -->
<h4 style="margin-top: 2rem;"><i class="fas fa-history"></i> Movimientos Recientes</h4>
<table class="table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo Café</th>
            <th>Cantidad</th>
            <th>Tipo</th>
            <th>Motivo</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($movimientos): ?>
            <?php foreach ($movimientos as $mov): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha_movimiento'])); ?></td>
                    <td><?php echo htmlspecialchars($mov['cafe_nombre']); ?></td>
                    <td style="color: <?php echo $mov['cantidad'] >= 0 ? 'green' : 'red'; ?>; font-weight: bold;">
                        <?php echo ($mov['cantidad'] >= 0 ? '+' : '') . number_format($mov['cantidad'], 2); ?> kg
                    </td>
                    <td><?php echo ucfirst($mov['tipo_movimiento']); ?></td>
                    <td><?php echo htmlspecialchars($mov['motivo'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">
                    No hay movimientos registrados
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Formulario oculto para eliminación -->
<form id="formEliminar" method="POST" style="display: none;">
    <input type="hidden" name="action" value="eliminar">
    <input type="hidden" name="tipo_cafe_id" id="idEliminar">
</form>

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let tipoIdEliminar = null;

// Mostrar SweetAlert si hay una operación completada (SOLO UNA VEZ)
<?php if ($show_alert): ?>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($alert_type == 'success'): ?>
    Swal.fire({
        icon: 'success',
        title: '<?php echo $alert_title; ?>',
        html: '<?php echo $alert_message; ?>',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#28a745',
        timer: 4000,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    
    <?php elseif ($alert_type == 'warning'): ?>
    Swal.fire({
        icon: 'warning',
        title: '<?php echo $alert_title; ?>',
        html: '<?php echo $alert_message; ?>',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#ffc107',
        timer: 5000,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    
    <?php elseif ($alert_type == 'error'): ?>
    Swal.fire({
        icon: 'error',
        title: '<?php echo $alert_title; ?>',
        html: '<?php echo $alert_message; ?>',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#dc3545',
        timer: 4000,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    <?php endif; ?>
});
<?php endif; ?>

function showAjusteForm() {
    document.getElementById('ajuste-form').style.display = 'block';
}

function hideAjusteForm() {
    document.getElementById('ajuste-form').style.display = 'none';
}

function confirmarEliminacion(id, nombre) {
    tipoIdEliminar = id;
    
    Swal.fire({
        title: '¿Confirmar Eliminación?',
        html: `¿Estás seguro de que deseas eliminar el tipo de café <strong>"${nombre}"</strong>?<br><br><small><i class="fas fa-info-circle"></i> Si tiene transacciones asociadas, se desactivará en lugar de eliminarse.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, Eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: false,
        allowEscapeKey: true
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarTipoCafe();
        }
    });
}

function eliminarTipoCafe() {
    if (tipoIdEliminar) {
        document.getElementById('idEliminar').value = tipoIdEliminar;
        document.getElementById('formEliminar').submit();
    }
}

<?php if ($has_processing): ?>
function filtrarInventario() {
    const procesamiento = document.getElementById('filtro-procesamiento').value.toLowerCase();
    const nombre = document.getElementById('buscar-nombre').value.toLowerCase();
    const filas = document.querySelectorAll('.fila-inventario');
    
    filas.forEach(fila => {
        const filaProc = fila.getAttribute('data-procesamiento').toLowerCase();
        const filaNombre = fila.getAttribute('data-nombre');
        
        const matchProc = !procesamiento || filaProc === procesamiento;
        const matchNombre = !nombre || filaNombre.includes(nombre);
        
        if (matchProc && matchNombre) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}
<?php endif; ?>
</script>

</body>
</html>
