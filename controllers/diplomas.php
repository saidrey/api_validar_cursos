<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Diploma.php';

$database = new Database();
$db = $database->getConnection();
$diploma = new Diploma($db);

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $diploma->id = $_GET['id'];
            $result = $diploma->leerUno();
            echo json_encode($result ?: ['mensaje' => 'Diploma no encontrado']);
        } else {
            $stmt = $diploma->leer();
            $diplomas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($diplomas);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->curso_id) && !empty($data->nombre_estudiante) && !empty($data->tipo_documento) && !empty($data->documento)) {
            $diploma->curso_id = $data->curso_id;
            $diploma->nombre_estudiante = $data->nombre_estudiante;
            $diploma->tipo_documento = $data->tipo_documento;
            $diploma->documento = $data->documento;
            $diploma->fecha_emision = $data->fecha_emision ?? date('Y-m-d');

            if($diploma->crear()) {
                http_response_code(201);
                echo json_encode(['mensaje' => 'Diploma creado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al crear diploma']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'Datos incompletos']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $diploma->id = $data->id;
            $diploma->curso_id = $data->curso_id;
            $diploma->nombre_estudiante = $data->nombre_estudiante;
            $diploma->tipo_documento = $data->tipo_documento;
            $diploma->documento = $data->documento;
            $diploma->fecha_emision = $data->fecha_emision;
            $diploma->activo = isset($data->activo) ? intval($data->activo) : 1;

            if($diploma->actualizar()) {
                echo json_encode(['mensaje' => 'Diploma actualizado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al actualizar diploma']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'ID requerido']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $diploma->id = $data->id;
            if($diploma->eliminar()) {
                echo json_encode(['mensaje' => 'Diploma eliminado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al eliminar diploma']);
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
