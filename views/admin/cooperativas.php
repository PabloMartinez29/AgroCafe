<?php
require_once 'config/database.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
            case 'update':
                // Validar campos obligatorios solo para create y update
                if (empty(trim($_POST['nombre'] ?? '')) || empty(trim($_POST['nit'] ?? '')) || 
                    empty(trim($_POST['telefono'] ?? '')) || empty(trim($_POST['email'] ?? '')) ||
                    empty(trim($_POST['direccion'] ?? '')) || empty(trim($_POST['representante_legal'] ?? ''))) {
                    $error = "Todos los campos son obligatorios";
                } else {
                    if ($_POST['action'] == 'create') {
                        // Verificar si el NIT ya existe
                        $checkNit = fetchOne("SELECT id FROM cooperativas WHERE nit = ? AND activo = 1", [trim($_POST['nit'] ?? '')]);
                        if ($checkNit) {
                            $error = "El NIT ya está registrado";
                        } else {
                            $data = [
                                'nombre' => trim($_POST['nombre'] ?? ''),
                                'nit' => trim($_POST['nit'] ?? ''),
                                'telefono' => trim($_POST['telefono'] ?? ''),
                                'email' => trim($_POST['email'] ?? ''),
                                'direccion' => trim($_POST['direccion'] ?? ''),
                                'representante_legal' => trim($_POST['representante_legal'] ?? '')
                            ];
                            
                            if (insertRecord('cooperativas', $data)) {
                                $success = "Cooperativa registrada exitosamente";
                            } else {
                                $error = "Error al registrar la cooperativa";
                            }
                        }
                    } else { // update
                        $id = intval($_POST['id']);
                        
                        // Verificar si el NIT ya existe en otra cooperativa
                        $checkNit = fetchOne("SELECT id FROM cooperativas WHERE nit = ? AND id != ? AND activo = 1", [trim($_POST['nit'] ?? ''), $id]);
                        if ($checkNit) {
                            $error = "El NIT ya está registrado en otra cooperativa";
                        } else {
                            $data = [
                                'nombre' => trim($_POST['nombre'] ?? ''),
                                'nit' => trim($_POST['nit'] ?? ''),
                                'telefono' => trim($_POST['telefono'] ?? ''),
                                'email' => trim($_POST['email'] ?? ''),
                                'direccion' => trim($_POST['direccion'] ?? ''),
                                'representante_legal' => trim($_POST['representante_legal'] ?? '')
                            ];
                            
                            if (updateRecord('cooperativas', $data, 'id = ?', [$id])) {
                                $success = "Cooperativa actualizada exitosamente";
                            } else {
                                $error = "Error al actualizar la cooperativa";
                            }
                        }
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id'] ?? 0);
                
                if ($id > 0) {
                    // Eliminar directamente sin validaciones de dependencias
                    $sql = "DELETE FROM cooperativas WHERE id = ?";
                    if (executeQuery($sql, [$id])) {
                        $success = "Cooperativa eliminada exitosamente";
                    } else {
                        $error = "Error al eliminar la cooperativa";
                    }
                } else {
                    $error = "ID de cooperativa inválido";
                }
                break;
        }
    }
}

// Obtener cooperativas
$cooperativas = fetchAll("SELECT * FROM cooperativas ORDER BY nombre");

// Obtener cooperativa específica para editar
$editCooperativa = null;
if (isset($_GET['edit'])) {
    $editCooperativa = fetchOne("SELECT * FROM cooperativas WHERE id = ? AND activo = 1", [$_GET['edit']]);
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

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group input:focus {
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
    <h3>Cooperativas</h3>
    <button class="btn" onclick="showCooperativeForm()">
        <i class="fas fa-plus"></i> Nueva Cooperativa
    </button>
</div>

<div id="cooperative-form" style="display: <?php echo $editCooperativa ? 'block' : 'none'; ?>; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
    <h4 style="margin-bottom: 1rem;">
        <?php echo $editCooperativa ? 'Editar' : 'Registrar Nueva'; ?> Cooperativa
    </h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editCooperativa ? 'update' : 'create'; ?>">
        <?php if ($editCooperativa): ?>
            <input type="hidden" name="id" value="<?php echo $editCooperativa['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required 
                       value="<?php echo $editCooperativa ? htmlspecialchars($editCooperativa['nombre']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>NIT:</label>
                <input type="text" name="nit" required 
                       value="<?php echo $editCooperativa ? htmlspecialchars($editCooperativa['nit']) : ''; ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="tel" name="telefono" required 
                       value="<?php echo $editCooperativa ? htmlspecialchars($editCooperativa['telefono']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required 
                       value="<?php echo $editCooperativa ? htmlspecialchars($editCooperativa['email']) : ''; ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Dirección:</label>
            <input type="text" name="direccion" required 
                   value="<?php echo $editCooperativa ? htmlspecialchars($editCooperativa['direccion']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Representante Legal:</label>
            <input type="text" name="representante_legal" required 
                   value="<?php echo $editCooperativa ? htmlspecialchars($editCooperativa['representante_legal']) : ''; ?>">
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> <?php echo $editCooperativa ? 'Actualizar' : 'Guardar'; ?>
        </button>
        <button type="button" class="btn btn-danger" onclick="hideCooperativeForm()">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>NIT</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Representante</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($cooperativas): ?>
            <?php foreach ($cooperativas as $cooperativa): ?>
                <tr>
                    <td>CO<?php echo str_pad($cooperativa['id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($cooperativa['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cooperativa['nit']); ?></td>
                    <td><?php echo htmlspecialchars($cooperativa['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($cooperativa['email']); ?></td>
                    <td><?php echo htmlspecialchars($cooperativa['representante_legal']); ?></td>
                    <td>
                        <a href="?view=cooperativas&edit=<?php echo $cooperativa['id']; ?>" class="btn" style="padding: 0.5rem;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta cooperativa?\n\nEsta acción no se puede deshacer.')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $cooperativa['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem;" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                    No hay cooperativas registradas
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function showCooperativeForm() {
    document.getElementById('cooperative-form').style.display = 'block';
}

function hideCooperativeForm() {
    document.getElementById('cooperative-form').style.display = 'none';
    if (window.location.href.includes('edit=')) {
        window.location.href = '?view=cooperativas';
    }
}
</script>

