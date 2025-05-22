<?php
require_once '../auth/conexion.php';
require_once '../helpers/correo_helper.php';

// Obtener correo enviado desde el formulario
$correo = $_POST['correo'] ?? '';

if (empty($correo)) {
    echo "Por favor, proporciona un correo.";
    exit;
}

// Preparar consulta para buscar usuario con ese correo
$sql = "SELECT * FROM estudiantes WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Usuario encontrado, generar token seguro único
    $token = bin2hex(random_bytes(32));

    // Guardar token y fecha de expiración en la base de datos
    $sqlUpdate = "UPDATE estudiantes SET token_recuperacion = ?, token_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE correo = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ss", $token, $correo);

    if ($stmtUpdate->execute()) {
        // Enviar correo con enlace de recuperación
        if (enviarEnlaceRecuperacion($correo, $token)) {
            echo "Correo de recuperación enviado correctamente.";
        } else {
            echo "Error al enviar el correo. Intenta nuevamente.";
        }
    } else {
        echo "Error al actualizar el token en la base de datos.";
    }

    $stmtUpdate->close();
} else {
    // No se encontró el correo
    echo "Correo no encontrado en el sistema.";
}

$stmt->close();
$conn->close();
?>
