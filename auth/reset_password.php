<?php
require_once '../auth/conexion.php';

// Obtener token de la URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo "Token no proporcionado.";
    exit;
}

// Buscar si el token es válido y no ha expirado
$stmt = $conn->prepare("SELECT id_usuario FROM estudiantes WHERE token_recuperacion = ? AND token_expira > NOW()");

$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1):
    $usuario = $resultado->fetch_assoc(); // por si necesitas el ID luego
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Restablecer contraseña</title>
    </head>
    <body>
        <h2>Restablecer contraseña</h2>
        <form action="../auth/process_reset.php" method="POST">
            <!-- Token oculto para identificar al usuario -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <label for="password">Nueva contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Cambiar contraseña</button>
        </form>
    </body>
    </html>
<?php else: ?>
    <p>El enlace es inválido o ya expiró. Solicita uno nuevo.</p>
<?php
endif;

$stmt->close();
$conn->close();
?>
