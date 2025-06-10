<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function enviar_codigo_verificacion($email_destino)
{
    $codigo = random_int(100000, 999999); // código seguro
    $_SESSION["2fa_code"] = $codigo;
    $_SESSION["2fa_time"] = time();

    $mail = new PHPMailer(true);

    try {
        // CONFIGURACIÓN SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.tuservidor.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'usuario@tuservidor.com';
        $mail->Password   = 'tu-contraseña';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // o PHPMailer::ENCRYPTION_SMTPS
        $mail->Port       = 587; // o 465 si usas SMTPS

        // REMITENTE Y DESTINATARIO
        $mail->setFrom('no-reply@enclave.com', 'ENCLAVE');
        $mail->addAddress($email_destino);

        // CONTENIDO DEL CORREO
        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación ENCLAVE';
        $mail->Body    = "<h3>Tu código es: <strong>$codigo</strong></h3><p>Expira en 5 minutos.</p>";
        $mail->AltBody = "Tu código es: $codigo. Expira en 5 minutos.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error enviando correo 2FA: {$mail->ErrorInfo}");
        return false;
    }
}
?>