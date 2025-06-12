<?php
// Variables de estado
$cajaAbierta = fetchOne("SELECT * FROM cajas WHERE estado = 'abierto' LIMIT 1");
$saldoActual = $cajaAbierta ? $cajaAbierta['saldo_disponible'] : 0;
$salarioBase = $cajaAbierta ? $cajaAbierta['salario_base'] : 0;

// FUNCIÓN MEJORADA: Calcular horas de operación
function calcularHorasOperacion($fechaApertura, $fechaCierre = null, $estado = 'cerrado') {
    $inicioTimestamp = strtotime($fechaApertura);
    
    if ($estado == 'abierto' || !$fechaCierre) {
        // Si está abierta, calcular desde apertura hasta ahora
        $finTimestamp = time();
    } else {
        // Si está cerrada, usar fecha de cierre
        $finTimestamp = strtotime($fechaCierre);
    }
    
    $diferenciaSegundos = $finTimestamp - $inicioTimestamp;
    $horas = floor($diferenciaSegundos / 3600);
    $minutos = floor(($diferenciaSegundos % 3600) / 60);
    
    return [
        'horas' => $horas,
        'minutos' => $minutos,
        'total_horas' => round($diferenciaSegundos / 3600, 2),
        'texto' => $horas . 'h ' . $minutos . 'm'
    ];
}

// Procesar formulario
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'abrir_caja':
            $nombreCaja = trim($_POST['nombre_caja']);
            $cantidadBase = floatval($_POST['cantidad_base']) ?: 0;
            $fechaApertura = $_POST['fecha_apertura'] . ' ' . $_POST['hora_apertura'] . ':00';
            $salarioBase = floatval($_POST['salario_base']);

            // CORRECCIÓN: Inicializar todos los campos con valores por defecto
            $sql = "INSERT INTO cajas (nombre_caja, cantidad_inicial, salario_base, saldo_disponible, fecha_apertura, kilos_comprados, kilos_vendidos, horas_operacion, estado) 
                    VALUES (?, ?, ?, ?, ?, 0, 0, 0, 'abierto')";
            if (executeQuery($sql, [$nombreCaja, $cantidadBase, $salarioBase, $salarioBase, $fechaApertura])) {
                $success = "Caja '$nombreCaja' abierta exitosamente.";
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=caja_abierta");
                exit;
            } else {
                $error = "Error al abrir la caja: " . (new PDOException())->getMessage();
            }
            break;

        case 'cerrar_caja':
            if ($cajaAbierta) {
                // CORRECCIÓN MEJORADA: Calcular horas exactas al cerrar
                $tiempoOperacion = calcularHorasOperacion($cajaAbierta['fecha_apertura'], null, 'abierto');
                $horasOperacion = $tiempoOperacion['total_horas'];
                
                $sql = "UPDATE cajas SET 
                        estado = 'cerrado', 
                        fecha_cierre = CURRENT_TIMESTAMP, 
                        horas_operacion = ?,
                        saldo_disponible = COALESCE(saldo_disponible, 0),
                        kilos_comprados = COALESCE(kilos_comprados, 0),
                        kilos_vendidos = COALESCE(kilos_vendidos, 0)
                        WHERE id = ?";
                        
                if (executeQuery($sql, [$horasOperacion, $cajaAbierta['id']])) {
                    $success = "Caja '" . $cajaAbierta['nombre_caja'] . "' cerrada exitosamente. Tiempo operativo: " . $tiempoOperacion['texto'];
                    header("Location: " . $_SERVER['PHP_SELF'] . "?success=caja_cerrada");
                    exit;
                } else {
                    $error = "Error al cerrar la caja.";
                }
            } else {
                $error = "No hay una caja abierta para cerrar.";
            }
            break;
    }
}

// Manejar mensajes de éxito desde URL
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'caja_abierta':
            $success = "Caja abierta exitosamente.";
            break;
        case 'caja_cerrada':
            $success = "Caja cerrada exitosamente.";
            break;
    }
}

