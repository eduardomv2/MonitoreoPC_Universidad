<?php
session_start();
require_once __DIR__ . '/../auth/conexion.php';
require_once __DIR__ . '/../auth/curl_headers.php'; // Importa la función para headers

// Obtener matrícula del técnico desde sesión
$matricula_tecnico = $_SESSION['matricula'] ?? null;
if (!$matricula_tecnico) {
    die("❌ Usuario no autenticado o matrícula no disponible.");
}

// Recibir datos del formulario
$nombre_completo   = $_POST['nombre_completo']    ?? '';
$numero_celular    = $_POST['numero_celular']     ?? '';
$matricula_cliente = $_POST['matricula_cliente']  ?? '';
$id_carrera        = intval($_POST['id_carrera']  ?? 0);
$semestre          = $_POST['semestre']           ?? '';  
$id_genero         = intval($_POST['id_genero']   ?? 0);
$id_equipo         = intval($_POST['id_equipo']   ?? 0);
$id_servicio       = intval($_POST['id_servicio'] ?? 0);
$id_subservicio    = intval($_POST['id_subservicio'] ?? 0);

// Validación básica
if (
    !$nombre_completo || !$numero_celular || !$matricula_cliente ||
    !$id_carrera || !$semestre || !$id_genero || !$id_equipo || !$id_servicio || !$id_subservicio
) {
    die("❌ Faltan datos obligatorios.");
}

// Preparar datos para enviar a la API
$data = [
    'matricula_tecnico' => $matricula_tecnico,
    'matricula_cliente' => $matricula_cliente,
    'id_carrera' => $id_carrera,
    'semestre' => $semestre,
    'id_genero' => $id_genero,
    'id_equipo' => $id_equipo,
    'id_servicio' => $id_servicio,
    'id_subservicio' => $id_subservicio
];

// Convertir a JSON
$json_data = json_encode($data);

// URL del endpoint API
$url_api = "http://localhost/MonitoreoPC_Universidad/api/tecnico/reportes/create.php";

// Inicializar cURL
$ch = curl_init($url_api);
if ($ch === false) {
    die("❌ No se pudo inicializar cURL.");
}

// Configurar opciones cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

// Usar función para obtener headers (aquí la magia)
$headers = getCurlHeaders($json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Ejecutar petición
$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("❌ Error en llamada a API: " . curl_error($ch));
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 201) {
    echo "<h2>✅ Reporte guardado correctamente</h2>";
} else {
    $resp_data = json_decode($response, true);
    $msg_error = $resp_data['mensaje'] ?? 'Error desconocido';
    die("❌ Error al guardar reporte: $msg_error");
}
