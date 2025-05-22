<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';
$conn = conectarDB();

$sql = "
    SELECT 
        s.id_servicio, s.descripcion AS nombre_servicio, 
        ss.id_subservicio, ss.descripcion AS nombre_subservicio
    FROM servicios s
    LEFT JOIN subservicios ss ON s.id_servicio = ss.id_servicio
    ORDER BY s.id_servicio, ss.id_subservicio
";

$result = $conn->query($sql);

$servicios = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $idServicio = $row['id_servicio'];

        if (!isset($servicios[$idServicio])) {
            $servicios[$idServicio] = [
                "id_servicio" => $idServicio,
                "nombre_servicio" => $row['nombre_servicio'],
                "subservicios" => []
            ];
        }

        if ($row['id_subservicio'] !== null) {
            $servicios[$idServicio]['subservicios'][] = [
                "id_subservicio" => $row['id_subservicio'],
                "nombre_subservicio" => $row['nombre_subservicio']
            ];
        }
    }

    $servicios = array_values($servicios);

    echo json_encode($servicios);
} else {
    echo json_encode(["mensaje" => "No se encontraron servicios"]);
}

$conn->close();
