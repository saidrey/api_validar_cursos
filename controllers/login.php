<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../config/JWT.php';
include_once '../models/Usuario.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->email) && !empty($data->password)) {
        $database = new Database();
        $db = $database->getConnection();
        $usuario = new Usuario($db);
        
        $usuario->email = $data->email;
        $result = $usuario->login();

        if($result && password_verify($data->password, $result['password'])) {
            // Generar JWT para admin y usuario
            $token = JWTHandler::generateToken($result);

            unset($result['password']);
            http_response_code(200);
            echo json_encode([
                'mensaje' => 'Login exitoso',
                'token' => $token,
                'usuario' => $result
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['mensaje' => 'Credenciales inválidas']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['mensaje' => 'Email y contraseña requeridos']);
    }
} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
