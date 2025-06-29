<?php
require_once 'config/database.php';
require_once 'lib/pdf_generator.php';

// Función para crear factura automáticamente si no existe
function crearFacturaAutomatica($compraId) {
    // Verificar si ya existe una factura para esta compra
    $facturaExistente = fetchOne("SELECT id FROM facturas WHERE compra_id = ?", [$compraId]);
    if ($facturaExistente) {
        return $facturaExistente['id'];
    }
    
    // Obtener datos de la compra
    $compra = fetchOne("SELECT * FROM compras WHERE id = ?", [$compraId]);
    if (!$compra) {
        return false;
    }
    
    // Crear factura automáticamente
    $numeroFactura = 'FC' . str_pad($compraId, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
    $facturaData = [
        'venta_id' => NULL,
        'compra_id' => $compraId,
        'numero_factura' => $numeroFactura,
        'fecha_factura' => $compra['fecha_compra'],
        'subtotal' => $compra['total'],
        'impuestos' => 0,
        'total' => $compra['total'],
        'estado_pago' => 'pendiente',
        'fecha_vencimiento' => date('Y-m-d', strtotime($compra['fecha_compra'] . ' +30 days')),
        'tipo_transaccion' => 'compra'
    ];
    
    if (insertRecord('facturas', $facturaData)) {
        return fetchOne("SELECT id FROM facturas WHERE compra_id = ?", [$compraId])['id'];
    }
    
    return false;
}

// MANEJO DE DESCARGA PDF - DEBE IR AL INICIO ANTES DE CUALQUIER HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'download_pdf') {
    $compraId = intval($_POST['compra_id']);
    
    // Verificar que la compra pertenece al campesino logueado
    $compra = fetchOne("SELECT * FROM compras WHERE id = ? AND campesino_id = ?", [$compraId, $_SESSION['user_id']]);
    
    if ($compra) {
        // Crear factura automáticamente si no existe
        $facturaId = crearFacturaAutomatica($compraId);
        
        if ($facturaId) {
            try {
                // LIMPIAR COMPLETAMENTE EL BUFFER DE SALIDA
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                // Generar el PDF
                $pdfContent = PDFGenerator::generarFactura($facturaId);
                
                if ($pdfContent && strlen($pdfContent) > 0) {
                    // Verificar que el contenido sea realmente un PDF
                    if (substr($pdfContent, 0, 4) !== '%PDF') {
                        throw new Exception("El contenido generado no es un PDF válido");
                    }
                    
                    // Configurar headers correctos
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename="factura_campesino_FC' . str_pad($compraId, 3, '0', STR_PAD_LEFT) . '.pdf"');
                    header('Content-Length: ' . strlen($pdfContent));
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    
                    // Enviar el PDF
                    echo $pdfContent;
                    exit();
                } else {
                    throw new Exception("Error al generar el contenido del PDF");
                }
            } catch (Exception $e) {
                error_log("Error al generar PDF para factura_id: $facturaId, compra_id: $compraId - " . $e->getMessage());
                
                // Limpiar cualquier salida
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                // Enviar error HTTP
                http_response_code(500);
                header('Content-Type: text/plain');
                echo "Error al generar el PDF: " . $e->getMessage();
                exit();
            }
        } else {
            error_log("Error al crear factura automática para compra_id: $compraId");
            $error = "Error al crear la factura. Consulta con el administrador.";
        }
    } else {
        $error = "No tienes permisos para descargar esta factura o la compra no existe.";
    }
}

$campesino_id = $_SESSION['user_id'];

