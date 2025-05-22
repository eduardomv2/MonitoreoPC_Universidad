<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['correo_verificacion'])) {
    echo "⚠️ No hay una sesión activa de verificación.";
    exit;
}

$correo = $_SESSION['correo_verificacion'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoIngresado = trim($_POST['codigo'] ?? '');

    // Validar formato del código
    if (!preg_match('/^\d{6}$/', $codigoIngresado)) {
        $error = "El código debe tener exactamente 6 dígitos.";
    } else {
        // Buscar el correo en registro_temporal
        $sql = "SELECT * FROM registro_temporal WHERE correo_institucional = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $codigoGuardado = $fila['codigo_verificacion'];

            if (trim($codigoIngresado) == trim((string)$codigoGuardado)) {
                // Mover el registro a la tabla estudiantes
               $insert = $conn->prepare("
    INSERT INTO estudiantes 
    (nombres, apellido_paterno, apellido_materno, semestre, matricula, id_carrera, correo, contrasena, fecha_nacimiento, id_genero, id_rol, activo)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$activo = 1;
echo "<pre>";
print_r($fila);
echo "</pre>";


$insert->bind_param(
    'sssisisssiii',
    $fila['nombre'],              // nombres
    $fila['apellido_paterno'],
    $fila['apellido_materno'],
    $fila['semestre'],
    $fila['matricula'],
    $fila['carrera_id'],          // id_carrera
    $fila['correo_institucional'], // correo
    $fila['contrasena'],
    $fila['fecha_nacimiento'],
    $fila['genero_id'],           // id_genero
    $fila['rol_id'],              // id_rol
    $activo                       // activo (tinyint)
);

                

                if ($insert->execute()) {
                    // Eliminar de registro_temporal
                    $delete = $conn->prepare("DELETE FROM registro_temporal WHERE correo_institucional = ?");
                    $delete->bind_param("s", $correo);
                    $delete->execute();
                    $delete->close();

                    unset($_SESSION['correo_verificacion']);
                    header('Location: ../views/login_view.php');
                    exit;

                } else {
                    $error = "❌ Error al guardar los datos definitivos: " . $insert->error;
                }

                $insert->close();
            } else {
                $error = "❌ Código incorrecto. Intenta nuevamente.";
            }
        } else {
            $error = "⚠️ No se encontró tu registro temporal. Por favor regístrate de nuevo.";
            unset($_SESSION['correo_verificacion']);
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de correo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        input[type="text"] {
            padding: 10px;
            width: 80%;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        p.error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verifica tu correo institucional</h2>
        <p>Hemos enviado un código de 6 dígitos a tu correo institucional. Ingrésalo aquí:</p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="text" name="codigo" placeholder="Código de 6 dígitos" pattern="\d{6}" required>
            <br>
            <button type="submit">Verificar</button>
        </form>
    </div>
</body>
</html>
