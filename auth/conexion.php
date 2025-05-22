<?php
// Datos de conexión
$servername = "localhost";
$username = "root";  // O el usuario que uses
$password = "102030";      // O la contraseña que uses
$dbname = "infotec"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar si la conexión es exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
