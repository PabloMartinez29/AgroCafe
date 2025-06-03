<?php
require_once 'config/database.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Validar campos obligatorios
                if (empty(trim($_POST['nombre'])) || empty(trim($_POST['email'])) || 
                    empty(trim($_POST['telefono'])) || empty(trim($_POST['direccion'])) ||
                    empty(trim($_POST['password']))) {
                    $error = "Todos los campos son obligatorios";
                } else {
                    // Verificar si el email ya existe
                    $checkEmail = fetchOne("SELECT id FROM usuarios WHERE email = ?", [trim($_POST['email'])]);
                    if ($checkEmail) {
                        $error = "El correo electrónico ya está registrado";
                    } else {
                        $data = [
                            'nombre' => trim($_POST['nombre']),
                            'email' => trim($_POST['email']),
                            'telefono' => trim($_POST['telefono']),
                            'direccion' => trim($_POST['direccion']),
                            'password' => trim($_POST['password']),
                            'rol' => $_POST['rol']
                        ];
                        
                        if (insertRecord('usuarios', $data)) {
                            $success = "Usuario registrado exitosamente";
                            // Limpiar formulario
                            unset($_POST);
                        } else {
                            $error = "Error al registrar el usuario";
                        }
                    }
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                
                // Verificar si el email ya existe en otro usuario
                $checkEmail = fetchOne("SELECT id FROM usuarios WHERE email = ? AND id != ?", [trim($_POST['email']), $id]);
                if ($checkEmail) {
                    $error = "El correo electrónico ya está registrado en otro usuario";
                } else {
                    $data = [
                        'nombre' => trim($_POST['nombre']),
                        'email' => trim($_POST['email']),
                        'telefono' => trim($_POST['telefono']),
                        'direccion' => trim($_POST['direccion']),
                        'rol' => $_POST['rol']
                    ];
                    
                    // Solo actualizar password si se proporciona
                    if (!empty(trim($_POST['password']))) {
                        $data['password'] = trim($_POST['password']);
                    }
                    
                    if (updateRecord('usuarios', $data, 'id = ?', [$id])) {
                        $success = "Usuario actualizado exitosamente";
                    } else {
                        $error = "Error al actualizar el usuario";
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                
                // No permitir eliminar el usuario actual
                if ($id == $_SESSION['user_id']) {
                    $error = "No puedes eliminar tu propio usuario";
                } else {
                    // Eliminar directamente sin validaciones de dependencias
                    $sql = "DELETE FROM usuarios WHERE id = ?";
                    if (executeQuery($sql, [$id])) {
                        $success = "Usuario eliminado exitosamente";
                    } else {
                        $error = "Error al eliminar el usuario";
                    }
                }
                break;
        }
    }
}

// Obtener usuarios
$usuarios = fetchAll("SELECT * FROM usuarios ORDER BY fecha_registro DESC");

// Obtener usuario específico para editar
$editUsuario = null;
if (isset($_GET['edit'])) {
    $editUsuario = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$_GET['edit']]);
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

    .form-group input, .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #8B4513;
        box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
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

    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .role-administrador {
        background: #dc3545;
        color: white;
    }

    .role-campesino {
        background: #28a745;
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
        .form-row {
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
    <h3><i class="fas fa-users"></i> Gestión de Usuarios</h3>
    <button class="btn" onclick="showUserForm()">
        <i class="fas fa-plus"></i> Nuevo Usuario
    </button>
</div>

<div id="user-form" class="form-container" style="display: <?php echo $editUsuario ? 'block' : 'none'; ?>;">
    <h4 style="margin-bottom: 1.5rem; color: #8B4513;">
        <i class="fas fa-<?php echo $editUsuario ? 'edit' : 'user-plus'; ?>"></i>
        <?php echo $editUsuario ? 'Editar' : 'Registrar Nuevo'; ?> Usuario
    </h4>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editUsuario ? 'update' : 'create'; ?>">
        <?php if ($editUsuario): ?>
            <input type="hidden" name="id" value="<?php echo $editUsuario['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nombre Completo:</label>
                <input type="text" name="nombre" required 
                       value="<?php echo $editUsuario ? htmlspecialchars($editUsuario['nombre']) : ''; ?>"
                       placeholder="Ingrese el nombre completo">
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" name="email" required 
                       value="<?php echo $editUsuario ? htmlspecialchars($editUsuario['email']) : ''; ?>"
                       placeholder="ejemplo@correo.com">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Teléfono:</label>
                <input type="tel" name="telefono" required 
                       value="<?php echo $editUsuario ? htmlspecialchars($editUsuario['telefono']) : ''; ?>"
                       placeholder="+57 300 123 4567">
            </div>
            <div class="form-group">
                <label><i class="fas fa-user-tag"></i> Rol:</label>
                <select name="rol" required>
                    <option value="">Seleccionar rol</option>
                    <option value="administrador" <?php echo ($editUsuario && $editUsuario['rol'] == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="campesino" <?php echo ($editUsuario && $editUsuario['rol'] == 'campesino') ? 'selected' : ''; ?>>Campesino</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Dirección:</label>
            <input type="text" name="direccion" required 
                   value="<?php echo $editUsuario ? htmlspecialchars($editUsuario['direccion']) : ''; ?>"
                   placeholder="Dirección completa">
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> Contraseña <?php echo $editUsuario ? '(dejar vacío para mantener actual)' : ''; ?>:</label>
            <input type="password" name="password" <?php echo $editUsuario ? '' : 'required'; ?> 
                   minlength="6" placeholder="Mínimo 6 caracteres">
        </div>
        
        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $editUsuario ? 'Actualizar' : 'Guardar'; ?>
            </button>
            <button type="button" class="btn btn-danger" onclick="hideUserForm()">
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
                <th>Email</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usuarios): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><strong>U<?php echo str_pad($usuario['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $usuario['rol']; ?>">
                                <?php echo ucfirst($usuario['rol']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                        <td>
                            <?php if ($usuario['activo']): ?>
                                <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Activo</span>
                            <?php else: ?>
                                <span style="color: #dc3545;"><i class="fas fa-times-circle"></i> Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?view=usuarios&edit=<?php echo $usuario['id']; ?>" 
                               class="btn" style="padding: 0.5rem;" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" style="display: inline;" 
                                      onsubmit="return confirm('¿Está seguro de eliminar este usuario?\n\nEsta acción no se puede deshacer.')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            style="padding: 0.5rem;" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #666; font-size: 0.8rem;">Usuario actual</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: #666;">
                        <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <br>No hay usuarios registrados
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function showUserForm() {
    document.getElementById('user-form').style.display = 'block';
    document.getElementById('user-form').scrollIntoView({ behavior: 'smooth' });
}

function hideUserForm() {
    document.getElementById('user-form').style.display = 'none';
    if (window.location.href.includes('edit=')) {
        window.location.href = '?view=usuarios';
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

