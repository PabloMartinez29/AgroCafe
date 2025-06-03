<?php
require_once 'config/database.php';
require_once 'lib/pdf_generator.php';

if (!isset($_GET['factura_id'])) {
    die('Factura ID no proporcionado');
}

$facturaId = intval($_GET['factura_id']);
$factura = fetchOne("SELECT * FROM facturas WHERE id = ?", [$facturaId]);
if (!$factura) {
    die('Factura no encontrada');
}

$pdfContent = ($factura['tipo_transaccion'] === 'venta') ? 
    PDFGenerator::generarFactura($facturaId) : 
    PDFGenerator::generarFacturaCampesino($factura['compra_id']);

if ($pdfContent) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="factura_' . $factura['numero_factura'] . '.pdf"');
    echo $pdfContent;
    exit();
} else {
    echo 'Error al generar el PDF';
}
?>