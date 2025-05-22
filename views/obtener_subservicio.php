<?php
require_once __DIR__ . '/../auth/conexion.php';

header('Content-Type: application/json');

if (!isset($_GET['id_servicio'])) {
    echo json_encode([]);
    exit;
}

$id_servicio = intval($_GET['id_servicio']);

$stmt = $conn->prepare("SELECT id_subservicio, descripcion FROM subservicios WHERE id_servicio = ? ORDER BY descripcion");
$stmt->bind_param("i", $id_servicio);
$stmt->execute();
$result = $stmt->get_result();

$subservicios = [];
while ($row = $result->fetch_assoc()) {
    $subservicios[] = ['id_subservicio' => $row['id_subservicio'], 'nombre' => $row['descripcion']];
}

echo json_encode($subservicios);
?>
