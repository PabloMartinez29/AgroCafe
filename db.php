<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'coffesycvcc';
$port = 3306; //Puerto por defecto de MySQL


// Crear Conexion
$conn = new mysqli($host, $user, $pass, $db, $port);

// Comprobar la conexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}