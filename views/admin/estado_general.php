<?php
// Verificar si la sesión no está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

// Obtener filtros
$filtro_periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'dia';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

// Ajustar fechas según el período
switch ($filtro_periodo) {
    case 'mes':
        $fecha_inicio = date('Y-m-01');
        $fecha_fin = date('Y-m-t');
        break;
    case 'año':
        $fecha_inicio = date('Y-01-01');
        $fecha_fin = date('Y-12-31');
        break;
    case 'personalizado':
        // Usar las fechas proporcionadas
        break;
    default: // día
        $fecha_inicio = date('Y-m-d');
        $fecha_fin = date('Y-m-d');
        break;
}

// Función para obtener datos de un período específico
function obtenerDatosPeriodo($fecha_inicio, $fecha_fin) {
    $valor_invertido = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) BETWEEN ? AND ?", [$fecha_inicio, $fecha_fin])['total'] ?? 0;
    $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE estado = 'completada' AND DATE(fecha_compra) BETWEEN ? AND ?", [$fecha_inicio, $fecha_fin])['total'] ?? 0;
    $kilos_vendidos = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) BETWEEN ? AND ?", [$fecha_inicio, $fecha_fin])['total'] ?? 0;
    $saldo_recuperado = fetchOne("SELECT COALESCE(SUM(cantidad * precio_kg), 0) as total FROM ventas WHERE estado = 'completada' AND DATE(fecha_venta) BETWEEN ? AND ?", [$fecha_inicio, $fecha_fin])['total'] ?? 0;
    
    $recuperado = min($valor_invertido, $saldo_recuperado);
    $ganancias = max(0, $saldo_recuperado - $valor_invertido);
    $margen_ganancias = ($valor_invertido > 0) ? (($ganancias / $valor_invertido) * 100) : 0;
    
    return [
        'valor_invertido' => $valor_invertido,
        'kilos_comprados' => $kilos_comprados,
        'kilos_vendidos' => $kilos_vendidos,
        'saldo_recuperado' => $saldo_recuperado,
        'recuperado' => $recuperado,
        'ganancias' => $ganancias,
        'margen_ganancias' => $margen_ganancias
    ];
}

// Obtener datos del período seleccionado
$datos_periodo = obtenerDatosPeriodo($fecha_inicio, $fecha_fin);

// Obtener datos para la gráfica según el período
$datos_grafica = [];
$labels_grafica = [];

if ($filtro_periodo == 'dia') {
    // Últimos 7 días
    for ($i = 6; $i >= 0; $i--) {
        $fecha = date('Y-m-d', strtotime("-$i days"));
        $datos_dia = obtenerDatosPeriodo($fecha, $fecha);
        $datos_grafica[] = $datos_dia['margen_ganancias'];
        $labels_grafica[] = date('d/m', strtotime($fecha));
    }
} elseif ($filtro_periodo == 'mes') {
    // Últimos 12 meses
    for ($i = 11; $i >= 0; $i--) {
        $fecha_mes = date('Y-m-01', strtotime("-$i months"));
        $fecha_fin_mes = date('Y-m-t', strtotime($fecha_mes));
        $datos_mes = obtenerDatosPeriodo($fecha_mes, $fecha_fin_mes);
        $datos_grafica[] = $datos_mes['margen_ganancias'];
        $labels_grafica[] = date('M Y', strtotime($fecha_mes));
    }
} elseif ($filtro_periodo == 'año') {
    // Últimos 5 años
    for ($i = 4; $i >= 0; $i--) {
        $año = date('Y') - $i;
        $fecha_año_inicio = "$año-01-01";
        $fecha_año_fin = "$año-12-31";
        $datos_año = obtenerDatosPeriodo($fecha_año_inicio, $fecha_año_fin);
        $datos_grafica[] = $datos_año['margen_ganancias'];
        $labels_grafica[] = $año;
    }
} else { // personalizado
    // Dividir el período personalizado en días
    $fecha_actual = new DateTime($fecha_inicio);
    $fecha_final = new DateTime($fecha_fin);
    
    while ($fecha_actual <= $fecha_final) {
        $fecha_str = $fecha_actual->format('Y-m-d');
        $datos_dia = obtenerDatosPeriodo($fecha_str, $fecha_str);
        $datos_grafica[] = $datos_dia['margen_ganancias'];
        $labels_grafica[] = $fecha_actual->format('d/m');
        $fecha_actual->add(new DateInterval('P1D'));
    }
}

