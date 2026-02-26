<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../models/CorreoEnviado.php';
include_once '../middleware/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($method === 'GET') {
    // Requiere autenticación de admin
    $user = requireAdmin();

    $database = new Database();
    $db = $database->getConnection();
    $correo = new CorreoEnviado($db);

    // Obtener estadísticas
    if(isset($_GET['estadisticas'])) {
        $stmt = $correo->obtenerEstadisticas();
        $estadisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($estadisticas);
        exit();
    }

    // Obtener por curso
    if(isset($_GET['curso_id'])) {
        $correo->curso_id = $_GET['curso_id'];
        $stmt = $correo->leerPorCurso();
        $correos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($correos);
        exit();
    }

    // Obtener todos
    $stmt = $correo->leer();
    $correos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($correos);

} else if($method === 'DELETE') {
    $user = requireAdmin();

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if(!$id) {
        http_response_code(400);
        echo json_encode(['mensaje' => 'ID requerido']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    $correo = new CorreoEnviado($db);
    $correo->id = $id;

    if($correo->eliminar()) {
        echo json_encode(['mensaje' => 'Mensaje eliminado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['mensaje' => 'Error al eliminar el mensaje']);
    }

} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
