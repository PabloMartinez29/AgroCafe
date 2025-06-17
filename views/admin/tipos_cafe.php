<?php
require_once 'config/database.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'nombre' => trim($_POST['nombre']),
                    'variedad' => $_POST['variedad'],
                    'descripcion' => trim($_POST['descripcion']),
                    'precio_base' => floatval($_POST['precio_base']),
                    'calidad' => $_POST['calidad'],
                    'tipo_procesamiento' => $_POST['tipo_procesamiento']
                ];
                
                if (insertRecord('tipos_cafe', $data)) {
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'title' => '¡Tipo de Café Creado!',
                        'message' => 'El nuevo tipo de café ha sido registrado exitosamente.'
                    ];
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'title' => 'Error al Crear',
                        'message' => 'No se pudo registrar el tipo de café. Inténtalo nuevamente.'
                    ];
                }
                header('Location: ?view=tipos-cafe');
                exit;
                
            case 'update':
                $id = intval($_POST['id']);
                $data = [
                    'nombre' => trim($_POST['nombre']),
                    'variedad' => $_POST['variedad'],
                    'descripcion' => trim($_POST['descripcion']),
                    'precio_base' => floatval($_POST['precio_base']),
                    'calidad' => $_POST['calidad'],
                    'tipo_procesamiento' => $_POST['tipo_procesamiento']
                ];
                
                if (updateRecord('tipos_cafe', $data, 'id = ?', [$id])) {
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'title' => '¡Tipo de Café Actualizado!',
                        'message' => 'Los cambios han sido guardados exitosamente.'
                    ];
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'title' => 'Error al Actualizar',
                        'message' => 'No se pudieron guardar los cambios. Inténtalo nuevamente.'
                    ];
                }
                header('Location: ?view=tipos-cafe');
                exit;

            case 'delete':
                $id = intval($_POST['id']);
                
                try {
                    // Obtener información del tipo de café antes de eliminarlo
                    $cafeInfo = fetchOne("SELECT nombre FROM tipos_cafe WHERE id = ?", [$id]);
                    $nombreCafe = $cafeInfo ? $cafeInfo['nombre'] : 'Desconocido';
                    
                    // Verificar si tiene referencias en otras tablas
                    $referencias = [];
                    
                    // Verificar compras
                    $compras = fetchOne("SELECT COUNT(*) as total FROM compras WHERE tipo_cafe_id = ?", [$id]);
                    if ($compras['total'] > 0) {
                        $referencias[] = "compras ({$compras['total']})";
                    }
                    
                    // Verificar ventas
                    $ventas = fetchOne("SELECT COUNT(*) as total FROM ventas WHERE tipo_cafe_id = ?", [$id]);
                    if ($ventas['total'] > 0) {
                        $referencias[] = "ventas ({$ventas['total']})";
                    }
                    
                    // Verificar movimientos de inventario
                    $movimientos = fetchOne("SELECT COUNT(*) as total FROM inventario_movimientos WHERE tipo_cafe_id = ?", [$id]);
                    if ($movimientos['total'] > 0) {
                        $referencias[] = "movimientos de inventario ({$movimientos['total']})";
                    }
                    
                    // Verificar precios históricos (si existe la tabla)
                    $preciosHistoricos = fetchOne("SELECT COUNT(*) as total FROM precios_historicos WHERE tipo_cafe_id = ? LIMIT 1", [$id]);
                    if ($preciosHistoricos && $preciosHistoricos['total'] > 0) {
                        $referencias[] = "precios históricos ({$preciosHistoricos['total']})";
                    }
                    
                    if (count($referencias) > 0) {
                        // Si tiene referencias, desactivar en lugar de eliminar
                        $result = executeQuery("UPDATE tipos_cafe SET activo = 0 WHERE id = ?", [$id]);
                        
                        if ($result) {
                            $referenciasList = implode(', ', $referencias);
                            $_SESSION['alert'] = [
                                'type' => 'warning',
                                'title' => 'Tipo de Café Desactivado',
                                'message' => "El tipo de café '<strong>$nombreCafe</strong>' ha sido desactivado porque tiene registros asociados en: <strong>$referenciasList</strong>.<br><br>El registro ya no aparecerá en nuevas operaciones, pero se mantiene el historial."
                            ];
                        } else {
                            throw new Exception("No se pudo desactivar el tipo de café");
                        }
                    } else {
                        // Si no tiene referencias, eliminar completamente
                        $result = executeQuery("DELETE FROM tipos_cafe WHERE id = ?", [$id]);
                        
                        if ($result) {
                            $_SESSION['alert'] = [
                                'type' => 'success',
                                'title' => '¡Eliminación Exitosa!',
                                'message' => "El tipo de café '<strong>$nombreCafe</strong>' ha sido eliminado completamente del sistema."
                            ];
                        } else {
                            throw new Exception("No se pudo eliminar el tipo de café");
                        }
                    }
                    
                } catch (Exception $e) {
                    error_log("Error al eliminar tipo_cafe_id $id: " . $e->getMessage());
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'title' => 'Error en la Eliminación',
                        'message' => "No se pudo procesar la eliminación: " . $e->getMessage()
                    ];
                }
                
                header('Location: ?view=tipos-cafe');
                exit;
        }
    }
}

