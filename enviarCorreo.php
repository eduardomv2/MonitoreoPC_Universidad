<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'infotecmonclova@gmail.com'; // Tu correo de Gmail
    $mail->Password   = 'klye yoqp srek cixi'; // Contraseña de la aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('infotecmonclova@gmail.com', 'Nombre del remitente');
    $mail->addAddress('destinatario@monclova.tecnm.mx', 'Nombre del estudiante');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Código de verificación';
    $mail->Body    = 'Tu código de verificación es: <strong>123456</strong>';
    $mail->AltBody = 'Tu código de verificación es: 123456';

    // Enviar correo
    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar el correo: {$mail->ErrorInfo}";
}

