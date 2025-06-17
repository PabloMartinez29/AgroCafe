<?php
// 🔧 ARCHIVO EN LA RAÍZ: Agrocafe1/send_email_ajax.php
require_once 'config/database.php';
require_once 'lib/email_sender.php';

// Configurar headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    $facturaId = intval($_POST['factura_id']);
    $email = trim($_POST['email']);
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'La dirección de correo electrónico no es válida: ' . htmlspecialchars($email)
        ]);
        exit;
    }
    
    // Validar factura ID
    if (empty($facturaId)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de factura no válido'
        ]);
        exit;
    }
    
    // Enviar email
    $emailSender = new EmailSender();
    $resultado = $emailSender->enviarFactura($facturaId, $email);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Factura enviada por email exitosamente a: ' . htmlspecialchars($email)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar la factura por email. Revisa los logs para más detalles.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error en send_email_ajax.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