// Obtener tipos de café ordenados por tipo de procesamiento (solo activos por defecto)
$mostrarInactivos = isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1';
$whereClause = $mostrarInactivos ? "" : "WHERE activo = 1";
$tiposCafe = fetchAll("SELECT * FROM tipos_cafe $whereClause ORDER BY activo DESC, tipo_procesamiento, nombre");

// Obtener tipo específico para editar
$editTipo = null;
if (isset($_GET['edit'])) {
    $editTipo = fetchOne("SELECT * FROM tipos_cafe WHERE id = ?", [$_GET['edit']]);
}

// Contar tipos activos e inactivos
$conteoActivos = fetchOne("SELECT COUNT(*) as total FROM tipos_cafe WHERE activo = 1");
$conteoInactivos = fetchOne("SELECT COUNT(*) as total FROM tipos_cafe WHERE activo = 0");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Café</title>
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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

        .table tbody tr.inactive {
            background: #f8f9fa;
            opacity: 0.7;
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
            transition: border-color 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .quality-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .quality-premium {
            background: #ffd700;
            color: #8B4513;
        }

        .quality-especial {
            background: #e8f5e8;
            color: #155724;
        }

        .quality-comercial {
            background: #e2e3e5;
            color: #495057;
        }

        .variety-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .variety-arabica {
            background: #8B4513;
            color: white;
        }

        .variety-robusta {
            background: #A0522D;
            color: white;
        }

        .processing-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
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

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .form-container {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px solid #ddd;
        }

        .stats-bar {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .table {
                font-size: 0.9rem;
            }
            
            .table th, .table td {
                padding: 0.5rem;
            }

            .stats-bar {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<!-- Estadísticas -->
<div class="stats-bar">
    <div>
        <strong><i class="fas fa-coffee"></i> Tipos de Café:</strong>
        <span style="color: #28a745;">Activos: <?php echo $conteoActivos['total']; ?></span>
        <?php if ($conteoInactivos['total'] > 0): ?>
            | <span style="color: #dc3545;">Inactivos: <?php echo $conteoInactivos['total']; ?></span>
        <?php endif; ?>
    </div>
    <div>
        <?php if ($conteoInactivos['total'] > 0): ?>
            <a href="?view=tipos-cafe&show_inactive=<?php echo $mostrarInactivos ? '0' : '1'; ?>" class="btn btn-info">
                <i class="fas fa-eye<?php echo $mostrarInactivos ? '-slash' : ''; ?>"></i>
                <?php echo $mostrarInactivos ? 'Ocultar Inactivos' : 'Mostrar Inactivos'; ?>
            </a>
        <?php endif; ?>
        <button class="btn" onclick="showCoffeeForm()">
            <i class="fas fa-plus"></i> Nuevo Tipo
        </button>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h3><i class="fas fa-coffee"></i> Gestión de Tipos de Café</h3>
</div>

<div id="coffee-form" class="form-container" style="display: <?php echo $editTipo ? 'block' : 'none'; ?>;">
    <h4 style="margin-bottom: 1.5rem; color: #8B4513;">
        <i class="fas fa-<?php echo $editTipo ? 'edit' : 'plus'; ?>"></i>
        <?php echo $editTipo ? 'Editar' : 'Registrar Nuevo'; ?> Tipo de Café
    </h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editTipo ? 'update' : 'create'; ?>">
        <?php if ($editTipo): ?>
            <input type="hidden" name="id" value="<?php echo $editTipo['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-coffee"></i> Nombre del Café:</label>
                <input type="text" name="nombre" required 
                       value="<?php echo $editTipo ? htmlspecialchars($editTipo['nombre']) : ''; ?>"
                       placeholder="Ej: Café Supremo, Café Especial, Pasilla Premium...">
            </div>
            <div class="form-group">
                <label><i class="fas fa-cogs"></i> Tipo de Procesamiento:</label>
                <select name="tipo_procesamiento" required>
                    <option value="">Seleccionar procesamiento</option>
                    <option value="normal" <?php echo ($editTipo && $editTipo['tipo_procesamiento'] == 'normal') ? 'selected' : ''; ?>>Normal</option>
                    <option value="mojado" <?php echo ($editTipo && $editTipo['tipo_procesamiento'] == 'mojado') ? 'selected' : ''; ?>>Mojado</option>
                    <option value="seco" <?php echo ($editTipo && $editTipo['tipo_procesamiento'] == 'seco') ? 'selected' : ''; ?>>Seco</option>
                    <option value="pasilla" <?php echo ($editTipo && $editTipo['tipo_procesamiento'] == 'pasilla') ? 'selected' : ''; ?>>Pasilla</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-leaf"></i> Variedad:</label>
                <select name="variedad" required>
                    <option value="">Seleccionar variedad</option>
                    <option value="arabica" <?php echo ($editTipo && $editTipo['variedad'] == 'arabica') ? 'selected' : ''; ?>>Arábica</option>
                    <option value="robusta" <?php echo ($editTipo && $editTipo['variedad'] == 'robusta') ? 'selected' : ''; ?>>Robusta</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-star"></i> Calidad:</label>
                <select name="calidad" required>
                    <option value="">Seleccionar calidad</option>
                    <option value="premium" <?php echo ($editTipo && $editTipo['calidad'] == 'premium') ? 'selected' : ''; ?>>Premium</option>
                    <option value="especial" <?php echo ($editTipo && $editTipo['calidad'] == 'especial') ? 'selected' : ''; ?>>Especial</option>
                    <option value="comercial" <?php echo ($editTipo && $editTipo['calidad'] == 'comercial') ? 'selected' : ''; ?>>Comercial</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-align-left"></i> Descripción:</label>
            <textarea name="descripcion" rows="3" 
                      placeholder="Describe las características del café, su origen, sabor, aroma, tipo de procesamiento, etc."><?php echo $editTipo ? htmlspecialchars($editTipo['descripcion']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-dollar-sign"></i> Precio Base (por kg):</label>
            <input type="number" name="precio_base" step="0.01" required 
                   value="<?php echo $editTipo ? $editTipo['precio_base'] : ''; ?>"
                   placeholder="Ej: 12000.00">
        </div>
        
        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $editTipo ? 'Actualizar' : 'Guardar'; ?>
            </button>
            <button type="button" class="btn btn-danger" onclick="hideCoffeeForm()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>

<div style="background: white; border-radius: 10px; overflow: hidden;">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Procesamiento</th>
                <th>Variedad</th>
                <th>Descripción</th>
                <th>Precio Base</th>
                <th>Calidad</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tiposCafe): ?>
                <?php foreach ($tiposCafe as $tipo): ?>
                    <tr class="<?php echo $tipo['activo'] == 0 ? 'inactive' : ''; ?>">
                        <td><strong>TC<?php echo str_pad($tipo['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo htmlspecialchars($tipo['nombre']); ?></td>
                        <td>
                            <span class="processing-badge processing-<?php echo $tipo['tipo_procesamiento'] ?? 'normal'; ?>">
                                <?php echo ucfirst($tipo['tipo_procesamiento'] ?? 'Normal'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="variety-badge variety-<?php echo $tipo['variedad']; ?>">
                                <?php echo ucfirst($tipo['variedad']); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $descripcion = htmlspecialchars($tipo['descripcion']);
                            echo strlen($descripcion) > 40 ? substr($descripcion, 0, 40) . '...' : $descripcion; 
                            ?>
                        </td>
                        <td><strong>$<?php echo number_format($tipo['precio_base'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="quality-badge quality-<?php echo $tipo['calidad']; ?>">
                                <?php echo ucfirst($tipo['calidad']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $tipo['activo'] == 1 ? 'active' : 'inactive'; ?>">
                                <?php echo $tipo['activo'] == 1 ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($tipo['fecha_creacion'])); ?></td>
                        <td>
                            <a href="?view=tipos-cafe&edit=<?php echo $tipo['id']; ?>" 
                               class="btn" style="padding: 0.5rem;" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($tipo['activo'] == 1): ?>
                                <button onclick="confirmarEliminacion(<?php echo $tipo['id']; ?>, '<?php echo htmlspecialchars($tipo['nombre'], ENT_QUOTES); ?>')"
                                        class="btn btn-danger" style="padding: 0.5rem;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="reactivarTipo(<?php echo $tipo['id']; ?>, '<?php echo htmlspecialchars($tipo['nombre'], ENT_QUOTES); ?>')"
                                        class="btn btn-success" style="padding: 0.5rem;" title="Reactivar">
                                    <i class="fas fa-undo"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 3rem; color: #666;">
                        <i class="fas fa-coffee" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <br>No hay tipos de café registrados
                        <br><small>Haz clic en "Nuevo Tipo" para agregar el primer tipo de café</small>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Formularios ocultos -->
<form id="formEliminar" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="idEliminar">
</form>

<form id="formReactivar" method="POST" style="display: none;">
    <input type="hidden" name="action" value="reactivate">
    <input type="hidden" name="id" id="idReactivar">
</form>

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Mostrar alertas de sesión
<?php if (isset($_SESSION['alert'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const alert = <?php echo json_encode($_SESSION['alert']); ?>;
    
    Swal.fire({
        icon: alert.type,
        title: alert.title,
        html: alert.message,
        confirmButtonText: 'Entendido',
        confirmButtonColor: alert.type === 'success' ? '#28a745' : (alert.type === 'warning' ? '#ffc107' : '#dc3545'),
        timer: alert.type === 'success' ? 4000 : 6000,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
});
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>

function showCoffeeForm() {
    document.getElementById('coffee-form').style.display = 'block';
    document.getElementById('coffee-form').scrollIntoView({ behavior: 'smooth' });
}

function hideCoffeeForm() {
    document.getElementById('coffee-form').style.display = 'none';
    if (window.location.href.includes('edit=')) {
        window.location.href = '?view=tipos-cafe';
    }
}

function confirmarEliminacion(id, nombre) {
    Swal.fire({
        title: '¿Confirmar Eliminación?',
        html: `¿Estás seguro de que deseas eliminar el tipo de café <strong>"${nombre}"</strong>?<br><br><small><i class="fas fa-info-circle"></i> Si tiene transacciones asociadas, se desactivará automáticamente en lugar de eliminarse.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, Eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('idEliminar').value = id;
            document.getElementById('formEliminar').submit();
        }
    });
}

function reactivarTipo(id, nombre) {
    Swal.fire({
        title: '¿Reactivar Tipo de Café?',
        html: `¿Deseas reactivar el tipo de café <strong>"${nombre}"</strong>?<br><br><small>Volverá a estar disponible para nuevas operaciones.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-undo"></i> Sí, Reactivar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Ejecutar reactivación via AJAX o formulario
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="activo" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

</body>
</html>
