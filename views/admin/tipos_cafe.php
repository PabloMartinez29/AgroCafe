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
                    'tipo_procesamiento' => $_POST['tipo_procesamiento'] // NUEVO CAMPO
                ];
                
                if (insertRecord('tipos_cafe', $data)) {
                    $success = "Tipo de café registrado exitosamente";
                } else {
                    $error = "Error al registrar el tipo de café";
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $data = [
                    'nombre' => trim($_POST['nombre']),
                    'variedad' => $_POST['variedad'],
                    'descripcion' => trim($_POST['descripcion']),
                    'precio_base' => floatval($_POST['precio_base']),
                    'calidad' => $_POST['calidad'],
                    'tipo_procesamiento' => $_POST['tipo_procesamiento'] // NUEVO CAMPO
                ];
                
                if (updateRecord('tipos_cafe', $data, 'id = ?', [$id])) {
                    $success = "Tipo de café actualizado exitosamente";
                } else {
                    $error = "Error al actualizar el tipo de café";
                }
                break;

            case 'delete':
                $id = intval($_POST['id']);
                
                $sql = "DELETE FROM tipos_cafe WHERE id = ?";
                if (executeQuery($sql, [$id])) {
                    $success = "Tipo de café eliminado exitosamente";
                } else {
                    $error = "Error al eliminar el tipo de café";
                }
                break;
        }
    }
}

// Obtener tipos de café ordenados por tipo de procesamiento
$tiposCafe = fetchAll("SELECT * FROM tipos_cafe ORDER BY tipo_procesamiento, nombre");

// Obtener tipo específico para editar
$editTipo = null;
if (isset($_GET['edit'])) {
    $editTipo = fetchOne("SELECT * FROM tipos_cafe WHERE id = ?", [$_GET['edit']]);
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

    .form-row-three {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
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

    /* NUEVOS ESTILOS PARA TIPO DE PROCESAMIENTO */
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

    .form-container {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border: 1px solid #ddd;
    }

    @media (max-width: 768px) {
        .form-row, .form-row-three {
            grid-template-columns: 1fr;
        }
        
        .table {
            font-size: 0.9rem;
        }
        
        .table th, .table td {
            padding: 0.5rem;
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
    <h3><i class="fas fa-coffee"></i> Tipos de Café</h3>
    <button class="btn" onclick="showCoffeeForm()">
        <i class="fas fa-plus"></i> Nuevo Tipo
    </button>
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
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tiposCafe): ?>
                <?php foreach ($tiposCafe as $tipo): ?>
                    <tr>
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
                        <td><?php echo date('d/m/Y', strtotime($tipo['fecha_creacion'])); ?></td>
                        <td>
                            <a href="?view=tipos-cafe&edit=<?php echo $tipo['id']; ?>" 
                               class="btn" style="padding: 0.5rem;" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('¿Está seguro de eliminar este tipo de café?\n\nEsta acción no se puede deshacer y puede afectar las transacciones existentes.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $tipo['id']; ?>">
                                <button type="submit" class="btn btn-danger" 
                                        style="padding: 0.5rem;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 3rem; color: #666;">
                        <i class="fas fa-coffee" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <br>No hay tipos de café registrados
                        <br><small>Haz clic en "Nuevo Tipo" para agregar el primer tipo de café</small>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
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

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>