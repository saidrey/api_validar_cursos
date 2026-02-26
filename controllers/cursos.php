<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Curso.php';
include_once '../middleware/auth.php';

$database = new Database();
$db = $database->getConnection();
$curso = new Curso($db);

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch($method) {
    case 'GET':
        // GET es público (no requiere autenticación)
        if(isset($_GET['id'])) {
            $curso->id = $_GET['id'];
            $result = $curso->leerUno();
            echo json_encode($result ?: ['mensaje' => 'Curso no encontrado']);
        } else {
            $stmt = $curso->leer();
            $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($cursos);
        }
        break;

    case 'POST':
        // POST requiere autenticación de admin
        $user = requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->nombre)) {
            $curso->nombre = $data->nombre;
            $curso->descripcion = $data->descripcion ?? '';
            $curso->resumen = $data->resumen ?? '';
            $curso->duracion = $data->duracion ?? '';
            $curso->instructor = $data->instructor ?? '';
            $curso->precio = $data->precio ?? 0;
            $curso->imagen = $data->imagen ?? '';

            if($curso->crear()) {
                http_response_code(201);
                echo json_encode(['mensaje' => 'Curso creado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al crear curso']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'Nombre del curso requerido']);
        }
        break;

    case 'PUT':
        // PUT requiere autenticación de admin
        $user = requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $curso->id = $data->id;
            $curso->nombre = $data->nombre;
            $curso->descripcion = $data->descripcion;
            $curso->resumen = $data->resumen;
            $curso->duracion = $data->duracion;
            $curso->instructor = $data->instructor;
            $curso->precio = $data->precio;
            $curso->imagen = $data->imagen;
            $curso->activo = isset($data->activo) ? intval($data->activo) : 1;

            if($curso->actualizar()) {
                echo json_encode(['mensaje' => 'Curso actualizado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al actualizar curso']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['mensaje' => 'ID requerido']);
        }
        break;

    case 'DELETE':
        // DELETE requiere autenticación de admin
        $user = requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $curso->id = $data->id;
            if($curso->eliminar()) {
                echo json_encode(['mensaje' => 'Curso eliminado exitosamente']);
            } else {
                http_response_code(503);
                echo json_encode(['mensaje' => 'Error al eliminar curso']);
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