// Calcular kilos disponibles totales
$tiposCafe = fetchAll("SELECT id FROM tipos_cafe WHERE activo = 1");
$kilos_disponibles_total = 0;
foreach ($tiposCafe as $tipo) {
    $kilos_comprados = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM compras WHERE tipo_cafe_id = ? AND estado = 'completada'", [$tipo['id']])['total'] ?? 0;
    $kilos_vendidos = fetchOne("SELECT COALESCE(SUM(cantidad), 0) as total FROM ventas WHERE tipo_cafe_id = ? AND estado = 'completada'", [$tipo['id']])['total'] ?? 0;
    $kilos_disponibles_tipo = $kilos_comprados - $kilos_vendidos;
    
    // Usar valor de sesión si existe
    $kilos_sesion = $_SESSION['kilos_disponibles_' . $tipo['id']] ?? $kilos_disponibles_tipo;
    $kilos_disponibles_total += $kilos_sesion;
}

// Obtener información de la caja actual
$caja_id = $_SESSION['caja_id'] ?? 'Sin caja activa';

// Generar título del eje X ANTES del JavaScript
$titulo_eje_x = '';
switch($filtro_periodo) {
    case 'dia': 
        $titulo_eje_x = 'Días'; 
        break;
    case 'mes': 
        $titulo_eje_x = 'Meses'; 
        break;
    case 'año': 
        $titulo_eje_x = 'Años'; 
        break;
    case 'personalizado': 
        $titulo_eje_x = 'Período'; 
        break;
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

    .btn-info {
        background: #17a2b8;
    }

    .btn-info:hover {
        background: #138496;
    }

    .dashboard-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        max-height: 500px; /* Límite para las tarjetas del dashboard */
        overflow: hidden; /* Evitar desbordamiento */
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

    .stat-card h3 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-card p {
        margin: 0;
        opacity: 0.9;
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 2px solid #8B4513;
    }

    #gananciaChart {
        width: 100%;
        height: 350px; /* Altura fija más pequeña */
        margin-top: 1rem;
        max-height: 350px; /* Límite máximo de altura */
    }

    /* Contenedor específico para la gráfica */
    .chart-container {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        height: 450px; /* Altura fija para el contenedor de la gráfica */
        overflow: hidden;
    }

    .chart-container h4 {
        margin-bottom: 1rem;
        height: 30px; /* Altura fija para el título */
    }

    .chart-wrapper {
        height: 350px; /* Altura específica para el canvas */
        width: 100%;
        position: relative;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h3><i class="fas fa-tachometer-alt"></i> Estado General del Negocio</h3>
    <a href="?view=ventas" class="btn">
        <i class="fas fa-arrow-left"></i> Volver a Ventas
    </a>
</div>

<!-- Filtros de Período -->
<div class="filter-section">
    <h5 style="color: #8B4513; margin-bottom: 1rem;">
        <i class="fas fa-filter"></i> Filtros de Período
    </h5>
    <form method="GET" id="filtroForm">
        <input type="hidden" name="view" value="estado-general">
        <div class="form-row">
            <div class="form-group">
                <label>Período:</label>
                <select name="periodo" onchange="toggleFechasPersonalizadas()" id="selectPeriodo">
                    <option value="dia" <?php echo $filtro_periodo == 'dia' ? 'selected' : ''; ?>>Día Actual</option>
                    <option value="mes" <?php echo $filtro_periodo == 'mes' ? 'selected' : ''; ?>>Mes Actual</option>
                    <option value="año" <?php echo $filtro_periodo == 'año' ? 'selected' : ''; ?>>Año Actual</option>
                    <option value="personalizado" <?php echo $filtro_periodo == 'personalizado' ? 'selected' : ''; ?>>Personalizado</option>
                </select>
            </div>
            <div class="form-group" id="fechaInicio" style="<?php echo $filtro_periodo != 'personalizado' ? 'display: none;' : ''; ?>">
                <label>Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
            </div>
            <div class="form-group" id="fechaFin" style="<?php echo $filtro_periodo != 'personalizado' ? 'display: none;' : ''; ?>">
                <label>Fecha Fin:</label>
                <input type="date" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
            </div>
            <div class="form-group" style="display: flex; align-items: end;">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Información de la Caja Actual -->
<div class="dashboard-card">
    <h4><i class="fas fa-cash-register"></i> Información de Caja</h4>
    <p><strong>Caja Actual:</strong> <?php echo htmlspecialchars($caja_id); ?></p>
    <p><strong>Kilos Totales Disponibles:</strong> <?php echo number_format($kilos_disponibles_total, 2); ?> kg</p>
    <?php if (isset($_SESSION['caja_iniciada'])): ?>
        <p><strong>Caja iniciada:</strong> <?php echo date('d/m/Y H:i:s', strtotime($_SESSION['caja_iniciada'])); ?></p>
    <?php endif; ?>
</div>

<!-- Métricas del Período Seleccionado -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>$<?php echo number_format($datos_periodo['valor_invertido'], 0, '.', ','); ?></h3>
        <p><i class="fas fa-money-bill-wave"></i> Valor Invertido</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($datos_periodo['recuperado'], 0, '.', ','); ?></h3>
        <p><i class="fas fa-hand-holding-usd"></i> Recuperado</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($datos_periodo['ganancias'], 0, '.', ','); ?></h3>
        <p><i class="fas fa-chart-line"></i> Ganancias</p>
    </div>
    <div class="stat-card">
        <h3><?php echo number_format($datos_periodo['margen_ganancias'], 2); ?>%</h3>
        <p><i class="fas fa-percentage"></i> Margen de Ganancias</p>
    </div>
    <div class="stat-card">
        <h3><?php echo number_format($datos_periodo['kilos_comprados'], 2); ?></h3>
        <p><i class="fas fa-weight"></i> Kilos Comprados</p>
    </div>
    <div class="stat-card">
        <h3><?php echo number_format($datos_periodo['kilos_vendidos'], 2); ?></h3>
        <p><i class="fas fa-shipping-fast"></i> Kilos Vendidos</p>
    </div>
</div>

<!-- Información del Período -->
<div class="dashboard-card">
    <h4><i class="fas fa-calendar-alt"></i> Período Analizado</h4>
    <p><strong>Desde:</strong> <?php echo date('d/m/Y', strtotime($fecha_inicio)); ?></p>
    <p><strong>Hasta:</strong> <?php echo date('d/m/Y', strtotime($fecha_fin)); ?></p>
    <p><strong>Tipo de Período:</strong> <?php 
        switch($filtro_periodo) {
            case 'dia': echo 'Día Actual'; break;
            case 'mes': echo 'Mes Actual'; break;
            case 'año': echo 'Año Actual'; break;
            case 'personalizado': echo 'Período Personalizado'; break;
        }
    ?></p>
</div>

<!-- Gráfica de Margen de Ganancias -->
<div class="chart-container">
    <h4><i class="fas fa-chart-bar"></i> Evolución del Margen de Ganancias</h4>
    <div class="chart-wrapper">
        <canvas id="gananciaChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleFechasPersonalizadas() {
    const periodo = document.getElementById('selectPeriodo').value;
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');
    
    if (periodo === 'personalizado') {
        fechaInicio.style.display = 'block';
        fechaFin.style.display = 'block';
    } else {
        fechaInicio.style.display = 'none';
        fechaFin.style.display = 'none';
    }
}

// Configurar la gráfica
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('gananciaChart').getContext('2d');
    
    const datos = <?php echo json_encode($datos_grafica); ?>;
    const labels = <?php echo json_encode($labels_grafica); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Margen de Ganancias (%)',
                data: datos,
                backgroundColor: 'rgba(139, 69, 19, 0.1)',
                borderColor: 'rgba(139, 69, 19, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(139, 69, 19, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Importante: mantener en false
            aspectRatio: 2, // Relación de aspecto 2:1 (ancho:alto)
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución del Margen de Ganancias - <?php echo ucfirst($filtro_periodo); ?>',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    color: '#8B4513'
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Porcentaje (%)',
                        color: '#8B4513',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(139, 69, 19, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '<?php echo $titulo_eje_x; ?>',
                        color: '#8B4513',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(139, 69, 19, 0.1)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#A0522D'
                }
            }
        }
    });
});

// Auto-submit del formulario cuando cambia el período (excepto personalizado)
document.getElementById('selectPeriodo').addEventListener('change', function() {
    if (this.value !== 'personalizado') {
        document.getElementById('filtroForm').submit();
    }
});
</script>
