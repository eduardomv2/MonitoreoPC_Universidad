<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';

//validar con authMiddleware
require_once __DIR__ . '/../../middleware/auth.php';
$usuario = authMiddleware();


$conexion = conectarDB(); // 💡 Aquí es donde defines tu conexión

$sql = "
    SELECT 
        CONCAT(est.nombres, ' ', est.apellido_paterno, ' ', est.apellido_materno) AS nombre_completo,
        car.nombre AS nombre_carrera,
        est.semestre,
        est.foto_perfil,
        est.celular
    FROM estudiantes est
    INNER JOIN carreras car ON est.id_carrera = car.id_carrera
    WHERE est.activo = 1 AND est.visible = 1
";



$resultado = $conexion->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener los datos"]);
    exit;
}

$tecnicos = [];

while ($fila = $resultado->fetch_assoc()) {
    $tecnicos[] = $fila;
}

echo json_encode($tecnicos);
