<?php
require_once 'config/database.php';
require_once 'lib/pdf_generator.php';

if (!isset($_GET['factura_id'])) {
    die('Factura ID no proporcionado');
}

$facturaId = intval($_GET['factura_id']);

// Obtener la factura con todos los datos necesarios
$factura = fetchOne("SELECT * FROM facturas WHERE id = ?", [$facturaId]);
if (!$factura) {
    die('Factura no encontrada');
}

try {
    $pdfContent = null;
    
    // CORREGIDO: Usar siempre el método principal que maneja ambos tipos
    $pdfContent = PDFGenerator::generarFactura($facturaId);
    
    if ($pdfContent) {
        // Limpiar cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Headers para PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="factura_' . $factura['numero_factura'] . '.pdf"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $pdfContent;
        exit();
    } else {
        throw new Exception('No se pudo generar el contenido del PDF');
    }
    
} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error generando PDF para factura ID $facturaId: " . $e->getMessage());
    
    // Mostrar error detallado para debugging
    echo '<div style="font-family: Arial, sans-serif; padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;">';
    echo '<h3>🚨 Error al generar PDF</h3>';
    echo '<p><strong>Factura ID:</strong> ' . $facturaId . '</p>';
    echo '<p><strong>Número Factura:</strong> ' . htmlspecialchars($factura['numero_factura']) . '</p>';
    echo '<p><strong>Tipo Transacción:</strong> ' . htmlspecialchars($factura['tipo_transaccion']) . '</p>';
    
    if ($factura['tipo_transaccion'] === 'compra') {
        echo '<p><strong>Compra ID:</strong> ' . $factura['compra_id'] . '</p>';
    } else {
        echo '<p><strong>Venta ID:</strong> ' . $factura['venta_id'] . '</p>';
    }
    
    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<hr>';
    echo '<p><strong>Datos de la factura:</strong></p>';
    echo '<pre style="background: #fff; padding: 10px; border-radius: 3px; overflow: auto;">';
    print_r($factura);
    echo '</pre>';
    echo '<p><a href="javascript:history.back()" style="color: #721c24; text-decoration: none;">← Volver atrás</a></p>';
    echo '</div>';
}
?>