// Resto del código para mostrar la vista...
$facturasCampesino = fetchAll("
    SELECT c.*, tc.nombre as tipo_cafe, tc.variedad,
           pc.fecha_pago, pc.monto as monto_pagado, pc.metodo_pago, pc.referencia,
           CASE 
               WHEN pc.estado = 'completado' THEN 'Pagada'
               ELSE 'Pendiente'
           END as estado_factura,
           CONCAT('FC', LPAD(c.id, 3, '0'), '-', YEAR(c.fecha_compra)) as numero_factura
    FROM compras c
    JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
    LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id AND pc.estado = 'completado'
    WHERE c.campesino_id = ? AND c.estado = 'completada'
    ORDER BY c.fecha_compra DESC
", [$campesino_id]);

$totalFacturas = count($facturasCampesino);
$facturasPagadas = count(array_filter($facturasCampesino, function($f) { return $f['estado_factura'] == 'Pagada'; }));
$facturasPendientes = $totalFacturas - $facturasPagadas;
$montoTotal = array_sum(array_column($facturasCampesino, 'total'));
?>

<style>
    .btn {
        background: #228B22;
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
        background: #32CD32;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: #dc3545;
    }

    .btn-danger:hover {
        background: #c82333;
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
        color: #228B22;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-pagada {
        background: #d4edda;
        color: #155724;
    }

    .status-pendiente {
        background: #fff3cd;
        color: #856404;
    }

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

    .alert {
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
        border-left: 4px solid;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left-color: #dc3545;
    }

    .info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border-left: 4px solid #228B22;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<h3 style="color: #228B22; margin-bottom: 2rem;">
    <i class="fas fa-file-invoice"></i> Mis Facturas
</h3>

<div class="stats-grid">
    <div class="stat-card">
        <h4><?php echo $totalFacturas; ?></h4>
        <p><i class="fas fa-file-invoice"></i> Total Facturas</p>
    </div>
    <div class="stat-card">
        <h4><?php echo $facturasPagadas; ?></h4>
        <p><i class="fas fa-check-circle"></i> Pagadas</p>
    </div>
    <div class="stat-card">
        <h4><?php echo $facturasPendientes; ?></h4>
        <p><i class="fas fa-clock"></i> Pendientes</p>
    </div>
    <div class="stat-card">
        <h4>$<?php echo number_format($montoTotal, 0, ',', '.'); ?></h4>
        <p><i class="fas fa-dollar-sign"></i> Monto Total</p>
    </div>
</div>

<div class="info-box">
    <p><i class="fas fa-info-circle" style="color: #228B22;"></i> 
    Aquí puedes ver todas tus facturas y descargarlas en formato PDF. Las facturas se generan automáticamente cuando se registra una compra.</p>
</div>

<?php if ($facturasCampesino && count($facturasCampesino) > 0): ?>
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Fecha</th>
                    <th>Tipo Café</th>
                    <th>Cantidad</th>
                    <th>Precio/kg</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturasCampesino as $factura): ?>
                    <tr>
                        <td><strong><?php echo $factura['numero_factura']; ?></strong></td>
                        <td><?php echo date('d/m/Y', strtotime($factura['fecha_compra'])); ?></td>
                        <td>
                            <?php echo htmlspecialchars($factura['tipo_cafe']); ?>
                            <br><small style="color: #666;"><?php echo ucfirst($factura['variedad']); ?></small>
                        </td>
                        <td><?php echo number_format($factura['cantidad'], 2); ?> kg</td>
                        <td>$<?php echo number_format($factura['precio_kg'], 0, ',', '.'); ?></td>
                        <td><strong>$<?php echo number_format($factura['total'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($factura['estado_factura']); ?>">
                                <?php echo $factura['estado_factura']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($factura['fecha_pago']): ?>
                                <?php echo date('d/m/Y', strtotime($factura['fecha_pago'])); ?>
                                <br><small style="color: #666;"><?php echo ucfirst($factura['metodo_pago']); ?></small>
                            <?php else: ?>
                                <span style="color: #666;">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="showLoading(this)">
                                <input type="hidden" name="action" value="download_pdf">
                                <input type="hidden" name="compra_id" value="<?php echo $factura['id']; ?>">
                                <button type="submit" class="btn" style="padding: 0.5rem;" title="Descargar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </form>
                            
                            <?php if ($factura['referencia']): ?>
                                <button onclick="showReference('<?php echo htmlspecialchars($factura['referencia']); ?>')" 
                                        class="btn" style="padding: 0.5rem; background: #17a2b8;" title="Ver Referencia">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; color: #666;">
        <i class="fas fa-file-invoice" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <h4>No hay facturas disponibles</h4>
        <p>Aún no tienes facturas generadas. Las facturas se crean automáticamente cuando realizas ventas.</p>
    </div>
<?php endif; ?>

<div id="referenceModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 15% auto; padding: 20px; border-radius: 10px; width: 90%; max-width: 400px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h4 style="color: #228B22;">Referencia de Pago</h4>
            <span onclick="closeReferenceModal()" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
        </div>
        <p id="referenceText" style="background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace;"></p>
        <button onclick="closeReferenceModal()" class="btn" style="width: 100%; margin-top: 1rem;">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>
</div>

<script>
function showReference(reference) {
    document.getElementById('referenceText').textContent = reference;
    document.getElementById('referenceModal').style.display = 'block';
}

function closeReferenceModal() {
    document.getElementById('referenceModal').style.display = 'none';
}

function showLoading(form) {
    const button = form.querySelector('button');
    const icon = button.querySelector('i');
    
    button.disabled = true;
    button.classList.add('loading');
    
    if (icon) {
        icon.className = 'fas fa-spinner fa-spin';
    }
    
    // Restaurar después de 10 segundos por si hay error
    setTimeout(() => {
        button.disabled = false;
        button.classList.remove('loading');
        if (icon) {
            icon.className = 'fas fa-file-pdf';
        }
    }, 10000);
}

window.onclick = function(event) {
    const modal = document.getElementById('referenceModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
