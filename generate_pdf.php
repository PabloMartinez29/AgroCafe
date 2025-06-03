<?php
require_once 'config/database.php';
require_once 'lib/pdf_generator.php';

$facturaId = intval($_GET['factura_id']);
$pdfContent = PDFGenerator::generarFactura($facturaId);

if ($pdfContent) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="factura_' . $facturaId . '.pdf"');
    echo $pdfContent;
} else {
    http_response_code(500);
    echo 'Error al generar el PDF';
}
?>