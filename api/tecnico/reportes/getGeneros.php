<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';
$conn = conectarDB();

// Contar reportes activos por género
$sql = "SELECT id_genero, COUNT(*) AS total_reportes FROM reportes WHERE estatus = 1 GROUP BY id_genero";
$result = $conn->query($sql);

$generos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $generos[] = $row;
    }
    echo json_encode($generos);
} else {
    echo json_encode(["mensaje" => "No se encontraron datos"]);
}

$conn->close();
