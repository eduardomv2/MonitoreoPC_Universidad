<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';
$conn = conectarDB();

// Contar reportes activos por semestre
$sql = "SELECT semestre, COUNT(*) AS total_reportes FROM reportes WHERE estatus = 1 GROUP BY semestre";
$result = $conn->query($sql);

$semestres = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $semestres[] = $row;
    }
    echo json_encode($semestres);
} else {
    echo json_encode(["mensaje" => "No se encontraron datos"]);
}

$conn->close();
