<?php
session_start();
require_once 'conexion.php';
require_once __DIR__ . '/../helpers/correo_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y obtener los datos
    $nombre = trim($_POST['nombre'] ?? '');
    $paterno = trim($_POST['apellido_paterno'] ?? '');
    $materno = trim($_POST['apellido_materno'] ?? '');
    $semestre = intval($_POST['semestre'] ?? 0);
    $matricula = trim($_POST['matricula'] ?? ''); 
    $carrera = intval($_POST['carrera_id'] ?? 0);
    $correo = trim($_POST['correo'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmarPassword = $_POST['confirmar_password'] ?? '';
    $genero_id = intval($_POST['genero_id'] ?? 0);
    $rol_id = intval($_POST['rol_id'] ?? 0);

    // Validación básica
    if (
        empty($nombre) || empty($paterno) || empty($matricula) ||
        empty($correo) || empty($password) || empty($confirmarPassword)
    ) {
        echo '⚠️ Todos los campos son obligatorios.';
        exit;
    }

    if ($password !== $confirmarPassword) {
        echo "❌ Las contraseñas no coinciden. Intenta nuevamente.";
        exit;
    }

    if (!preg_match('/@monclova\.tecnm\.mx$/', $correo)) {
        echo "⚠️ El correo debe ser del dominio @monclova.tecnm.mx";
        exit;
    }

    // Verificar si ya existe en estudiantes
    $verificar = $conn->prepare("SELECT id_usuario FROM estudiantes WHERE correo = ? OR matricula = ?");
    $verificar->bind_param("ss", $correo, $matricula);
    $verificar->execute();
    $verificar->store_result();
    if ($verificar->num_rows > 0) {
        echo "⚠️ Ya existe un estudiante registrado con esa matrícula o correo.";
        $verificar->close();
        exit;
    }
    $verificar->close();

    // Verificar si ya existe en registro_temporal
    $verificarTemporal = $conn->prepare("SELECT id FROM registro_temporal WHERE correo_institucional = ? OR matricula = ?");
    $verificarTemporal->bind_param("ss", $correo, $matricula);
    $verificarTemporal->execute();
    $verificarTemporal->store_result();
    if ($verificarTemporal->num_rows > 0) {
        $_SESSION['correo_verificacion'] = $correo;
        header('Location: verify.php');
        exit;
    }
    $verificarTemporal->close();

    // Encriptar contraseña
    $pass = password_hash($password, PASSWORD_DEFAULT);

    // Generar código de verificación y fecha de expiración
    $codigo = rand(100000, 999999);
    date_default_timezone_set('America/Monterrey'); // Ajusta a tu zona horaria
$expiraEn = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Fecha de expiración a 5 minutos a partir de ahora

    // Insertar en registro_temporal
    $sql = "INSERT INTO registro_temporal 
    (nombre, apellido_paterno, apellido_materno, matricula, semestre, correo_institucional, contrasena, estatus, carrera_id, genero_id, rol_id, fecha_nacimiento, codigo_verificacion, expira_en)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssissiiisss', // tipos: s = string, i = integer
        $nombre, $paterno, $materno, $matricula, $semestre, $correo,
        $pass, $carrera, $genero_id, $rol_id, $fecha_nacimiento, $codigo, $expiraEn
    );

    if ($stmt->execute()) {
        $_SESSION['correo_verificacion'] = $correo;
        $resultadoCorreo = enviarCodigoVerificacion($correo, $codigo);

        if ($resultadoCorreo === true) {
            header('Location: verify.php');
            exit;
        } else {
            echo "⚠️ Registro temporal exitoso, pero falló el envío del correo: $resultadoCorreo";
        }
    } else {
        echo '❌ Error en la base de datos: ' . $stmt->error;
    }

    $stmt->close();
}
?>
