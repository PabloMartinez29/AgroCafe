<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';


class PDFGenerator {
    public static function generarFactura($facturaId) {
        
        if (!class_exists('TCPDF')) {
            error_log('TCPDF no está disponible');
            return false;
        }
        
        $factura = fetchOne("
            SELECT f.*, v.cantidad, v.precio_kg, v.total as venta_total,
                   COALESCE(c.nombre, v.cliente_nombre) as cliente_nombre,
                   COALESCE(c.email, '') as cliente_email,
                   tc.nombre as tipo_cafe, tc.variedad
            FROM facturas f
            JOIN ventas v ON f.venta_id = v.id
            LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
            JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
            WHERE f.id = ?", [$facturaId]);
        
        if (!$factura) return false;
        
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
                    <td>' . htmlspecialchars($factura['tipo_cafe']) . ' (' . ucfirst($factura['variedad']) . ')</td>
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
    
    public static function generarFacturaCampesino($compraId) {
        if (!class_exists('TCPDF')) {
            return self::generarFacturaCampesinoSimple($compraId);
        }
        
        $compra = fetchOne("
            SELECT c.*, u.nombre as campesino_nombre, u.email as campesino_email, 
                   u.telefono as campesino_telefono, u.direccion as campesino_direccion,
                   tc.nombre as tipo_cafe, tc.variedad,
                   pc.fecha_pago, pc.monto as monto_pagado, pc.metodo_pago, pc.referencia
            FROM compras c
            JOIN usuarios u ON c.campesino_id = u.id
            JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
            LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id AND pc.estado = 'completado'
            WHERE c.id = ?", [$compraId]);
        
        if (!$compra) return false;
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('CaféTrade');
        $pdf->SetAuthor('CaféTrade');
        $pdf->SetTitle('Factura Campesino FC' . str_pad($compra['id'], 3, '0', STR_PAD_LEFT));
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
                    <p><strong>FC' . str_pad($compra['id'], 3, '0', STR_PAD_LEFT) . '</strong></p>
                    <p>Fecha: ' . date('d/m/Y', strtotime($compra['fecha_compra'])) . '</p>
                </td>
            </tr>
        </table>
        <br><br>
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%;">
                    <h3 style="color: #228B22;">PROVEEDOR (CAMPESINO):</h3>
                    <p><strong>' . htmlspecialchars($compra['campesino_nombre']) . '</strong></p>
                    <p>Tel: ' . htmlspecialchars($compra['campesino_telefono']) . '</p>
                    <p>Email: ' . htmlspecialchars($compra['campesino_email']) . '</p>
                    <p>Dirección: ' . htmlspecialchars($compra['campesino_direccion']) . '</p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <p>Estado: <strong>' . ucfirst($compra['estado']) . '</strong></p>
                    ' . ($compra['fecha_pago'] ? '<p>Fecha Pago: ' . date('d/m/Y', strtotime($compra['fecha_pago'])) . '</p>' : '<p style="color: #dc3545;">Pago Pendiente</p>') . '
                    ' . ($compra['metodo_pago'] ? '<p>Método: ' . ucfirst($compra['metodo_pago']) . '</p>' : '') . '
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
                    <td><strong>' . htmlspecialchars($compra['tipo_cafe']) . '</strong><br><small>Variedad: ' . ucfirst($compra['variedad']) . '</small></td>
                    <td style="text-align: center;">' . number_format($compra['cantidad'], 2) . ' kg</td>
                    <td style="text-align: right;">$' . number_format($compra['precio_kg'], 0, ',', '.') . '</td>
                    <td style="text-align: right;"><strong>$' . number_format($compra['total'], 0, ',', '.') . '</strong></td>
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
                            <td style="text-align: right;">$' . number_format($compra['total'], 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td><strong>Impuestos:</strong></td>
                            <td style="text-align: right;">$0</td>
                        </tr>
                        <tr style="background-color: #e8f5e8;">
                            <td><strong>TOTAL A PAGAR:</strong></td>
                            <td style="text-align: right;"><strong>$' . number_format($compra['total'], 0, ',', '.') . '</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ' . ($compra['fecha_pago'] ? '
        <br>
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; background-color: #e8f5e8;">
            <tr>
                <td>
                    <h4 style="color: #228B22;">INFORMACIÓN DE PAGO</h4>
                    <p><strong>Fecha de Pago:</strong> ' . date('d/m/Y', strtotime($compra['fecha_pago'])) . '</p>
                    <p><strong>Método de Pago:</strong> ' . ucfirst($compra['metodo_pago']) . '</p>
                    <p><strong>Monto Pagado:</strong> $' . number_format($compra['monto_pagado'], 0, ',', '.') . '</p>
                    ' . ($compra['referencia'] ? '<p><strong>Referencia:</strong> ' . htmlspecialchars($compra['referencia']) . '</p>' : '') . '
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
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        return $pdf->Output('', 'S'); // Devolver el PDF como cadena
    }
    
    public static function generarFacturaCampesinoSimple($compraId) {
        $compra = fetchOne("
            SELECT c.*, u.nombre as campesino_nombre, u.email as campesino_email, 
                   u.telefono as campesino_telefono, u.direccion as campesino_direccion,
                   tc.nombre as tipo_cafe, tc.variedad,
                   pc.fecha_pago, pc.monto as monto_pagado, pc.metodo_pago, pc.referencia
            FROM compras c
            JOIN usuarios u ON c.campesino_id = u.id
            JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id
            LEFT JOIN pagos_campesinos pc ON c.id = pc.compra_id AND pc.estado = 'completado'
            WHERE c.id = ?", [$compraId]);
        
        if (!$compra) return false;
        
        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Factura Campesino FC' . str_pad($compra['id'], 3, '0', STR_PAD_LEFT) . '</title><style>body{font-family:Arial,sans-serif;margin:20px;}.header{border-bottom:3px solid #228B22;padding-bottom:20px;margin-bottom:30px;}.company-info{float:left;width:60%;}.invoice-info{float:right;width:35%;text-align:right;}.clear{clear:both;}.customer-info{margin:20px 0;}.table{width:100%;border-collapse:collapse;margin:20px 0;}.table th,.table td{border:1px solid #ddd;padding:10px;text-align:left;}.table th{background-color:#f8f9fa;}.total-section{float:right;width:40%;margin-top:20px;}.payment-info{background-color:#e8f5e8;padding:15px;margin:20px 0;border-radius:5px;}.footer{text-align:center;margin-top:50px;font-size:12px;color:#666;}</style></head><body><div class="header"><div class="company-info"><h1 style="color:#228B22;">AgroCafé</h1><p>Sistema de Compra y Venta de Café</p><p>NIT: 900.123.456-7</p><p>Bogotá, Colombia</p><p>Tel: +57 1 234 5678</p></div><div class="invoice-info"><h2 style="color:#228B22;">FACTURA CAMPESINO</h2><p><strong>FC' . str_pad($compra['id'], 3, '0', STR_PAD_LEFT) . '</strong></p><p>Fecha: ' . date('d/m/Y', strtotime($compra['fecha_compra'])) . '</p></div><div class="clear"></div></div><div class="customer-info"><h3 style="color:#228B22;">PROVEEDOR (CAMPESINO):</h3><p><strong>' . htmlspecialchars($compra['campesino_nombre']) . '</strong></p><p>Tel: ' . htmlspecialchars($compra['campesino_telefono']) . '</p><p>Email: ' . htmlspecialchars($compra['campesino_email']) . '</p><p>Dirección: ' . htmlspecialchars($compra['campesino_direccion']) . '</p></div><table class="table"><thead><tr><th>Descripción del Producto</th><th>Cantidad</th><th>Precio por kg</th><th>Total</th></tr></thead><tbody><tr><td><strong>' . htmlspecialchars($compra['tipo_cafe']) . '</strong><br><small>Variedad: ' . ucfirst($compra['variedad']) . '</small></td><td>' . number_format($compra['cantidad'], 2) . ' kg</td><td>$' . number_format($compra['precio_kg'], 0, ',', '.') . '</td><td><strong>$' . number_format($compra['total'], 0, ',', '.') . '</strong></td></tr></tbody></table><div class="total-section"><table class="table"><tr><td><strong>Subtotal:</strong></td><td style="text-align:right;">$' . number_format($compra['total'], 0, ',', '.') . '</td></tr><tr><td><strong>Impuestos:</strong></td><td style="text-align:right;">$0</td></tr><tr style="background-color:#e8f5e8;"><td><strong>TOTAL A PAGAR:</strong></td><td style="text-align:right;"><strong>$' . number_format($compra['total'], 0, ',', '.') . '</strong></td></tr></table></div><div class="clear">';
        
        if ($compra['fecha_pago']) {
            $html .= '<div class="payment-info"><h4 style="color:#228B22;">INFORMACIÓN DE PAGO</h4><p><strong>Fecha de Pago:</strong> ' . date('d/m/Y', strtotime($compra['fecha_pago'])) . '</p><p><strong>Método de Pago:</strong> ' . ucfirst($compra['metodo_pago']) . '</p><p><strong>Monto Pagado:</strong> $' . number_format($compra['monto_pagado'], 0, ',', '.') . '</p>' . ($compra['referencia'] ? '<p><strong>Referencia:</strong> ' . htmlspecialchars($compra['referencia']) . '</p>' : '') . '<p style="color:#228B22;"><strong>✓ PAGO COMPLETADO</strong></p></div>';
        } else {
            $html .= '<div style="background-color:#fff3cd;padding:15px;margin:20px 0;border-radius:5px;"><h4 style="color:#856404;">ESTADO DE PAGO</h4><p style="color:#856404;"><strong>⏳ PAGO PENDIENTE</strong></p><p>El pago de esta compra está pendiente de procesamiento.</p></div>';
        }
        
        $html .= '<div class="footer"><p>Gracias por ser parte de CaféTrade. Esta factura fue generada electrónicamente.</p><p>AgroCafé - Conectando Campesinos con el Mundo | www.AgroCafé.com</p><p>Para consultas: agrocafe1129@gmail.com | +57 350-888-4148</p></div></body></html>';
        
        return $html; // Devolver el HTML como cadena
    }
}
?>