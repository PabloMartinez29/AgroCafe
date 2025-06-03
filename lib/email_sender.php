<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

class EmailSender {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'agrocafe1129@gmail.com';
            $this->mail->Password = 'zznj zudn xzol favh';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            $this->mail->setFrom('agrocafe1129@gmail.com', 'CaféTrade');
            $this->mail->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Error configurando email: {$this->mail->ErrorInfo}");
            throw new Exception("Error configurando email: {$this->mail->ErrorInfo}");
        }
    }
    
    public function enviarFactura($facturaId, $emailDestino) {
        try {
            $factura = fetchOne("
                SELECT f.*, v.cantidad, v.precio_kg, v.total as venta_total,
                       COALESCE(c.nombre, v.cliente_nombre, u.nombre) as cliente_nombre,
                       COALESCE(c.email, u.email, ?) as cliente_email,
                       tc.nombre as tipo_cafe, tc.variedad, f.tipo_transaccion
                FROM facturas f
                LEFT JOIN ventas v ON f.venta_id = v.id
                LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
                LEFT JOIN compras comp ON f.compra_id = comp.id
                LEFT JOIN usuarios u ON comp.campesino_id = u.id
                JOIN tipos_cafe tc ON COALESCE(v.tipo_cafe_id, comp.tipo_cafe_id) = tc.id
                WHERE f.id = ?", [$emailDestino, $facturaId]);
            
            if (!$factura) {
                error_log("Factura no encontrada: ID $facturaId");
                return false;
            }   
            
            $this->mail->addAddress($emailDestino, $factura['cliente_nombre']);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Factura ' . $factura['numero_factura'] . ' - AgroCafé';
            
            $body = $this->generarCuerpoEmail($factura);
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $pdfPath = $this->generarPDFTemporal($factura);
            if ($pdfPath) {
                $this->mail->addAttachment($pdfPath, 'factura_' . $factura['numero_factura'] . '.pdf');
            } else {
                error_log("No se pudo generar el PDF para la factura ID $facturaId");
            }
            
            $result = $this->mail->send();
            if ($pdfPath && file_exists($pdfPath)) unlink($pdfPath);
            return $result;
        } catch (Exception $e) {
            error_log("Error enviando email a $emailDestino (Factura ID $facturaId): {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    private function generarCuerpoEmail($factura) {
        $tipoTransaccion = ucfirst($factura['tipo_transaccion']);
        return '
        <!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;}.header{background:#8B4513;color:white;padding:20px;text-align:center;}.content{padding:20px;}.footer{background:#f8f9fa;padding:15px;text-align:center;font-size:12px;color:#666;}.highlight{background:#fff3cd;padding:10px;border-left:4px solid #ffc107;margin:15px 0;}</style></head><body><div class="header"><h1>AgroCafe</h1><p>Factura Electrónica</p></div><div class="content"><h2>Estimado/a ' . htmlspecialchars($factura['cliente_nombre']) . ',</h2><p>Nos complace enviarle la factura correspondiente a su ' . $tipoTransaccion . ' de café:</p><div class="highlight"><strong>Factura:</strong> ' . $factura['numero_factura'] . '<br><strong>Fecha:</strong> ' . date('d/m/Y', strtotime($factura['fecha_factura'])) . '<br><strong>Producto:</strong> ' . htmlspecialchars($factura['tipo_cafe']) . ' (' . ucfirst($factura['variedad']) . ')<br><strong>Cantidad:</strong> ' . number_format($factura['tipo_transaccion'] === 'venta' ? $factura['cantidad'] : $factura['cantidad'], 2) . ' kg<br><strong>Total:</strong> $' . number_format($factura['total'], 0, ',', '.') . '</div><p>Adjunto encontrará la factura en formato PDF para sus registros.</p><p>Si tiene alguna pregunta, no dude en contactarnos.</p><p>Gracias por confiar en AgroCafé.</p><p>Cordialmente,<br><strong>Equipo AgroCafé</strong></p></div><div class="footer"><p>AgroCafé - Sistema de Gestión de Café<br>Email: agrocafe1129@gmail.com | Teléfono: +57 350 888 4148</p></div></body></html>';
    }
    
    private function generarPDFTemporal($factura) {
        require_once 'pdf_generator.php';
        ob_start();
        if ($factura['tipo_transaccion'] === 'venta') {
            $pdfContent = PDFGenerator::generarFactura($factura['id']);
        } else {
            $compra = fetchOne("SELECT compra_id FROM facturas WHERE id = ?", [$factura['id']]);
            if ($compra) {
                $pdfContent = PDFGenerator::generarFacturaCampesino($compra['compra_id']);
            } else {
                $pdfContent = false;
            }
        }
        ob_end_clean(); 
        if ($pdfContent) {
            $filename = 'temp/factura_' . $factura['numero_factura'] . '_' . time() . '.pdf';
            if (!is_dir('temp')) mkdir('temp', 0777, true);
            if (file_put_contents($filename, $pdfContent)) {
                return $filename;
            } else {
                error_log("Error al escribir el PDF en $filename");
                return false;
            }
        }
        error_log("No se pudo generar el contenido del PDF para la factura ID {$factura['id']}");
        return false;
    }
}
?>