<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';
$conn = conectarDB();

// Obtener matrícula cliente desde query param ?matricula=...
$matricula = $_GET['matricula'] ?? null;

if (!$matricula) {
    http_response_code(400);
    echo json_encode(["error" => "Se requiere el parámetro matricula"]);
    exit;
}

$sql = "SELECT * FROM reportes WHERE matricula_cliente = ? AND estatus = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

$reportes = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reportes[] = $row;
    }
    echo json_encode($reportes);
} else {
    echo json_encode(["mensaje" => "No se encontraron reportes para la matrícula cliente especificada"]);
}

$stmt->close();
$conn->close();
