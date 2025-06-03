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


$tendenciasMensuales = fetchAll("
    SELECT tc.variedad,
           YEAR(ph.fecha_precio) as año,
           MONTH(ph.fecha_precio) as mes,
           COALESCE(AVG(CASE WHEN ph.tipo_operacion = 'compra' THEN ph.precio END), 0) as precio_compra_promedio,
           COALESCE(AVG(CASE WHEN ph.tipo_operacion = 'venta' THEN ph.precio END), 0) as precio_venta_promedio
    FROM precios_historicos ph
    JOIN tipos_cafe tc ON ph.tipo_cafe_id = tc.id
    $whereClause
    AND ph.fecha_precio >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY tc.variedad, YEAR(ph.fecha_precio), MONTH(ph.fecha_precio)
    ORDER BY año DESC, mes DESC
", $params);


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

    .chart-placeholder {
        height: 300px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        text-align: center;
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
        <div class="chart-placeholder">
            <div>
                <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; color: #8B4513;"></i>
                <p>Gráfico de tendencias de precios</p>
                <p style="font-size: 0.8rem;">Implementar con Chart.js</p>
            </div>
        </div>
    </div>
    
    <div class="analysis-card">
        <h4><i class="fas fa-chart-bar"></i> Volumen de Transacciones</h4>
        <div class="chart-placeholder">
            <div>
                <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem; color: #8B4513;"></i>
                <p>Gráfico de volúmenes</p>
                <p style="font-size: 0.8rem;">Comparativo mensual</p>
            </div>
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


