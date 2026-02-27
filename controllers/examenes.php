<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once '../config/Database.php';
include_once '../middleware/auth.php';
include_once '../models/Examen.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($method === 'GET') {
    $user = requireAuth();

    $database = new Database();
    $db = $database->getConnection();

    $examen = new Examen($db);
    $stmt = $examen->listarPorUsuario($user['user_id']);
    $examenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($examenes as &$e) {
        $e['id'] = intval($e['id']);
        $e['nota'] = floatval($e['nota']);
    }

    echo json_encode($examenes);

} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
