<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

echo "PHPMailer version: " . PHPMailer::VERSION . "\n";
echo "TCPDF instalado correctamente\n";

if (class_exists('TCPDF')) {
    echo "TCPDF está disponible\n";
} else {
    echo "Error: TCPDF no está disponible\n";
}
?>