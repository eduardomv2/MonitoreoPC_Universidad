<?php
require_once __DIR__ . '/../../utils/db.php';

header('Content-Type: application/json');

// Conexión a la base de datos
$mysqli = conectarDB();

// Leer el JSON del cuerpo de la petición
$data = json_decode(file_get_contents("php://input"), true);

// ID obligatorio
$id = $data['id_reporte'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID del reporte requerido']);
    exit;
}

// Lista de campos que se pueden actualizar
$camposPermitidos = [
    'no_control',
    'matricula_tecnico',
    'matricula_cliente',
    'id_carrera',
    'semestre',
    'id_genero',
    'id_equipo',
    'id_servicio',
    'id_subservicio',
    'ruta_evidencia',
    'estatus'
];

// Preparar la consulta dinámica
$campos = [];
$valores = [];

foreach ($camposPermitidos as $campo) {
    if (array_key_exists($campo, $data)) {
        $campos[] = "$campo = ?";
        $valores[] = $data[$campo];
    }
}

if (empty($campos)) {
    http_response_code(400);
    echo json_encode(['error' => 'No hay campos para actualizar']);
    exit;
}

// Agregamos el ID al final de los valores
$valores[] = $id;

// Construcción y ejecución del UPDATE
$sql = "UPDATE reportes SET " . implode(', ', $campos) . " WHERE id_reporte = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt && $stmt->execute($valores)) {
    echo json_encode(['mensaje' => 'Reporte actualizado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar el reporte']);
}
