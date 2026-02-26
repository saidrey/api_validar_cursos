<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once '../config/Database.php';
include_once '../config/Pagination.php';
include_once '../middleware/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($method === 'GET') {
    requireAdmin();

    $database = new Database();
    $db = $database->getConnection();

    // Por defecto muestra TODOS; con ?soloActivos=1 filtra solo activos
    $soloActivos = isset($_GET['soloActivos']) && $_GET['soloActivos'] === '1';
    $filtroActivo = $soloActivos ? 'WHERE activo = 1' : '';

    $baseQuery = "SELECT id, nombre, email, rol, activo, fecha_creacion
                  FROM usuarios $filtroActivo";

    $searchFields = ['nombre', 'email', 'rol'];

    $paginationData = Pagination::buildQuery($baseQuery, $_GET, $searchFields);

    $countStmt = $db->prepare($paginationData['countQuery']);
    foreach ($paginationData['bindParams'] as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $db->prepare($paginationData['query']);
    foreach ($paginationData['bindParams'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $paginationData['limit'], PDO::PARAM_INT);
    $stmt->bindValue(':offset', $paginationData['offset'], PDO::PARAM_INT);
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuarios as &$usuario) {
        $usuario['id'] = intval($usuario['id']);
        $usuario['activo'] = intval($usuario['activo']);
    }

    echo json_encode(Pagination::buildResponse(
        $usuarios,
        $total,
        $paginationData['page'],
        $paginationData['limit']
    ));

} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
