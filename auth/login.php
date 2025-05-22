<?php
session_start();

require_once __DIR__ . '/../auth/conexion.php';
$error = null;

// Verificar si el usuario ya está logueado
if (isset($_SESSION['id_usuario']) && isset($_SESSION['id_rol'])) {
    if ($_SESSION['id_rol'] == 1) {
        header("Location: ../views/tecnico_view.php");
    } elseif ($_SESSION['id_rol'] == 2) {
        header("Location: ../views/cliente_view.php");
    } else {
        header("Location: ../views/login_view.php"); 
    }
    exit();
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'], $_POST['password'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Preparar consulta para obtener datos del usuario
    $stmt = $conn->prepare("SELECT id_usuario, matricula, nombres, apellido_paterno, apellido_materno, contrasena, id_rol FROM estudiantes WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $estudiante = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $estudiante['contrasena'])) {

            
            $_SESSION['id_usuario'] = $estudiante['id_usuario']; // Lo que tengas como ID en la BD

            $_SESSION['matricula'] = $estudiante['matricula'];
            $_SESSION['usuario_nombre'] = $estudiante['nombres'] . " " . $estudiante['apellido_paterno'] . " " . $estudiante['apellido_materno'];
            $_SESSION['id_rol'] = $estudiante['id_rol'];

            // Redirigir según rol
            if ($estudiante['id_rol'] == 1) {
                header("Location: ../views/tecnico_view.php");
            } elseif ($estudiante['id_rol'] == 2) {
                header("Location: ../views/cliente_view.php");
            } else {
                header("Location: ../views/tecnico_view.php");
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Correo no encontrado.";
    }

    $stmt->close();
}

// Si necesitas cargar datos para el formulario, por ejemplo carreras:
$carreras = [];
$result_carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre");
if ($result_carreras) {
    while ($row = $result_carreras->fetch_assoc()) {
        $carreras[] = $row;
    }
}
?>
