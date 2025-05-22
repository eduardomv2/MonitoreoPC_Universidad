<?php
require_once __DIR__ . '/../utils/db.php';

function registrarAuditoria($usuarioId, $endpoint, $metodo) {
    $conn = conectarDB();
    $stmt = $conn->prepare("INSERT INTO auditoria_api (usuario_id, endpoint, metodo_http) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuarioId, $endpoint, $metodo);
    $stmt->execute();
    $stmt->close();
}

function authMiddleware() {
    $headers = getallheaders();

    if (!isset($headers['X-API-KEY'])) {
        http_response_code(401);
        echo json_encode(['error' => 'API Key no proporcionada']);
        exit;
    }

    $apiKey = $headers['X-API-KEY'];
    $conn = conectarDB();

    $stmt = $conn->prepare("SELECT * FROM usuarios_api WHERE api_key = ? AND estatus = 1");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'API Key inválida']);
        exit;
    }

    $usuario = $result->fetch_assoc();

    // Reset diario de peticiones
    $hoy = date('Y-m-d');
    if ($usuario['fecha_reset'] != $hoy) {
        $stmt = $conn->prepare("UPDATE usuarios_api SET peticiones_restantes = 3000, fecha_reset = ? WHERE id = ?");
        $stmt->bind_param("si", $hoy, $usuario['id']);
        $stmt->execute();
        $usuario['peticiones_restantes'] = 3000;
    }

    if ($usuario['peticiones_restantes'] <= 0) {
        http_response_code(429); // Too Many Requests
        echo json_encode(['error' => 'Límite diario de peticiones alcanzado']);
        exit;
    }

    // Restar una petición
    $stmt = $conn->prepare("UPDATE usuarios_api SET peticiones_restantes = peticiones_restantes - 1 WHERE id = ?");
    $stmt->bind_param("i", $usuario['id']);
    $stmt->execute();

    // Registrar auditoría
    $endpoint = $_SERVER['REQUEST_URI'];  // Ruta que se pidió
    $metodo = $_SERVER['REQUEST_METHOD']; // GET, POST, etc.
    registrarAuditoria($usuario['id'], $endpoint, $metodo);

    return $usuario;
}
