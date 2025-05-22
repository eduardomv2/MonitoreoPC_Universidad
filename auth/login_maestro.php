<?php
session_start();
require_once __DIR__ . '/../auth/conexion.php';

if (isset($_SESSION['maestro_id'])) {
    header("Location: ../views/login_view_maestro.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['password'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_maestro, contrasena, estatus FROM maestros WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $maestro = $result->fetch_assoc();

        // Verificar si el maestro está activo
        if ($maestro['estatus'] == 0) {
            header("Location: ../views/login_maestro.php?error=Tu cuenta está inactiva.");
            exit();
        }

        // Verificar contraseña sin hash (inseguro para producción)
        if ($password == $maestro['contrasena']) {
            $_SESSION['maestro_id'] = $maestro['id_maestro'];
            header("Location: ../views/maestro_view.php");
            exit();
        } else {
            header("Location: ../views/login_maestro.php?error=Contraseña incorrecta.");
            exit();
        }
    } else {
        header("Location: ../views/login_maestro.php?error=Usuario no encontrado.");
        exit();
    }
} else {
    header("Location: ../views/login_maestro.php?error=Campos incompletos.");
    exit();
}
?>
