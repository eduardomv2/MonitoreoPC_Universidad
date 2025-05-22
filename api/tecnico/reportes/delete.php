<?php
require_once __DIR__ . '/../../utils/db.php';

// Obtener ID del reporte por método GET
$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400); // Código 400: Bad Request
    echo json_encode(['error' => 'ID requerido']);
    exit;
}

$mysqli = conectarDB(); // Conexión a la base de datos

// Preparar la consulta para actualizar el estado
$stmt = $mysqli->prepare("UPDATE reportes SET estatus = 0 WHERE id_reporte = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al preparar la consulta']);
    exit;
}

// Vincular parámetros
$stmt->bind_param("i", $id);

// Ejecutar consulta
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['mensaje' => 'Reporte eliminado (estatus = 0)']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Reporte no encontrado o ya estaba eliminado']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al ejecutar la consulta']);
}

$stmt->close();
$mysqli->close();
?>
