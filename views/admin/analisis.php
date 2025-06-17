<?php
require_once 'config/database.php';

$tipoFiltro = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$periodoFiltro = isset($_GET['periodo']) ? $_GET['periodo'] : 'mensual';

$whereClause = "WHERE 1=1";
$params = [];

if ($tipoFiltro) {
    $whereClause .= " AND tc.variedad = ?";
    $params[] = $tipoFiltro;
}

// Precios actuales
$preciosActuales = fetchAll("
    SELECT tc.nombre, tc.variedad, 
           COALESCE(AVG(CASE WHEN ph.tipo_operacion = 'compra' THEN ph.precio END), 0) as precio_compra,
           COALESCE(AVG(CASE WHEN ph.tipo_operacion = 'venta' THEN ph.precio END), 0) as precio_venta
    FROM tipos_cafe tc
    LEFT JOIN precios_historicos ph ON tc.id = ph.tipo_cafe_id 
    AND ph.fecha_precio >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    $whereClause
    GROUP BY tc.id, tc.nombre, tc.variedad
    ORDER BY tc.nombre
", $params);

// Tendencias mensuales para gráficos
$tendenciasMensuales = fetchAll("
    SELECT tc.variedad,
           YEAR(ph.fecha_precio) as año,
           MONTH(ph.fecha_precio) as mes,
           COALESCE(AVG(CASE WHEN ph.tipo_operacion = 'compra' THEN ph.precio END), 0) as precio_compra_promedio,
           COALESCE(AVG(CASE WHEN ph.tipo_operacion = 'venta' THEN ph.precio END), 0) as precio_venta_promedio,
           COUNT(ph.id) as total_transacciones
    FROM precios_historicos ph
    JOIN tipos_cafe tc ON ph.tipo_cafe_id = tc.id
    $whereClause
    AND ph.fecha_precio >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY tc.variedad, YEAR(ph.fecha_precio), MONTH(ph.fecha_precio)
    ORDER BY año ASC, mes ASC
", $params);

// Datos para gráfico de volumen por mes
$volumenMensual = fetchAll("
    SELECT 
        YEAR(fecha_compra) as año,
        MONTH(fecha_compra) as mes,
        COUNT(*) as total_compras,
        SUM(cantidad) as kilos_comprados,
        SUM(total) as valor_compras
    FROM compras 
    WHERE estado = 'completada' 
    AND fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY YEAR(fecha_compra), MONTH(fecha_compra)
    
    UNION ALL
    
    SELECT 
        YEAR(fecha_venta) as año,
        MONTH(fecha_venta) as mes,
        COUNT(*) as total_ventas,
        SUM(cantidad) as kilos_vendidos,
        SUM(total) as valor_ventas
    FROM ventas 
    WHERE estado = 'completada' 
    AND fecha_venta >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY YEAR(fecha_venta), MONTH(fecha_venta)
    ORDER BY año ASC, mes ASC
");

// Estadísticas generales
$estadisticas = fetchOne("
    SELECT 
        COALESCE(COUNT(DISTINCT c.id), 0) as total_compras,
        COALESCE(COUNT(DISTINCT v.id), 0) as total_ventas,
        COALESCE(AVG(c.precio_kg), 0) as precio_promedio_compra,
        COALESCE(AVG(v.precio_kg), 0) as precio_promedio_venta,
        COALESCE(SUM(c.total), 0) as total_invertido,
        COALESCE(SUM(v.total), 0) as total_ingresos
    FROM compras c
    CROSS JOIN ventas v
    WHERE c.estado = 'completada' AND v.estado = 'completada'
");

if (!$estadisticas) {
    $estadisticas = [
        'total_compras' => 0,
        'total_ventas' => 0,
        'precio_promedio_compra' => 0,
        'precio_promedio_venta' => 0,
        'total_invertido' => 0,
        'total_ingresos' => 0
    ];
}

$tiposCafe = fetchAll("SELECT DISTINCT variedad FROM tipos_cafe WHERE activo = 1");

// Preparar datos para JavaScript
$mesesLabels = [];
$preciosCompraData = [];
$preciosVentaData = [];
$volumenComprasData = [];
$volumenVentasData = [];

// Procesar tendencias mensuales para el gráfico de precios
$tendenciasPorMes = [];
foreach ($tendenciasMensuales as $tendencia) {
    $mesKey = $tendencia['año'] . '-' . str_pad($tendencia['mes'], 2, '0', STR_PAD_LEFT);
    if (!isset($tendenciasPorMes[$mesKey])) {
        $tendenciasPorMes[$mesKey] = [
            'mes' => $tendencia['mes'],
            'año' => $tendencia['año'],
            'precio_compra' => 0,
            'precio_venta' => 0,
            'count' => 0
        ];
    }
    $tendenciasPorMes[$mesKey]['precio_compra'] += $tendencia['precio_compra_promedio'];
    $tendenciasPorMes[$mesKey]['precio_venta'] += $tendencia['precio_venta_promedio'];
    $tendenciasPorMes[$mesKey]['count']++;
}

// Procesar volumen mensual
$volumenPorMes = [];
foreach ($volumenMensual as $vol) {
    $mesKey = $vol['año'] . '-' . str_pad($vol['mes'], 2, '0', STR_PAD_LEFT);
    if (!isset($volumenPorMes[$mesKey])) {
        $volumenPorMes[$mesKey] = [
            'mes' => $vol['mes'],
            'año' => $vol['año'],
            'compras' => 0,
            'ventas' => 0
        ];
    }
    // Distinguir entre compras y ventas basado en las columnas
    if (isset($vol['total_compras'])) {
        $volumenPorMes[$mesKey]['compras'] = $vol['total_compras'];
    }
    if (isset($vol['total_ventas'])) {
        $volumenPorMes[$mesKey]['ventas'] = $vol['total_ventas'];
    }
}

// Generar arrays para JavaScript
ksort($tendenciasPorMes);
ksort($volumenPorMes);

foreach ($tendenciasPorMes as $mesKey => $data) {
    $mesesLabels[] = date('M Y', mktime(0, 0, 0, $data['mes'], 1, $data['año']));
    $preciosCompraData[] = round($data['precio_compra'] / max($data['count'], 1), 0);
    $preciosVentaData[] = round($data['precio_venta'] / max($data['count'], 1), 0);
}

foreach ($volumenPorMes as $mesKey => $data) {
    $volumenComprasData[] = $data['compras'];
    $volumenVentasData[] = $data['ventas'];
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

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #8B4513;
        font-weight: 500;
    }

    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group select:focus {
        outline: none;
        border-color: #8B4513;
    }

    .stats-grid {
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

    .stat-card h4 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .stat-card p {
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .analysis-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .analysis-card h4 {
        color: #8B4513;
        margin-bottom: 1rem;
    }

    .price-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
    }

    .price-item:last-child {
        border-bottom: none;
    }

    .price-trend {
        font-weight: bold;
    }

    .trend-up {
        color: #28a745;
    }

    .trend-down {
        color: #dc3545;
    }

    .chart-container {
        height: 350px;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        position: relative;
    }

    .chart-canvas {
        width: 100% !important;
        height: 300px !important;
    }

    @media print {
        .btn {
            display: none;
        }
    }
</style>

<h3 style="margin-bottom: 2rem;">Análisis de Precios del Café</h3>

<!-- Filtros -->
<div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; margin-bottom: 2rem; align-items: end;">
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
        <form method="GET">
            <input type="hidden" name="view" value="analisis">
            <div class="form-group">
                <label>Tipo de Café:</label>
                <select name="tipo" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <?php if ($tiposCafe): ?>
                        <?php foreach ($tiposCafe as $tipo): ?>
                            <option value="<?php echo $tipo['variedad']; ?>" 
                                    <?php echo ($tipoFiltro == $tipo['variedad']) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($tipo['variedad']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>
    
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
        <h4 style="color: #8B4513; margin-bottom: 1rem;">Estadísticas Generales</h4>
        <div style="font-size: 0.9rem;">
            <div><strong>Total Compras:</strong> <?php echo $estadisticas['total_compras']; ?></div>
            <div><strong>Total Ventas:</strong> <?php echo $estadisticas['total_ventas']; ?></div>
            <div><strong>Margen Promedio:</strong> 
                <?php 
                $margen = floatval($estadisticas['precio_promedio_venta']) - floatval($estadisticas['precio_promedio_compra']);
                echo '$' . number_format($margen, 0, ',', '.'); 
                ?>
            </div>
        </div>
    </div>
    
    <button class="btn" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir Reporte
    </button>
</div>

<!-- Estadísticas principales -->
<div class="stats-grid">
    <div class="stat-card">
        <h4>$<?php echo number_format(floatval($estadisticas['precio_promedio_compra']), 0, ',', '.'); ?></h4>
        <p>Precio Promedio Compra</p>
    </div>
    <div class="stat-card">
        <h4>$<?php echo number_format(floatval($estadisticas['precio_promedio_venta']), 0, ',', '.'); ?></h4>
        <p>Precio Promedio Venta</p>
    </div>
    <div class="stat-card">
        <h4>$<?php echo number_format(floatval($estadisticas['total_invertido']), 0, ',', '.'); ?></h4>
        <p>Total Invertido</p>
    </div>
    <div class="stat-card">
        <h4>$<?php echo number_format(floatval($estadisticas['total_ingresos']), 0, ',', '.'); ?></h4>
        <p>Total Ingresos</p>
    </div>
</div>

<!-- Precios actuales -->
<div class="analysis-card">
    <h4><i class="fas fa-chart-line"></i> Precios Actuales por Tipo</h4>
    <?php if ($preciosActuales && count($preciosActuales) > 0): ?>
        <?php foreach ($preciosActuales as $precio): ?>
            <div class="price-item">
                <div>
                    <strong><?php echo htmlspecialchars($precio['nombre']); ?></strong>
                    <span style="color: #666;">(<?php echo ucfirst($precio['variedad']); ?>)</span>
                </div>
                <div>
                    <span>Compra: $<?php echo number_format(floatval($precio['precio_compra']), 0, ',', '.'); ?></span> |
                    <span>Venta: $<?php echo number_format(floatval($precio['precio_venta']), 0, ',', '.'); ?></span>
                    <span class="price-trend trend-up">
                        <i class="fas fa-arrow-up"></i> +5.2%
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No hay datos de precios disponibles</p>
    <?php endif; ?>
</div>

<!-- Gráficos -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="analysis-card">
        <h4><i class="fas fa-chart-line"></i> Tendencia de Precios (6 meses)</h4>
        <div class="chart-container">
            <canvas id="tendenciaChart" class="chart-canvas"></canvas>
        </div>
    </div>
    
    <div class="analysis-card">
        <h4><i class="fas fa-chart-bar"></i> Volumen de Transacciones</h4>
        <div class="chart-container">
            <canvas id="volumenChart" class="chart-canvas"></canvas>
        </div>
    </div>
</div>

<!-- Tendencias mensuales -->
<?php if ($tendenciasMensuales && count($tendenciasMensuales) > 0): ?>
<div class="analysis-card">
    <h4><i class="fas fa-calendar"></i> Tendencias Mensuales</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 0.5rem; border: 1px solid #ddd;">Período</th>
                    <th style="padding: 0.5rem; border: 1px solid #ddd;">Variedad</th>
                    <th style="padding: 0.5rem; border: 1px solid #ddd;">Precio Compra</th>
                    <th style="padding: 0.5rem; border: 1px solid #ddd;">Precio Venta</th>
                    <th style="padding: 0.5rem; border: 1px solid #ddd;">Margen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tendenciasMensuales as $tendencia): ?>
                    <tr>
                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                            <?php echo $tendencia['mes'] . '/' . $tendencia['año']; ?>
                        </td>
                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                            <?php echo ucfirst($tendencia['variedad']); ?>
                        </td>
                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                            $<?php echo number_format(floatval($tendencia['precio_compra_promedio']), 0, ',', '.'); ?>
                        </td>
                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                            $<?php echo number_format(floatval($tendencia['precio_venta_promedio']), 0, ',', '.'); ?>
                        </td>
                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                            $<?php echo number_format(floatval($tendencia['precio_venta_promedio']) - floatval($tendencia['precio_compra_promedio']), 0, ',', '.'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Datos desde PHP
const mesesLabels = <?php echo json_encode($mesesLabels); ?>;
const preciosCompraData = <?php echo json_encode($preciosCompraData); ?>;
const preciosVentaData = <?php echo json_encode($preciosVentaData); ?>;
const volumenComprasData = <?php echo json_encode($volumenComprasData); ?>;
const volumenVentasData = <?php echo json_encode($volumenVentasData); ?>;

// Configuración común para los gráficos
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(139, 69, 19, 0.1)'
            },
            ticks: {
                color: '#8B4513'
            }
        },
        x: {
            grid: {
                color: 'rgba(139, 69, 19, 0.1)'
            },
            ticks: {
                color: '#8B4513'
            }
        }
    }
};

// Gráfico de Tendencia de Precios
document.addEventListener('DOMContentLoaded', function() {
    const ctxTendencia = document.getElementById('tendenciaChart').getContext('2d');
    
    new Chart(ctxTendencia, {
        type: 'line',
        data: {
            labels: mesesLabels,
            datasets: [
                {
                    label: 'Precio Compra',
                    data: preciosCompraData,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                },
                {
                    label: 'Precio Venta',
                    data: preciosVentaData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                title: {
                    display: true,
                    text: 'Evolución de Precios de Compra y Venta',
                    color: '#8B4513',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                ...commonOptions.scales,
                y: {
                    ...commonOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Precio (COP)',
                        color: '#8B4513',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        ...commonOptions.scales.y.ticks,
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Volumen de Transacciones
    const ctxVolumen = document.getElementById('volumenChart').getContext('2d');
    
    new Chart(ctxVolumen, {
        type: 'bar',
        data: {
            labels: mesesLabels,
            datasets: [
                {
                    label: 'Compras',
                    data: volumenComprasData,
                    backgroundColor: 'rgba(139, 69, 19, 0.8)',
                    borderColor: '#8B4513',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Ventas',
                    data: volumenVentasData,
                    backgroundColor: 'rgba(160, 82, 45, 0.8)',
                    borderColor: '#A0522D',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                title: {
                    display: true,
                    text: 'Volumen Mensual de Transacciones',
                    color: '#8B4513',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                ...commonOptions.scales,
                y: {
                    ...commonOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Número de Transacciones',
                        color: '#8B4513',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});

// Función para actualizar gráficos cuando cambian los filtros
function actualizarGraficos() {
    // Esta función se puede expandir para recargar datos via AJAX
    console.log('Actualizando gráficos...');
}
</script>
