<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->nombre) && !empty($data->email) && !empty($data->telefono) && !empty($data->mensaje)) {

        $config = require __DIR__ . '/../config/mail.php';

        try {
            $mail = new PHPMailer(true);

            if($config['mode'] === 'production') {
                $mail->isSMTP();
                $mail->Host = $config['smtp']['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $config['smtp']['username'];
                $mail->Password = $config['smtp']['password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = $config['smtp']['port'];
                $mail->Timeout = 10;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($config['smtp']['from_email'], $config['smtp']['from_name']);
            $mail->addAddress($config['contact_email']);
            $mail->addReplyTo($data->email, $data->nombre);

            $mail->isHTML(true);
            $mail->Subject = "Nuevo mensaje de contacto - {$data->nombre}";

            $mail->Body = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='font-family: Arial, sans-serif; color: #333;'>
  <div style='max-width:600px; margin:0 auto; padding:20px;'>
    <div style='background:#137fec; color:white; padding:24px; border-radius:10px 10px 0 0;'>
      <h2 style='margin:0;'>Nuevo mensaje de contacto</h2>
    </div>
    <div style='background:#f8f9fa; padding:24px; border-radius:0 0 10px 10px;'>
      <p><strong>Nombre:</strong> {$data->nombre}</p>
      <p><strong>Email:</strong> {$data->email}</p>
      <p><strong>Teléfono:</strong> {$data->telefono}</p>
      <p><strong>Mensaje:</strong></p>
      <div style='background:white; padding:16px; border-left:4px solid #137fec; border-radius:4px;'>
        " . nl2br(htmlspecialchars($data->mensaje)) . "
      </div>
    </div>
  </div>
</body>
</html>";

            $mail->AltBody = "Nombre: {$data->nombre}\nEmail: {$data->email}\nTeléfono: {$data->telefono}\nMensaje: {$data->mensaje}";

            if($config['mode'] === 'production') {
                $mail->send();
            }

            http_response_code(200);
            echo json_encode(['mensaje' => 'Mensaje enviado correctamente']);

        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'mensaje' => 'Error al enviar el mensaje',
                'error' => $e->getMessage()
            ]);
        }

    } else {
        http_response_code(400);
        echo json_encode(['mensaje' => 'Nombre, email, teléfono y mensaje son requeridos']);
    }
} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
