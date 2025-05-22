<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';

require_once __DIR__ . '/../../middleware/auth.php';
$usuario = authMiddleware();

$conn = conectarDB();

$sql = "SELECT id_carrera, COUNT(*) AS total_reportes FROM reportes WHERE estatus = 1 GROUP BY id_carrera";

$result = $conn->query($sql);

$stats = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stats[] = $row;
    }
    echo json_encode($stats);
} else {
    echo json_encode(["mensaje" => "No se encontraron datos"]);
}

$conn->close();
