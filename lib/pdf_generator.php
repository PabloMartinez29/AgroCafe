<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

class PDFGenerator {
    public static function generarFactura($facturaId) {
        
        if (!class_exists('TCPDF')) {
            error_log('TCPDF no está disponible');
            return false;
        }
        
        // Obtener datos de la factura
        $factura = fetchOne("SELECT * FROM facturas WHERE id = ?", [$facturaId]);
        if (!$factura) {
            error_log("Factura no encontrada con ID: $facturaId");
            return false;
        }
        
        // Determinar el tipo de factura y obtener los datos correspondientes
        if ($factura['tipo_transaccion'] === 'venta') {
            return self::generarFacturaCooperativa($facturaId);
        } else {
            return self::generarFacturaCampesinoCorregido($facturaId);
        }
    }
    
    private static function generarFacturaCooperativa($facturaId) {
        $factura = fetchOne("
            SELECT f.*, v.cantidad, v.precio_kg, v.total as venta_total,
                   COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
                   COALESCE(c.email, '') as cliente_email,
                   COALESCE(c.telefono, '') as cliente_telefono,
                   COALESCE(c.direccion, '') as cliente_direccion,
                   tc.nombre as tipo_cafe, tc.variedad,
                   COALESCE(tc.tipo_procesamiento, 'normal') as tipo_procesamiento
            FROM facturas f
            JOIN ventas v ON f.venta_id = v.id
            LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
            JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
            WHERE f.id = ?", [$facturaId]);
        
        if (!$factura) {
            error_log("Datos de factura cooperativa no encontrados para ID: $facturaId");
            return false;
        }
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('AgroCafé');
        $pdf->SetAuthor('AgroCafé');
        $pdf->SetTitle('Factura ' . $factura['numero_factura']);
        $pdf->SetSubject('Factura de Venta');
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
        
        $html = '
        <table style="width: 100%; border-bottom: 2px solid #8B4513;">
            <tr>
                <td style="width: 70%;">
                    <h1 style="color: #8B4513;">AgroCafé</h1>
                    <p>Sistema de Compra y Venta de Café</p>
                    <p>NIT: 900.123.456-7</p>
                    <p>Bogotá, Colombia</p>
                </td>
                <td style="width: 30%; text-align: right;">
                    <h2 style="color: #8B4513;">FACTURA</h2>
                    <p><strong>' . $factura['numero_factura'] . '</strong></p>
                    <p>Fecha: ' . date('d/m/Y', strtotime($factura['fecha_factura'])) . '</p>
                </td>
            </tr>
        </table>
        <br><br>
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%;">
                    <h3 style="color: #8B4513;">FACTURAR A:</h3>
                    <p><strong>' . htmlspecialchars($factura['cliente_nombre']) . '</strong></p>
                    ' . ($factura['cliente_telefono'] ? '<p>Tel: ' . htmlspecialchars($factura['cliente_telefono']) . '</p>' : '') . '
                    ' . ($factura['cliente_email'] ? '<p>Email: ' . htmlspecialchars($factura['cliente_email']) . '</p>' : '') . '
                </td>
                <td style="width: 50%; text-align: right;">
                    <p>Fecha Vencimiento: ' . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . '</p>
                    <p>Estado: <strong>' . ucfirst($factura['estado_pago']) . '</strong></p>
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="text-align: left;">Descripción</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: right;">Precio Unitario</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . htmlspecialchars($factura['tipo_cafe']) . ' (' . ucfirst($factura['variedad']) . ')' . 
                    ($factura['tipo_procesamiento'] !== 'normal' ? '<br><small>Procesamiento: ' . ucfirst($factura['tipo_procesamiento']) . '</small>' : '') . '</td>
                    <td style="text-align: center;">' . number_format($factura['cantidad'], 2) . ' kg</td>
                    <td style="text-align: right;">$' . number_format($factura['precio_kg'], 0, ',', '.') . '</td>
                    <td style="text-align: right;">$' . number_format($factura['venta_total'], 0, ',', '.') . '</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%;">
                    <table border="1" cellpadding="5" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td style="text-align: right;">$' . number_format($factura['subtotal'], 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td><strong>IVA (19%):</strong></td>
                            <td style="text-align: right;">$' . number_format($factura['impuestos'], 0, ',', '.') . '</td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <td><strong>TOTAL:</strong></td>
                            <td style="text-align: right;"><strong>$' . number_format($factura['total'], 0, ',', '.') . '</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br><br>
        <div style="text-align: center; color: #666; font-size: 9px;">
            <p>Gracias por su compra. Esta factura fue generada electrónicamente.</p>
            <p>Agrocafé - Sistema de Gestión de Café | www.AgroCafé.com</p>
        </div>
        ';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        return $pdf->Output('', 'S'); 
    }
    
    private static function generarFacturaCampesinoCorregido($facturaId) {
        if (!class_exists('TCPDF')) {
            error_log('TCPDF no está disponible para generarFacturaCampesino');
            return false;
        }
        
        // CORREGIDO: Consultar a través de la tabla facturas como las cooperativas
        $factura = fetchOne("
            SELECT f.*, 
                   comp.cantidad, comp.precio_kg, comp.total as venta_total,
                   comp.fecha_compra, comp.estado as estado_compra,
                   u.nombre as cliente_nombre,
                   COALESCE(u.email, '') as cliente_email,
                   COALESCE(u.telefono, '') as cliente_telefono,
                   COALESCE(u.direccion, '') as cliente_direccion,
                   COALESCE(u.cedula, '') as cliente_cedula,
                   tc.nombre as tipo_cafe, tc.variedad,
                   COALESCE(tc.tipo_procesamiento, 'normal') as tipo_procesamiento,
                   pc.fecha_pago, pc.monto as monto_pagado, pc.metodo_pago, pc.referencia
            FROM facturas f
            JOIN compras comp ON f.compra_id = comp.id
            JOIN usuarios u ON comp.campesino_id = u.id
            JOIN tipos_cafe tc ON comp.tipo_cafe_id = tc.id
            LEFT JOIN pagos_campesinos pc ON comp.id = pc.compra_id AND pc.estado = 'completado'
            WHERE f.id = ?", [$facturaId]);
        
        if (!$factura) {
            error_log("Datos de factura campesino no encontrados para ID: $facturaId");
            return false;
        }
        
        // Validar datos esenciales
        if (!isset($factura['cantidad']) || !isset($factura['precio_kg']) || !isset($factura['total']) ||
            $factura['cantidad'] === null || $factura['precio_kg'] === null || $factura['total'] === null) {
            error_log("Datos inválidos en factura campesino con ID $facturaId: " . print_r($factura, true));
            return false;
        }
        
        error_log("Generando PDF para factura campesino ID: $facturaId");
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('AgroCafé');
        $pdf->SetAuthor('AgroCafé');
        $pdf->SetTitle('Factura ' . $factura['numero_factura']);
        $pdf->SetSubject('Factura de Compra de Café');
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
        
        $html = '
        <table style="width: 100%; border-bottom: 2px solid #228B22;">
            <tr>
                <td style="width: 70%;">
                    <h1 style="color: #228B22;">AgroCafé</h1>
                    <p>Sistema de Compra y Venta de Café</p>
                    <p>NIT: 900.123.456-7</p>
                    <p>Bogotá, Colombia</p>
                    <p>Tel: +57 350-888-4148</p>
                </td>
                <td style="width: 30%; text-align: right;">
                    <h2 style="color: #228B22;">FACTURA CAMPESINO</h2>
                    <p><strong>' . $factura['numero_factura'] . '</strong></p>
                    <p>Fecha: ' . date('d/m/Y', strtotime($factura['fecha_factura'])) . '</p>
                </td>
            </tr>
        </table>
        <br><br>
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%;">
                    <h3 style="color: #228B22;">PROVEEDOR (CAMPESINO):</h3>
                    <p><strong>' . htmlspecialchars($factura['cliente_nombre']) . '</strong></p>
                    ' . ($factura['cliente_cedula'] ? '<p>Cédula: ' . htmlspecialchars($factura['cliente_cedula']) . '</p>' : '') . '
                    ' . ($factura['cliente_telefono'] ? '<p>Tel: ' . htmlspecialchars($factura['cliente_telefono']) . '</p>' : '') . '
                    ' . ($factura['cliente_email'] ? '<p>Email: ' . htmlspecialchars($factura['cliente_email']) . '</p>' : '') . '
                    ' . ($factura['cliente_direccion'] ? '<p>Dirección: ' . htmlspecialchars($factura['cliente_direccion']) . '</p>' : '') . '
                </td>
                <td style="width: 50%; text-align: right;">
                    <p>Fecha Vencimiento: ' . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . '</p>
                    <p>Estado: <strong>' . ucfirst($factura['estado_pago']) . '</strong></p>
                    ' . ($factura['fecha_pago'] ? '<p>Fecha Pago: ' . date('d/m/Y', strtotime($factura['fecha_pago'])) . '</p>' : '<p style="color: #dc3545;">Pago Pendiente</p>') . '
                    ' . ($factura['metodo_pago'] ? '<p>Método: ' . ucfirst($factura['metodo_pago']) . '</p>' : '') . '
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="text-align: left;">Descripción del Producto</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: right;">Precio por kg</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>' . htmlspecialchars($factura['tipo_cafe']) . '</strong><br>
                        <small>Variedad: ' . ucfirst($factura['variedad']) . '</small>' .
                        ($factura['tipo_procesamiento'] !== 'normal' ? '<br><small>Procesamiento: ' . ucfirst($factura['tipo_procesamiento']) . '</small>' : '') . '</td>
                    <td style="text-align: center;">' . number_format($factura['cantidad'], 2) . ' kg</td>
                    <td style="text-align: right;">$' . number_format($factura['precio_kg'], 0, ',', '.') . '</td>
                    <td style="text-align: right;"><strong>$' . number_format($factura['venta_total'], 0, ',', '.') . '</strong></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <table border="1" cellpadding="5" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td style="text-align: right;">$' . number_format($factura['subtotal'], 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td><strong>Impuestos:</strong></td>
                            <td style="text-align: right;">$' . number_format($factura['impuestos'], 0, ',', '.') . '</td>
                        </tr>
                        <tr style="background-color: #e8f5e8;">
                            <td><strong>TOTAL A PAGAR:</strong></td>
                            <td style="text-align: right;"><strong>$' . number_format($factura['total'], 0, ',', '.') . '</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ' . ($factura['fecha_pago'] ? '
        <br>
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; background-color: #e8f5e8;">
            <tr>
                <td>
                    <h4 style="color: #228B22;">INFORMACIÓN DE PAGO</h4>
                    <p><strong>Fecha de Pago:</strong> ' . date('d/m/Y', strtotime($factura['fecha_pago'])) . '</p>
                    <p><strong>Método de Pago:</strong> ' . ucfirst($factura['metodo_pago']) . '</p>
                    <p><strong>Monto Pagado:</strong> $' . number_format($factura['monto_pagado'], 0, ',', '.') . '</p>
                    ' . ($factura['referencia'] ? '<p><strong>Referencia:</strong> ' . htmlspecialchars($factura['referencia']) . '</p>' : '') . '
                    <p style="color: #228B22;"><strong>✓ PAGO COMPLETADO</strong></p>
                </td>
            </tr>
        </table>' : '
        <br>
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; background-color: #fff3cd;">
            <tr>
                <td>
                    <h4 style="color: #856404;">ESTADO DE PAGO</h4>
                    <p style="color: #856404;"><strong>⏳ PAGO PENDIENTE</strong></p>
                    <p>El pago de esta compra está pendiente de procesamiento.</p>
                </td>
            </tr>
        </table>') . '
        <br><br>
        <div style="text-align: center; color: #666; font-size: 9px;">
            <p>Gracias por ser parte de AgroCafé. Esta factura fue generada electrónicamente.</p>
            <p>AgroCafé - Conectando Campesinos con el Mundo | www.AgroCafé.com</p>
            <p>Para consultas: agrocafe1129@gmail.com | +57 350-888-4148</p>
        </div>
        ';
        
        try {
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdfOutput = $pdf->Output('', 'S');
            error_log("PDF generado exitosamente para factura campesino ID: $facturaId, longitud: " . strlen($pdfOutput));
            return $pdfOutput;
        } catch (Exception $e) {
            error_log("Error en writeHTML para factura campesino ID $facturaId: " . $e->getMessage());
            return false;
        }
    }
    
    // MÉTODO LEGACY - Mantener por compatibilidad pero redirigir al nuevo método
    public static function generarFacturaCampesino($compraId) {
        error_log("ADVERTENCIA: Usando método legacy generarFacturaCampesino con compraId: $compraId");
        
        // Buscar la factura que corresponde a esta compra
        $factura = fetchOne("SELECT id FROM facturas WHERE compra_id = ? AND tipo_transaccion = 'compra'", [$compraId]);
        
        if ($factura) {
            return self::generarFacturaCampesinoCorregido($factura['id']);
        } else {
            error_log("No se encontró factura para compraId: $compraId");
            return false;
        }
    }
}
?>
