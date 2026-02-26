<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Usuario.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(empty($data->nombre) || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Nombre, correo y contraseña son requeridos']);
    exit();
}

if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'El correo no es válido']);
    exit();
}

if(strlen($data->password) < 6) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

if($usuario->emailExiste($data->email)) {
    http_response_code(409);
    echo json_encode(['mensaje' => 'Este correo ya está registrado']);
    exit();
}

$usuario->nombre   = htmlspecialchars(strip_tags(trim($data->nombre)));
$usuario->email    = $data->email;
$usuario->password = $data->password;
$usuario->rol      = 'usuario';

if($usuario->crear()) {
    http_response_code(201);
    echo json_encode(['mensaje' => 'Cuenta creada exitosamente']);
} else {
    http_response_code(503);
    echo json_encode(['mensaje' => 'Error al crear la cuenta. Intenta de nuevo.']);
}
