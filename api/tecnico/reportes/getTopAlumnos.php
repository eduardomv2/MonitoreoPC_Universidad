<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../utils/db.php';
$conn = conectarDB();

// Paso 1: obtener las 3 matriculas de técnicos con más reportes activos
$sqlTopTecnicos = "
    SELECT matricula_tecnico, COUNT(*) AS total_reportes 
    FROM reportes 
    WHERE estatus = 1 
    GROUP BY matricula_tecnico 
    ORDER BY total_reportes DESC 
    LIMIT 3";

$resultTopTecnicos = $conn->query($sqlTopTecnicos);

$topTecnicos = [];

if ($resultTopTecnicos && $resultTopTecnicos->num_rows > 0) {
    // Recorrer cada técnico top
    while ($tecnico = $resultTopTecnicos->fetch_assoc()) {
        $matricula = $tecnico['matricula_tecnico'];

        // Paso 2: obtener servicios y subservicios realizados por este técnico
        $sqlServicios = "
            SELECT id_servicio, id_subservicio, COUNT(*) AS cantidad 
            FROM reportes 
            WHERE estatus = 1 AND matricula_tecnico = ? 
            GROUP BY id_servicio, id_subservicio
            ORDER BY cantidad DESC";

        $stmt = $conn->prepare($sqlServicios);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $resultServicios = $stmt->get_result();

        $servicios = [];
        while ($servicio = $resultServicios->fetch_assoc()) {
            $servicios[] = $servicio;
        }
        $stmt->close();

        // Construir el resultado para este técnico
        $topTecnicos[] = [
            "matricula_tecnico" => $matricula,
            "total_reportes" => (int)$tecnico['total_reportes'],
            "servicios" => $servicios
        ];
    }

    echo json_encode($topTecnicos);

} else {
    echo json_encode(["mensaje" => "No se encontraron técnicos"]);
}

$conn->close();
