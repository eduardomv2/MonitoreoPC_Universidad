<?php
header("Content-Type: application/json");

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../utils/db.php';

//validar con authMiddleware
//require_once __DIR__ . '/../../middleware/auth.php';
//$usuario = authMiddleware();
 // Aquí se valida la API Key

$conn = conectarDB();

// Consulta para obtener solo los reportes con estatus = 1
$sql = "SELECT * FROM reportes WHERE estatus = 1";
$result = $conn->query($sql);

$reportes = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reportes[] = $row;
    }
    echo json_encode($reportes);
} else {
    echo json_encode(["mensaje" => "No se encontraron reportes activos"]);
}

$conn->close();
