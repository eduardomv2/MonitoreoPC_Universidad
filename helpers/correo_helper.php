<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// Configuración común de PHPMailer
function configurarMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'infotecmonclova@gmail.com';
    $mail->Password   = 'klye yoqp srek cixi'; // Usa tu App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom('infotecmonclova@gmail.com', 'Registro Universidad');
    return $mail;
}

// Función para enviar código de verificación
function enviarCodigoVerificacion($correoDestino, $codigo) {
    try {
        $mail = configurarMailer();
        $mail->addAddress($correoDestino);
        $mail->isHTML(true);
        $mail->Subject = 'Tu código de verificación';
        $mail->Body    = "<strong>Tu código de verificación es: $codigo</strong>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}

// Función para enviar enlace de recuperación de contraseña
function enviarEnlaceRecuperacion($correoDestino, $token) {
    try {
        $mail = configurarMailer();
        $mail->addAddress($correoDestino);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperar contraseña';

        // Construir enlace con ruta absoluta
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $basePath = dirname($_SERVER['PHP_SELF']);
        $enlace = "$protocolo://$host$basePath/reset_password.php?token=$token";

        // Cuerpo del mensaje
        $mensaje = "Hola, solicitaste recuperar tu contraseña.<br><br>";
        $mensaje .= "Haz clic en el siguiente enlace para restablecerla:<br><br>";
        $mensaje .= "<a href=\"$enlace\">$enlace</a><br><br>";
        $mensaje .= "Este enlace expirará en 1 hora.";

        $mail->Body = $mensaje;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>
