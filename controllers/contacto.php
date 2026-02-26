<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
include_once '../config/Database.php';
include_once '../models/Curso.php';
include_once '../models/CorreoEnviado.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->nombre) && !empty($data->email) && !empty($data->telefono) && !empty($data->curso_id)) {
        
        // Obtener información del curso
        $database = new Database();
        $db = $database->getConnection();
        $curso = new Curso($db);
        $curso->id = $data->curso_id;
        $cursoInfo = $curso->leerUno();
        
        if(!$cursoInfo) {
            http_response_code(404);
            echo json_encode(['mensaje' => 'Curso no encontrado']);
            exit();
        }
        
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
                $mail->SMTPDebug = 0;
            }
            
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($config['smtp']['from_email'], $config['smtp']['from_name']);
            $mail->addAddress($data->email, $data->nombre);
            
            $mail->isHTML(true);
            $mail->Subject = "Información del Curso: {$cursoInfo['nombre']}";
            
            // Plantilla de correo
            $mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #137fec; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .curso-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #137fec; }
        .curso-info h2 { color: #137fec; margin-top: 0; }
        .detail { margin: 10px 0; }
        .detail strong { color: #0a1d37; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0; color: #666; font-size: 14px; }
        .button { display: inline-block; background: #137fec; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>¡Hola, {$data->nombre}!</h1>
            <p>Gracias por tu interés en nuestros cursos</p>
        </div>
        
        <div class='content'>
            <p>Nos complace enviarte la información del curso que solicitaste:</p>
            
            <div class='curso-info'>
                <h2>{$cursoInfo['nombre']}</h2>
                
                <div class='detail'>
                    <strong>📞 Teléfono:</strong> {$data->telefono}
                </div>
                
                <div class='detail'>
                    <strong>📚 Descripción:</strong><br>
                    {$cursoInfo['descripcion']}
                </div>
                
                <div class='detail'>
                    <strong>⏱️ Duración:</strong> {$cursoInfo['duracion']}
                </div>
                
                <div class='detail'>
                    <strong>✅ Lo que aprenderás:</strong><br>
                    " . nl2br($cursoInfo['resumen']) . "
                </div>
            </div>
            
            <p>Nuestro equipo se pondrá en contacto contigo pronto para brindarte más detalles y resolver cualquier duda que tengas.</p>
            
            <p>Si deseas más información o tienes alguna pregunta, no dudes en responder a este correo.</p>
        </div>
        
        <div class='footer'>
            <p><strong>Diplomas App</strong></p>
            <p>Tu futuro profesional comienza aquí</p>
            <p style='font-size: 12px; color: #999;'>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
            ";
            
            // Versión texto plano
            $mail->AltBody = "
Hola {$data->nombre},

Gracias por tu interés en nuestros cursos.

Tus datos de contacto:
Teléfono: {$data->telefono}

INFORMACIÓN DEL CURSO:

Curso: {$cursoInfo['nombre']}
Descripción: {$cursoInfo['descripcion']}
Duración: {$cursoInfo['duracion']}

Lo que aprenderás:
{$cursoInfo['resumen']}

Nuestro equipo se pondrá en contacto contigo pronto.

Saludos,
Diplomas App
            ";
            
            if($config['mode'] === 'production') {
                $mail->send();
                
                // Registrar correo enviado en BD
                $correoEnviado = new CorreoEnviado($db);
                $correoEnviado->destinatario_email = $data->email;
                $correoEnviado->destinatario_nombre = $data->nombre;
                $correoEnviado->destinatario_telefono = $data->telefono;
                $correoEnviado->asunto = $mail->Subject;
                $correoEnviado->cuerpo = $mail->Body;
                $correoEnviado->curso_id = $data->curso_id;
                $correoEnviado->registrarEnviado();
            } else {
                // Modo desarrollo: Guardar en log
                $logFile = __DIR__ . '/../logs/contacto.log';
                $logDir = dirname($logFile);
                
                if (!file_exists($logDir)) {
                    mkdir($logDir, 0777, true);
                }
                
                $logEntry = date('Y-m-d H:i:s') . " - ";
                $logEntry .= "Nombre: {$data->nombre}, ";
                $logEntry .= "Email: {$data->email}, ";
                $logEntry .= "Teléfono: {$data->telefono}, ";
                $logEntry .= "Curso: {$cursoInfo['nombre']}\n";
                
                file_put_contents($logFile, $logEntry, FILE_APPEND);
                
                // Registrar en BD incluso en modo desarrollo
                $correoEnviado = new CorreoEnviado($db);
                $correoEnviado->destinatario_email = $data->email;
                $correoEnviado->destinatario_nombre = $data->nombre;
                $correoEnviado->destinatario_telefono = $data->telefono;
                $correoEnviado->asunto = "Información del Curso: {$cursoInfo['nombre']}";
                $correoEnviado->cuerpo = "Modo desarrollo - Ver logs";
                $correoEnviado->curso_id = $data->curso_id;
                $correoEnviado->registrarEnviado();
            }
            
            http_response_code(200);
            echo json_encode([
                'mensaje' => 'Solicitud enviada exitosamente',
                'nota' => 'Pronto recibirás un correo con la información del curso'
            ]);
            
        } catch (Exception $e) {
            // Registrar correo fallido en BD
            $correoEnviado = new CorreoEnviado($db);
            $correoEnviado->destinatario_email = $data->email;
            $correoEnviado->destinatario_nombre = $data->nombre;
            $correoEnviado->destinatario_telefono = $data->telefono;
            $correoEnviado->asunto = "Información del Curso: {$cursoInfo['nombre']}";
            $correoEnviado->cuerpo = "Error al enviar";
            $correoEnviado->curso_id = $data->curso_id;
            $correoEnviado->error_mensaje = $e->getMessage();
            $correoEnviado->registrarFallido();
            
            http_response_code(503);
            echo json_encode([
                'mensaje' => 'Error al procesar solicitud',
                'error' => $e->getMessage()
            ]);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(['mensaje' => 'Nombre, email, teléfono y curso son requeridos']);
    }
} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
