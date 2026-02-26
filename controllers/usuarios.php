<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $usuario->id = $_GET['id'];
            $result = $usuario->leerUno();
            echo json_encode($result ?: ['mensaje' => 'Usuario no encontrado']);
        } else {
            $stmt = $usuario->leer();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($usuarios);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->nombre) && !empty($data->email) && !empty($data->password)) {
            $usuario->nombre = $data->nombre;
            $usuario->email = $data->email;
            $usuario->password = $data->password;
            $usuario->rol = $data->rol ?? 'usuario';

            if($usuario->crear()) {
                http_response_code(201);
                echo json_encode(['mensaje' => 'Usuario creado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al crear usuario']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'Datos incompletos']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $usuario->id = $data->id;
            $usuario->nombre = $data->nombre;
            $usuario->email = $data->email;
            $usuario->rol = $data->rol;
            $usuario->activo = isset($data->activo) ? intval($data->activo) : 1;

            if($usuario->actualizar()) {
                echo json_encode(['mensaje' => 'Usuario actualizado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al actualizar usuario']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'ID requerido']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $usuario->id = $data->id;
            if($usuario->eliminar()) {
                echo json_encode(['mensaje' => 'Usuario eliminado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al eliminar usuario']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'ID requerido']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['mensaje' => 'Método no permitido']);
        break;
}