// CORRECCIÓN: Obtener historial con cálculo de horas mejorado
$historicoCajas = fetchAll("SELECT 
    id,
    nombre_caja,
    COALESCE(cantidad_inicial, 0) as cantidad_inicial,
    COALESCE(salario_base, 0) as salario_base,
    COALESCE(saldo_disponible, 0) as saldo_disponible,
    COALESCE(kilos_comprados, 0) as kilos_comprados,
    COALESCE(kilos_vendidos, 0) as kilos_vendidos,
    COALESCE(horas_operacion, 0) as horas_operacion,
    fecha_apertura,
    fecha_cierre,
    estado
    FROM cajas 
    ORDER BY fecha_apertura DESC");

// Actualizar caja abierta después de posibles cambios
$cajaAbierta = fetchOne("SELECT * FROM cajas WHERE estado = 'abierto' LIMIT 1");
?>

<div class="container-fluid mt-4">
    <h3 class="text-center"><i class="fas fa-cash-register"></i> Gestión de Caja</h3>

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
                    <?php if (isset($_GET['compra_success'])): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i> Compra registrada exitosamente. Saldo actualizado.
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['compra_error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> Error al registrar la compra. Saldo insuficiente o caja cerrada.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script mejorado para mostrar modal y limpiar URL -->
    <?php if ($success || $error || isset($_GET['compra_success']) || isset($_GET['compra_error']) || isset($_GET['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('notificationModal')).show();
                
                if (window.location.search) {
                    const url = new URL(window.location);
                    url.search = '';
                    window.history.replaceState({}, document.title, url.toString());
                }
            });
        </script>
    <?php endif; ?>

    <!-- Botón para abrir caja si no hay ninguna abierta -->
    <?php if (!$cajaAbierta): ?>
        <div class="text-center mb-4">
            <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#openCajaModal">
                <i class="fas fa-plus-circle"></i> Abrir Caja
            </button>
        </div>
    <?php endif; ?>

    <!-- Modal para abrir caja -->
    <div class="modal fade" id="openCajaModal" tabindex="-1" aria-labelledby="openCajaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="openCajaModalLabel">Abrir Nueva Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="openCajaForm">
                        <input type="hidden" name="action" value="abrir_caja">
                        <div class="mb-3">
                            <label for="nombre_caja" class="form-label">Nombre de la Caja</label>
                            <input type="text" class="form-control" id="nombre_caja" name="nombre_caja" required>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad_base" class="form-label">Cantidad Base (kg)</label>
                            <input type="number" class="form-control" id="cantidad_base" name="cantidad_base" step="0.01" min="0" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_apertura" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha_apertura" name="fecha_apertura" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="hora_apertura" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora_apertura" name="hora_apertura" value="<?php echo date('H:i'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="salario_base" class="form-label">Monto Inicial ($)</label>
                            <input type="number" class="form-control" id="salario_base" name="salario_base" step="0.01" min="0" value="0" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Abrir Caja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestión de caja abierta -->
    <?php if ($cajaAbierta): ?>
        <?php 
        // CORRECCIÓN: Calcular tiempo actual para caja abierta
        $tiempoActual = calcularHorasOperacion($cajaAbierta['fecha_apertura'], null, 'abierto');
        ?>
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title text-center text-success">
                    <i class="fas fa-cash-register"></i> Caja Actual: <?php echo htmlspecialchars($cajaAbierta['nombre_caja']); ?> 
                    <span class="badge bg-success">Abierta</span>
                </h4>
                <div class="row text-center mt-3">
                    <div class="col-md-3">
                        <p><strong>Fecha de apertura:</strong><br><?php echo date('d/m/Y H:i', strtotime($cajaAbierta['fecha_apertura'])); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Saldo disponible:</strong><br>$<?php echo number_format($cajaAbierta['saldo_disponible'] ?? 0, 0, ',', '.'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Kilos comprados:</strong><br><?php echo number_format($cajaAbierta['kilos_comprados'] ?? 0, 2); ?> kg</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Horas abiertas:</strong><br>
                            <span class="tiempo-activo" data-inicio="<?php echo strtotime($cajaAbierta['fecha_apertura']); ?>">
                                <?php echo $tiempoActual['texto']; ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Botón para cerrar caja -->
                <div class="text-center mt-4">
                    <button class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#closeCajaModal">
                        <i class="fas fa-times-circle"></i> Cerrar Caja
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal mejorado para cerrar caja -->
    <div class="modal fade" id="closeCajaModal" tabindex="-1" aria-labelledby="closeCajaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeCajaModalLabel">Cerrar Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($cajaAbierta): ?>
                        <?php $tiempoModal = calcularHorasOperacion($cajaAbierta['fecha_apertura'], null, 'abierto'); ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>¿Estás seguro de cerrar la caja?</strong>
                        </div>
                        <p><strong>Caja:</strong> <?php echo htmlspecialchars($cajaAbierta['nombre_caja']); ?></p>
                        <p><strong>Tiempo operativo:</strong> <?php echo $tiempoModal['texto']; ?> (<?php echo $tiempoModal['total_horas']; ?> horas)</p>
                        <p><strong>Saldo actual:</strong> $<?php echo number_format($cajaAbierta['saldo_disponible'] ?? 0, 0, ',', '.'); ?></p>
                        
                        <form method="POST" id="closeCajaForm">
                            <input type="hidden" name="action" value="cerrar_caja">
                            <div class="text-center">
                                <button type="submit" class="btn btn-danger" id="btnCerrarCaja">
                                    <i class="fas fa-check"></i> Sí, cerrar caja
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Cajas -->
    <div class="mt-4">
        <h4 class="text-center"><i class="fas fa-history"></i> Historial de Cajas</h4>
        <?php if ($historicoCajas): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover tabla-historial">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fas fa-tag"></i> Nombre</th>
                            <th><i class="fas fa-weight"></i> Kilos</th>
                            <th><i class="fas fa-dollar-sign"></i> Inicial</th>
                            <th><i class="fas fa-dollar-sign"></i> Final</th>
                            <th><i class="fas fa-clock"></i> Tiempo</th>
                            <th><i class="fas fa-calendar"></i> Apertura</th>
                            <th><i class="fas fa-info-circle"></i> Estado</th>
                            <th><i class="fas fa-eye"></i> Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historicoCajas as $caja): ?>
                            <?php 
                            // CORRECCIÓN PRINCIPAL: Calcular horas correctamente para cada caja
                            $tiempoOperacion = calcularHorasOperacion(
                                $caja['fecha_apertura'], 
                                $caja['fecha_cierre'], 
                                $caja['estado']
                            );
                            ?>
                            <tr class="fila-historial">
                                <td class="campo-nombre"><?php echo htmlspecialchars($caja['nombre_caja']); ?></td>
                                <td class="campo-numero"><?php echo number_format($caja['kilos_comprados'], 2); ?> kg</td>
                                <td class="campo-dinero">$<?php echo number_format($caja['salario_base'], 0, ',', '.'); ?></td>
                                <td class="campo-dinero">$<?php echo number_format($caja['saldo_disponible'], 0, ',', '.'); ?></td>
                                <td class="campo-tiempo">
                                    <?php if ($caja['estado'] == 'abierto'): ?>
                                        <span class="tiempo-activo badge bg-info" data-inicio="<?php echo strtotime($caja['fecha_apertura']); ?>">
                                            <?php echo $tiempoOperacion['texto']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="tiempo-cerrado">
                                            <?php echo $tiempoOperacion['texto']; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="campo-fecha"><?php echo date('d/m/Y H:i', strtotime($caja['fecha_apertura'])); ?></td>
                                <td class="campo-estado">
                                    <?php if ($caja['estado'] == 'abierto'): ?>
                                        <span class="badge bg-success estado-abierto">Abierto</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary estado-cerrado">Cerrado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="campo-accion">
                                    <button class="btn btn-warning btn-sm boton-detalles" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $caja['id']; ?>">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay registros de cajas.
            </div>
        <?php endif; ?>
    </div>

    <!-- Modales de detalles mejorados -->
    <?php foreach ($historicoCajas as $caja): ?>
        <?php $tiempoDetalle = calcularHorasOperacion($caja['fecha_apertura'], $caja['fecha_cierre'], $caja['estado']); ?>
        <div class="modal fade" id="detailsModal<?php echo $caja['id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $caja['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel<?php echo $caja['id']; ?>">
                            <i class="fas fa-info-circle"></i> Detalles de la Caja: <?php echo htmlspecialchars($caja['nombre_caja']); ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6><i class="fas fa-info"></i> Información General</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Cantidad Inicial:</strong> <?php echo number_format($caja['cantidad_inicial'], 2); ?> kg</p>
                                        <p><strong>Monto Inicial:</strong> $<?php echo number_format($caja['salario_base'], 0, ',', '.'); ?></p>
                                        <p><strong>Monto Final:</strong> $<?php echo number_format($caja['saldo_disponible'], 0, ',', '.'); ?></p>
                                        <p><strong>Diferencia:</strong> 
                                            <?php 
                                            $diferencia = $caja['saldo_disponible'] - $caja['salario_base'];
                                            $clase = $diferencia >= 0 ? 'text-success' : 'text-danger';
                                            ?>
                                            <span class="<?php echo $clase; ?>">
                                                $<?php echo number_format($diferencia, 0, ',', '.'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6><i class="fas fa-clock"></i> Información Temporal</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Fecha Apertura:</strong> <?php echo date('d/m/Y H:i', strtotime($caja['fecha_apertura'])); ?></p>
                                        <p><strong>Fecha Cierre:</strong> 
                                            <?php if ($caja['fecha_cierre']): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($caja['fecha_cierre'])); ?>
                                            <?php else: ?>
                                                <span class="badge bg-warning">En curso</span>
                                            <?php endif; ?>
                                        </p>
                                        <p><strong>Tiempo Operativo:</strong> 
                                            <span class="badge bg-primary">
                                                <?php echo $tiempoDetalle['texto']; ?> (<?php echo $tiempoDetalle['total_horas']; ?>h)
                                            </span>
                                        </p>
                                        <p><strong>Estado:</strong> 
                                            <?php if ($caja['estado'] == 'abierto'): ?>
                                                <span class="badge bg-success">Abierto</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Cerrado</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6><i class="fas fa-chart-bar"></i> Estadísticas de Operación</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Kilos Comprados:</strong> <?php echo number_format($caja['kilos_comprados'], 2); ?> kg</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Kilos Vendidos:</strong> <?php echo number_format($caja['kilos_vendidos'], 2); ?> kg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- SCRIPT MEJORADO: Actualización en tiempo real de horas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar tiempo en tiempo real
    function actualizarTiempos() {
        const elementosTiempo = document.querySelectorAll('.tiempo-activo');
        
        elementosTiempo.forEach(function(elemento) {
            const inicioTimestamp = parseInt(elemento.getAttribute('data-inicio'));
            const ahora = Math.floor(Date.now() / 1000);
            const diferencia = ahora - inicioTimestamp;
            
            const horas = Math.floor(diferencia / 3600);
            const minutos = Math.floor((diferencia % 3600) / 60);
            
            elemento.textContent = horas + 'h ' + minutos + 'm';
        });
    }
    
    // Actualizar cada minuto
    actualizarTiempos();
    setInterval(actualizarTiempos, 60000);
    
    // Prevenir doble envío del formulario de cerrar caja
    const closeCajaForm = document.getElementById('closeCajaForm');
    const btnCerrarCaja = document.getElementById('btnCerrarCaja');
    
    if (closeCajaForm && btnCerrarCaja) {
        closeCajaForm.addEventListener('submit', function(e) {
            btnCerrarCaja.disabled = true;
            btnCerrarCaja.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cerrando...';
            
            setTimeout(function() {
                btnCerrarCaja.disabled = false;
                btnCerrarCaja.innerHTML = '<i class="fas fa-check"></i> Sí, cerrar caja';
            }, 5000);
        });
    }
    
    // Prevenir doble envío del formulario de abrir caja
    const openCajaForm = document.getElementById('openCajaForm');
    if (openCajaForm) {
        openCajaForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Abriendo...';
        });
    }
});
</script>

