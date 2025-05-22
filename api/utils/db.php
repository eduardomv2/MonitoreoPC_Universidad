<?php
// utils/db.php
function conectarDB() {
    $host = "localhost";
    $usuario = "root";
    $pass = "102030";
    $bd = "infotec";

    $mysqli = new mysqli($host, $usuario, $pass, $bd);
    if ($mysqli->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Error al conectar a la base de datos"]);
        exit;
    }
    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}
