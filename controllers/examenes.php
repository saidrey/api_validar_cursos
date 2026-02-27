<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once '../config/Database.php';
include_once '../middleware/auth.php';
include_once '../models/Examen.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if ($method === 'GET') {
    $user = requireAuth();

    $examen = new Examen($db);
    $stmt = $examen->listarPorUsuario($user['user_id']);
    $examenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($examenes as &$e) {
        $e['id'] = intval($e['id']);
        $e['nota'] = floatval($e['nota']);
    }

    echo json_encode($examenes);

} elseif ($method === 'POST') {
    $user = requireAuth();

    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->curso_id) && isset($data->nota)) {
        $examen = new Examen($db);
        $examen->usuario_id = $user['user_id'];
        $examen->email      = $user['email'];
        $examen->curso_id   = intval($data->curso_id);
        $examen->nota       = floatval($data->nota);

        if ($examen->crear()) {
            http_response_code(201);
            echo json_encode(['mensaje' => 'Examen guardado exitosamente']);
        } else {
            http_response_code(503);
            echo json_encode(['mensaje' => 'Error al guardar el examen']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['mensaje' => 'Datos incompletos: se requiere curso_id y nota']);
    }

} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