<style>
/* ESTILOS MEJORADOS PARA MEJOR VISIBILIDAD */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

/* Estilos específicos para la tabla del historial */
.tabla-historial {
    background-color: #ffffff;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.tabla-historial thead th {
    background-color: #343a40 !important;
    color: #ffffff !important;
    font-weight: 600;
    border: none;
    padding: 12px 8px;
    text-align: center;
    font-size: 0.9rem;
}

.fila-historial {
    background-color: #ffffff !important;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.fila-historial:hover {
    background-color: #f8f9fa !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.fila-historial:nth-child(even) {
    background-color: #f8f9fa !important;
}

.fila-historial:nth-child(even):hover {
    background-color: #e9ecef !important;
}

/* Estilos para campos específicos con mejor contraste */
.campo-nombre {
    font-weight: 600 !important;
    color: #2c3e50 !important;
    font-size: 0.95rem;
    padding: 12px 8px !important;
}

.campo-numero {
    color: #495057 !important;
    font-weight: 500 !important;
    text-align: center;
    font-size: 0.9rem;
    padding: 12px 8px !important;
}

.campo-dinero {
    color: #28a745 !important;
    font-weight: 600 !important;
    text-align: center;
    font-size: 0.9rem;
    padding: 12px 8px !important;
}

.campo-fecha {
    color: #6c757d !important;
    font-weight: 500 !important;
    text-align: center;
    font-size: 0.85rem;
    padding: 12px 8px !important;
}

