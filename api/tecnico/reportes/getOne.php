<?php
require_once __DIR__ . '/../../utils/db.php';


//validar con authMiddleware
require_once __DIR__ . '/../../middleware/auth.php';
$usuario = authMiddleware();

// Obtener el parámetro 'id' desde la URL (GET)
$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID requerido']);
    exit;
}

// Conectar a la base de datos con MySQLi
$mysqli = conectarDB();

// Preparar la consulta con statement preparado
$stmt = $mysqli->prepare("SELECT * FROM reportes WHERE id_reporte = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la preparación de la consulta']);
    exit;
}

$stmt->bind_param("i", $id); // 'i' porque es un entero

$stmt->execute();

$result = $stmt->get_result();

$reporte = $result->fetch_assoc();

if ($reporte) {
    echo json_encode($reporte);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Reporte no encontrado']);
}

$stmt->close();
$mysqli->close();
?>
