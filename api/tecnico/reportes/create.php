<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Solo en desarrollo
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../utils/db.php';
$conn = conectarDB();

//validar con authMiddleware
require_once __DIR__ . '/../../middleware/auth.php';
$usuario = authMiddleware();


$input = json_decode(file_get_contents("php://input"), true);

// Validación de campos requeridos (sin no_control porque se genera)
$requiredFields = [
    'matricula_tecnico', 'matricula_cliente',
    'id_carrera', 'id_genero', 'id_equipo', 'id_servicio',
    'id_subservicio'
];

foreach ($requiredFields as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Falta el campo requerido: $field"]);
        exit;
    }
}

// Función para generar un código único estilo RPT-20240520-ABC123
function generarCodigoUnico($conn) {
    do {
        $codigo = 'RPT-' . date('Ymd') . '-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        $check = $conn->prepare("SELECT 1 FROM reportes WHERE no_control = ?");
        $check->bind_param("s", $codigo);
        $check->execute();
        $check->store_result();
        $existe = $check->num_rows > 0;
        $check->close();
    } while ($existe);
    return $codigo;
}

// Escapar y preparar variables
$matricula_tecnico = $conn->real_escape_string($input['matricula_tecnico']);
$matricula_cliente = $conn->real_escape_string($input['matricula_cliente']);
$id_carrera = (int)$input['id_carrera'];
$semestre = isset($input['semestre']) ? $conn->real_escape_string($input['semestre']) : null;
$id_genero = (int)$input['id_genero'];
$id_equipo = (int)$input['id_equipo'];
$id_servicio = (int)$input['id_servicio'];
$id_subservicio = (int)$input['id_subservicio'];
$estatus = 1; // Estatus en 1 por defecto
$no_control = generarCodigoUnico($conn);

// Insertar el nuevo reporte
$stmt = $conn->prepare("
    INSERT INTO reportes (
        no_control, matricula_tecnico, matricula_cliente, id_carrera,
        semestre, id_genero, id_equipo, id_servicio, id_subservicio,
        estatus
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssissiiii",
    $no_control, $matricula_tecnico, $matricula_cliente, $id_carrera,
    $semestre, $id_genero, $id_equipo, $id_servicio, $id_subservicio,
    $estatus
);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        "mensaje" => "Reporte creado exitosamente",
        "id_reporte" => $stmt->insert_id,
        "no_control" => $no_control
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error al insertar el reporte",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