/* NUEVOS ESTILOS: Para el campo de tiempo */
.campo-tiempo {
    text-align: center;
    padding: 12px 8px !important;
    font-weight: 600 !important;
}

.tiempo-activo {
    background-color: #17a2b8 !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 6px 12px;
    border-radius: 20px;
    animation: pulse 2s infinite;
}

.tiempo-cerrado {
    color: #6c757d !important;
    font-weight: 600;
    font-size: 0.9rem;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.campo-estado {
    text-align: center;
    padding: 12px 8px !important;
}

.campo-accion {
    text-align: center;
    padding: 12px 8px !important;
}

/* Badges con mejor visibilidad */
.estado-abierto {
    background-color: #28a745 !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 6px 12px;
    border-radius: 20px;
}

.estado-cerrado {
    background-color: #6c757d !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 6px 12px;
    border-radius: 20px;
}

/* Botón de detalles mejorado */
.boton-detalles {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.boton-detalles:hover {
    background-color: #e0a800 !important;
    border-color: #d39e00 !important;
    color: #212529 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Responsive mejorado */
@media (max-width: 768px) {
    .tabla-historial {
        font-size: 0.8rem;
    }
    
    .tabla-historial th,
    .tabla-historial td {
        padding: 8px 4px !important;
    }
    
    .campo-nombre,
    .campo-numero,
    .campo-dinero,
    .campo-fecha,
    .campo-tiempo {
        font-size: 0.8rem !important;
    }
    
    .boton-detalles {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    
    .tiempo-activo {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}

/* Efectos adicionales para mejor UX */
.table-responsive {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.badge {
    font-size: 0.75em;
    text-shadow: none;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}

/* Animaciones suaves */
.fila-historial,
.boton-detalles,
.estado-abierto,
.estado-cerrado,
.tiempo-activo {
    transition: all 0.3s ease;
}

/* Mejor contraste para texto */
.tabla-historial tbody td {
    color: #212529 !important;
    opacity: 1 !important;
}
</style>
