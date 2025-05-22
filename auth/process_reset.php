<?php
require_once '../auth/conexion.php'; // Conexión a la base de datos

// Obtener datos del formulario
$token = $_POST['token'] ?? '';
$passwordNueva = $_POST['password'] ?? '';
$passwordConfirm = $_POST['confirm_password'] ?? '';

// Validaciones básicas
if (empty($token) || empty($passwordNueva) || empty($passwordConfirm)) {
    echo "Faltan datos. Asegúrate de llenar todos los campos.";
    exit;
}

if ($passwordNueva !== $passwordConfirm) {
    echo "Las contraseñas no coinciden.";
    exit;
}

// Buscar al estudiante con el token y verificar si sigue vigente
$stmt = $conn->prepare("SELECT id_usuario FROM estudiantes WHERE token_recuperacion = ? AND token_expira > NOW()");

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Usuario encontrado y token válido
    $estudiante = $result->fetch_assoc();
    $idEstudiante = $estudiante['id_usuario'];


    // Hashear la nueva contraseña
    $hashPassword = password_hash($passwordNueva, PASSWORD_DEFAULT);

    // Actualizar contraseña y eliminar token
   $stmtUpdate = $conn->prepare("UPDATE estudiantes SET contrasena = ?, token_recuperacion = NULL, token_expira = NULL WHERE id_usuario = ?");

    $stmtUpdate->bind_param("si", $hashPassword, $idEstudiante);

    if ($stmtUpdate->execute()) {
        echo "Contraseña actualizada correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.";
    } else {
        echo "Error al guardar la nueva contraseña. Inténtalo más tarde.";
    }

    $stmtUpdate->close();
} else {
    echo "El enlace es inválido o ya expiró. Solicita uno nuevo.";
}

$stmt->close();
$conn->close();
?>
