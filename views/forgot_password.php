<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Recuperar contraseña</title>
</head>
<body>
    <h1>Recuperar contraseña</h1>
    <form action="../auth/send_reset_link.php" method="POST">
        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" id="correo" required />
        <button type="submit">Enviar enlace</button>
    </form>
</body>
</html>
