<?php
require_once 'config/database.php';

$campesino_id = $_SESSION['user_id'];

// Precios del campesino
$preciosCampesino = fetchAll("
    SELECT tc.nombre, tc.variedad,
           AVG(c.precio_kg) as precio_promedio,
           SUM(c.cantidad) as cantidad_total,
           COUNT(c.id) as num_ventas,
           MAX(c.fecha_compra) as ultima_venta
    FROM compras c
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    WHERE c.campesino_id = ? AND c.estado = 'completada'
    GROUP BY tc.id, tc.nombre, tc.variedad
    ORDER BY precio_promedio DESC
", [$campesino_id]);

// Precios del mercado
$preciosMercado = fetchAll("
    SELECT tc.nombre, tc.variedad,
           AVG(c.precio_kg) as precio_mercado
    FROM compras c
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    WHERE c.estado = 'completada'
    AND c.fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY tc.id, tc.nombre, tc.variedad
");

// Mapear precios de mercado
$mercadoMap = [];
foreach ($preciosMercado as $precio) {
    $mercadoMap[$precio['nombre']] = $precio['precio_mercado'];
}

// Evolución de precios del campesino (para gráfico de líneas)
$evolucionPrecios = fetchAll("
    SELECT 
        YEAR(c.fecha_compra) as año,
        MONTH(c.fecha_compra) as mes,
        AVG(c.precio_kg) as precio_promedio,
        SUM(c.cantidad) as cantidad_total,
        SUM(c.total) as ingresos_mes
    FROM compras c
    WHERE c.campesino_id = ? AND c.estado = 'completada'
    AND c.fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY YEAR(c.fecha_compra), MONTH(c.fecha_compra)
    ORDER BY año ASC, mes ASC
", [$campesino_id]);

// Datos para comparación con mercado (últimos 6 meses)
$comparacionMercado = fetchAll("
    SELECT 
        tc.nombre,
        tc.variedad,
        AVG(CASE WHEN c.campesino_id = ? THEN c.precio_kg END) as mi_precio,
        AVG(CASE WHEN c.campesino_id != ? THEN c.precio_kg END) as precio_mercado
    FROM compras c
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    WHERE c.estado = 'completada'
    AND c.fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY tc.id, tc.nombre, tc.variedad
    HAVING mi_precio IS NOT NULL AND precio_mercado IS NOT NULL
    ORDER BY tc.nombre
", [$campesino_id, $campesino_id]);

// Estadísticas generales
$estadisticasGenerales = fetchOne("
    SELECT 
        COUNT(*) as total_ventas,
        SUM(cantidad) as total_cantidad,
        SUM(total) as total_ingresos,
        AVG(precio_kg) as precio_promedio_general,
        MIN(precio_kg) as precio_minimo,
        MAX(precio_kg) as precio_maximo
    FROM compras
    WHERE campesino_id = ? AND estado = 'completada'
", [$campesino_id]);

// Mejor mes
$mejorMes = fetchOne("
    SELECT 
        YEAR(fecha_compra) as año,
        MONTH(fecha_compra) as mes,
        SUM(total) as ingresos
    FROM compras
    WHERE campesino_id = ? AND estado = 'completada'
    GROUP BY YEAR(fecha_compra), MONTH(fecha_compra)
    ORDER BY ingresos DESC
    LIMIT 1
", [$campesino_id]);

// Tendencia actual vs anterior
$tendenciaActual = fetchOne("
    SELECT AVG(precio_kg) as precio_promedio
    FROM compras
    WHERE campesino_id = ? AND estado = 'completada'
    AND fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)
", [$campesino_id]);

$tendenciaAnterior = fetchOne("
    SELECT AVG(precio_kg) as precio_promedio
    FROM compras
    WHERE campesino_id = ? AND estado = 'completada'
    AND fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    AND fecha_compra < DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)
", [$campesino_id]);

$porcentajeTendencia = 0;
if ($tendenciaAnterior['precio_promedio'] > 0) {
    $porcentajeTendencia = (($tendenciaActual['precio_promedio'] - $tendenciaAnterior['precio_promedio']) / $tendenciaAnterior['precio_promedio']) * 100;
}

// Preparar datos para JavaScript
$mesesLabels = [];
$preciosEvolucionData = [];
$ingresosData = [];

foreach ($evolucionPrecios as $mes) {
    $mesesLabels[] = date('M Y', mktime(0, 0, 0, $mes['mes'], 1, $mes['año']));
    $preciosEvolucionData[] = round($mes['precio_promedio'], 0);
    $ingresosData[] = round($mes['ingresos_mes'], 0);
}

// Datos para comparación con mercado
$tiposCafeLabels = [];
$misPreciosData = [];
$preciosMercadoData = [];

foreach ($comparacionMercado as $comp) {
    $tiposCafeLabels[] = $comp['nombre'];
    $misPreciosData[] = round($comp['mi_precio'], 0);
    $preciosMercadoData[] = round($comp['precio_mercado'], 0);
}
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #228B22, #32CD32);
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
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .analysis-card h4 {
        color: #228B22;
        margin-bottom: 1rem;
    }

    .price-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #eee;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-radius: 5px;
    }

    .price-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
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

    .trend-neutral {
        color: #6c757d;
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

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .comparison-table th, .comparison-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .comparison-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #228B22;
    }

    .highlight-box {
        background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border-left: 4px solid #228B22;
    }

    @media print {
        .chart-container {
            height: 250px;
        }
    }
</style>

<h3 style="color: #228B22; margin-bottom: 2rem;">
    <i class="fas fa-chart-line"></i> Mi Análisis de Precios
</h3>

<?php if ($estadisticasGenerales['total_ventas'] > 0): ?>
    <!-- Estadísticas principales -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>$<?php echo number_format($estadisticasGenerales['precio_promedio_general'], 0, ',', '.'); ?></h4>
            <p>Mi Precio Promedio</p>
        </div>
        <div class="stat-card">
            <h4>$<?php echo number_format($estadisticasGenerales['precio_maximo'], 0, ',', '.'); ?></h4>
            <p>Mejor Precio Obtenido</p>
        </div>
        <div class="stat-card">
            <h4><?php echo number_format($estadisticasGenerales['total_cantidad'], 1); ?> kg</h4>
            <p>Total Vendido</p>
        </div>
        <div class="stat-card">
            <h4>$<?php echo number_format($estadisticasGenerales['total_ingresos'], 0, ',', '.'); ?></h4>
            <p>Ingresos Totales</p>
        </div>
    </div>

    <!-- Resumen de rendimiento -->
    <div class="highlight-box">
        <h4 style="color: #228B22; margin-bottom: 1rem;">
            <i class="fas fa-trophy"></i> Resumen de Mi Rendimiento
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div>
                <strong>Tendencia de Precios:</strong>
                <span class="price-trend <?php echo $porcentajeTendencia > 0 ? 'trend-up' : ($porcentajeTendencia < 0 ? 'trend-down' : 'trend-neutral'); ?>">
                    <?php if ($porcentajeTendencia > 0): ?>
                        <i class="fas fa-arrow-up"></i> +<?php echo number_format($porcentajeTendencia, 1); ?>%
                    <?php elseif ($porcentajeTendencia < 0): ?>
                        <i class="fas fa-arrow-down"></i> <?php echo number_format($porcentajeTendencia, 1); ?>%
                    <?php else: ?>
                        <i class="fas fa-minus"></i> Sin cambios
                    <?php endif; ?>
                </span>
            </div>
            <div>
                <strong>Mejor Mes:</strong>
                <?php if ($mejorMes): ?>
                    <?php echo $mejorMes['mes']; ?>/<?php echo $mejorMes['año']; ?> 
                    ($<?php echo number_format($mejorMes['ingresos'], 0, ',', '.'); ?>)
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </div>
            <div>
                <strong>Total de Ventas:</strong> <?php echo $estadisticasGenerales['total_ventas']; ?>
            </div>
        </div>
    </div>

    <!-- Mis precios por tipo de café -->
    <div class="analysis-card">
        <h4><i class="fas fa-coffee"></i> Mis Precios por Tipo de Café</h4>
        <?php if ($preciosCampesino && count($preciosCampesino) > 0): ?>
            <?php foreach ($preciosCampesino as $precio): ?>
                <?php 
                $precioMercado = isset($mercadoMap[$precio['nombre']]) ? $mercadoMap[$precio['nombre']] : 0;
                $diferencia = $precio['precio_promedio'] - $precioMercado;
                $porcentajeDif = $precioMercado > 0 ? ($diferencia / $precioMercado) * 100 : 0;
                ?>
                <div class="price-item">
                    <div>
                        <strong><?php echo htmlspecialchars($precio['nombre']); ?></strong>
                        <span style="color: #666;">(<?php echo ucfirst($precio['variedad']); ?>)</span>
                        <br>
                        <small style="color: #666;">
                            <?php echo number_format($precio['cantidad_total'], 1); ?> kg vendidos en <?php echo $precio['num_ventas']; ?> venta(s)
                        </small>
                    </div>
                    <div style="text-align: right;">
                        <div><strong>Mi Precio: $<?php echo number_format($precio['precio_promedio'], 0, ',', '.'); ?></strong></div>
                        <?php if ($precioMercado > 0): ?>
                            <div style="font-size: 0.9rem; color: #666;">
                                Mercado: $<?php echo number_format($precioMercado, 0, ',', '.'); ?>
                            </div>
                            <div class="price-trend <?php echo $diferencia > 0 ? 'trend-up' : ($diferencia < 0 ? 'trend-down' : 'trend-neutral'); ?>">
                                <?php if ($diferencia > 0): ?>
                                    <i class="fas fa-arrow-up"></i> +$<?php echo number_format($diferencia, 0, ',', '.'); ?>
                                <?php elseif ($diferencia < 0): ?>
                                    <i class="fas fa-arrow-down"></i> $<?php echo number_format($diferencia, 0, ',', '.'); ?>
                                <?php else: ?>
                                    <i class="fas fa-equals"></i> Igual al mercado
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
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
            <h4><i class="fas fa-chart-line"></i> Evolución de Mis Precios</h4>
            <div class="chart-container">
                <canvas id="evolucionChart" class="chart-canvas"></canvas>
            </div>
        </div>
        
        <div class="analysis-card">
            <h4><i class="fas fa-chart-bar"></i> Comparación con el Mercado</h4>
            <div class="chart-container">
                <canvas id="comparacionChart" class="chart-canvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Evolución mensual -->
    <?php if ($evolucionPrecios && count($evolucionPrecios) > 0): ?>
        <div class="analysis-card">
            <h4><i class="fas fa-calendar"></i> Mi Evolución Mensual (Últimos 6 meses)</h4>
            <div style="overflow-x: auto;">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Mes/Año</th>
                            <th>Precio Promedio</th>
                            <th>Cantidad Vendida</th>
                            <th>Ingresos</th>
                            <th>Tendencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $prevPrice = null;
                        foreach ($evolucionPrecios as $index => $mes): 
                            $tendencia = '';
                            if ($prevPrice !== null) {
                                $diff = $mes['precio_promedio'] - $prevPrice;
                                if ($diff > 0) {
                                    $tendencia = '<span class="trend-up"><i class="fas fa-arrow-up"></i> +' . number_format($diff, 0) . '</span>';
                                } elseif ($diff < 0) {
                                    $tendencia = '<span class="trend-down"><i class="fas fa-arrow-down"></i> ' . number_format($diff, 0) . '</span>';
                                } else {
                                    $tendencia = '<span class="trend-neutral"><i class="fas fa-minus"></i></span>';
                                }
                            }
                            $prevPrice = $mes['precio_promedio'];
                        ?>
                            <tr>
                                <td><?php echo $mes['mes'] . '/' . $mes['año']; ?></td>
                                <td>$<?php echo number_format($mes['precio_promedio'], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($mes['cantidad_total'], 1); ?> kg</td>
                                <td>$<?php echo number_format($mes['ingresos_mes'], 0, ',', '.'); ?></td>
                                <td><?php echo $tendencia; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; color: #666;">
        <i class="fas fa-chart-line" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <h4>No hay datos para analizar</h4>
        <p>Aún no tienes ventas registradas para generar análisis de precios.</p>
        <p>Una vez que realices ventas, podrás ver aquí estadísticas detalladas de tus precios y tendencias.</p>
    </div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Datos desde PHP
const mesesLabels = <?php echo json_encode($mesesLabels); ?>;
const preciosEvolucionData = <?php echo json_encode($preciosEvolucionData); ?>;
const ingresosData = <?php echo json_encode($ingresosData); ?>;
const tiposCafeLabels = <?php echo json_encode($tiposCafeLabels); ?>;
const misPreciosData = <?php echo json_encode($misPreciosData); ?>;
const preciosMercadoData = <?php echo json_encode($preciosMercadoData); ?>;

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
                color: 'rgba(34, 139, 34, 0.1)'
            },
            ticks: {
                color: '#228B22'
            }
        },
        x: {
            grid: {
                color: 'rgba(34, 139, 34, 0.1)'
            },
            ticks: {
                color: '#228B22'
            }
        }
    }
};

// Gráfico de Evolución de Precios
document.addEventListener('DOMContentLoaded', function() {
    const ctxEvolucion = document.getElementById('evolucionChart').getContext('2d');
    
    new Chart(ctxEvolucion, {
        type: 'line',
        data: {
            labels: mesesLabels,
            datasets: [
                {
                    label: 'Mis Precios',
                    data: preciosEvolucionData,
                    borderColor: '#228B22',
                    backgroundColor: 'rgba(34, 139, 34, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#228B22',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Ingresos (x1000)',
                    data: ingresosData.map(val => val / 1000),
                    borderColor: '#32CD32',
                    backgroundColor: 'rgba(50, 205, 50, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#32CD32',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                title: {
                    display: true,
                    text: 'Mi Evolución de Precios e Ingresos',
                    color: '#228B22',
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
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Precio por Kg (COP)',
                        color: '#228B22',
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
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Ingresos (Miles COP)',
                        color: '#32CD32',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        color: '#32CD32',
                        callback: function(value) {
                            return '$' + (value * 1000).toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Comparación con el Mercado
    const ctxComparacion = document.getElementById('comparacionChart').getContext('2d');
    
    new Chart(ctxComparacion, {
        type: 'bar',
        data: {
            labels: tiposCafeLabels,
            datasets: [
                {
                    label: 'Mis Precios',
                    data: misPreciosData,
                    backgroundColor: 'rgba(34, 139, 34, 0.8)',
                    borderColor: '#228B22',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Precio de Mercado',
                    data: preciosMercadoData,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: '#ffc107',
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
                    text: 'Mis Precios vs Mercado por Tipo de Café',
                    color: '#228B22',
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
                        text: 'Precio por Kg (COP)',
                        color: '#228B22',
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
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});

// Función para imprimir el reporte
function imprimirReporte() {
    window.print();
}
</script>
