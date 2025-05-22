<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    
    header('Content-Type: text/html; charset=utf-8');

    
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Acceso denegado</title>
        <meta http-equiv="refresh" content="3;url=../views/login_view.php">
        <style>
            body { font-family: Arial, sans-serif; background: #f8f8f8; color: #333; text-align: center; padding: 50px;}
            h2 { color: #c00; }
        </style>
    </head>
    <body>
        <h2>Acceso denegado. Debes iniciar sesión para acceder a esta página.</h2>
        <p>Serás redirigido al login en 3 segundos...</p>
    </body>
    </html>';
    exit();
}
?>
