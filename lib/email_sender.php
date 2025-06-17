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
                SELECT f.*, 
                       COALESCE(v.cantidad, comp.cantidad) as cantidad,
                       COALESCE(v.precio_kg, comp.precio_kg) as precio_kg,
                       COALESCE(v.total, comp.total, f.total) as total,
                       COALESCE(c.nombre, v.cliente_nombre, u.nombre) as cliente_nombre,
                       COALESCE(c.email, u.email, ?) as cliente_email,
                       tc.nombre as tipo_cafe, 
                       tc.variedad, 
                       f.tipo_transaccion
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
            
            // MEJORADO: Generar PDF con mejor manejo de errores
            $pdfPath = $this->generarPDFTemporal($factura);
            if ($pdfPath && file_exists($pdfPath)) {
                $this->mail->addAttachment($pdfPath, 'factura_' . $factura['numero_factura'] . '.pdf');
                error_log("PDF adjuntado exitosamente: $pdfPath");
            } else {
                error_log("ADVERTENCIA: No se pudo generar o encontrar el PDF para la factura ID $facturaId");
                // Continuar enviando el email sin adjunto
            }
            
            $result = $this->mail->send();
            error_log("Email enviado " . ($result ? "exitosamente" : "con errores") . " a $emailDestino para factura ID $facturaId");
            
            // Limpiar archivo temporal
            if ($pdfPath && file_exists($pdfPath)) {
                unlink($pdfPath);
                error_log("Archivo temporal eliminado: $pdfPath");
            }
            
            // Limpiar destinatarios para próximo uso
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error enviando email a $emailDestino (Factura ID $facturaId): " . $e->getMessage());
            error_log("PHPMailer ErrorInfo: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    private function generarCuerpoEmail($factura) {
        $tipoTransaccion = ucfirst($factura['tipo_transaccion']);
        $colorTema = $factura['tipo_transaccion'] === 'venta' ? '#8B4513' : '#228B22';
        $tipoCliente = $factura['tipo_transaccion'] === 'venta' ? 'Cooperativa' : 'Campesino';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: ' . $colorTema . '; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 10px 10px; }
                .highlight { background: #fff3cd; padding: 15px; border-left: 4px solid ' . $colorTema . '; margin: 15px 0; border-radius: 5px; }
                .tipo-badge { background: ' . $colorTema . '; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="header">
                    <h1>🌱 AgroCafé</h1>
                    <p>Factura Electrónica - ' . $tipoCliente . '</p>
                </div>
                
                <div class="content">
                    <h2>Estimado/a ' . htmlspecialchars($factura['cliente_nombre']) . ',</h2>
                    
                    <p>Nos complace enviarle la factura correspondiente a su ' . strtolower($tipoTransaccion) . ' de café:</p>
                    
                    <div class="highlight">
                        <h3 style="margin-top: 0; color: ' . $colorTema . ';">📄 Detalles de la Factura</h3>
                        <p><strong>Número:</strong> ' . $factura['numero_factura'] . ' <span class="tipo-badge">' . $tipoCliente . '</span></p>
                        <p><strong>Fecha:</strong> ' . date('d/m/Y', strtotime($factura['fecha_factura'])) . '</p>
                        <p><strong>Producto:</strong> ' . htmlspecialchars($factura['tipo_cafe']) . ' (' . ucfirst($factura['variedad']) . ')</p>
                        <p><strong>Cantidad:</strong> ' . number_format($factura['cantidad'], 2) . ' kg</p>
                        <p><strong>Total:</strong> <span style="font-size: 18px; color: ' . $colorTema . ';"><strong>$' . number_format($factura['total'] ?? 0, 0, ',', '.') . '</strong></span></p>
                    </div>
                    
                    ' . ($factura['tipo_transaccion'] === 'compra' ? 
                        '<p><strong>🌾 Nota para Campesinos:</strong> Esta factura corresponde a la compra de su café. Gracias por ser parte de nuestra red de productores y por contribuir con café de calidad.</p>' : 
                        '<p><strong>🏢 Nota para Cooperativas:</strong> Esta factura corresponde a su compra de café premium. Gracias por confiar en AgroCafé para sus necesidades de café.</p>') . '
                    
                    <p>📎 <strong>Adjunto encontrará la factura en formato PDF</strong> para sus registros contables y fiscales.</p>
                    
                    <p>Si tiene alguna pregunta sobre esta factura o necesita información adicional, no dude en contactarnos.</p>
                    
                    <p>Gracias por confiar en AgroCafé.</p>
                    
                    <p>Cordialmente,<br><strong>Equipo AgroCafé</strong></p>
                </div>
                
                <div class="footer">
                    <p><strong>🌱 AgroCafé - Conectando el Campo con el Mundo</strong></p>
                    <p>📧 agrocafe1129@gmail.com | 📱 +57 350 888 4148</p>
                    <p>🌐 www.agrocafe.com</p>
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 10px 0;">
                    <p><small>Este es un mensaje automático, por favor no responda directamente a este correo.</small></p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    private function generarPDFTemporal($factura) {
        require_once 'pdf_generator.php';
        
        // Limpiar cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        try {
            error_log("Iniciando generación de PDF temporal para factura ID: {$factura['id']}, tipo: {$factura['tipo_transaccion']}");
            
            $pdfContent = PDFGenerator::generarFactura($factura['id']);
            
            ob_end_clean(); // Limpiar buffer
            
            if ($pdfContent && strlen($pdfContent) > 0) {
                // Crear directorio temporal si no existe
                $tempDir = 'temp';
                if (!is_dir($tempDir)) {
                    if (!mkdir($tempDir, 0777, true)) {
                        error_log("No se pudo crear el directorio temporal: $tempDir");
                        return false;
                    }
                }
                
                $filename = $tempDir . '/factura_' . $factura['numero_factura'] . '_' . time() . '.pdf';
                
                if (file_put_contents($filename, $pdfContent)) {
                    error_log("PDF temporal creado exitosamente: $filename (tamaño: " . strlen($pdfContent) . " bytes)");
                    return $filename;
                } else {
                    error_log("Error al escribir el PDF en $filename");
                    return false;
                }
            } else {
                error_log("El contenido del PDF está vacío o es falso para la factura ID {$factura['id']}");
                return false;
            }
            
        } catch (Exception $e) {
            if (ob_get_level()) {
                ob_end_clean(); // Asegurar limpieza del buffer
            }
            error_log("Excepción al generar PDF temporal para factura ID {$factura['id']}: " . $e->getMessage());
            return false;
        }
    }
}
?>