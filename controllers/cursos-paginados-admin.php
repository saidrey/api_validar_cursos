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
    // Requiere autenticación de administrador
    requireAdmin();

    $database = new Database();
    $db = $database->getConnection();

    // Por defecto muestra TODOS (activos e inactivos); con ?soloActivos=1 filtra solo activos
    $soloActivos = isset($_GET['soloActivos']) && $_GET['soloActivos'] === '1';
    $filtroActivo = $soloActivos ? 'WHERE activo = 1' : '';

    $baseQuery = "SELECT id, nombre, descripcion, resumen, duracion, instructor, precio, imagen, activo, fecha_creacion
                  FROM cursos $filtroActivo";

    // Campos donde buscar
    $searchFields = ['nombre', 'descripcion', 'instructor'];

    // Construir query con paginación, búsqueda y ordenamiento
    $paginationData = Pagination::buildQuery($baseQuery, $_GET, $searchFields);

    // Obtener total de registros
    $countStmt = $db->prepare($paginationData['countQuery']);
    foreach ($paginationData['bindParams'] as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Obtener datos paginados
    $stmt = $db->prepare($paginationData['query']);
    foreach ($paginationData['bindParams'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $paginationData['limit'], PDO::PARAM_INT);
    $stmt->bindValue(':offset', $paginationData['offset'], PDO::PARAM_INT);
    $stmt->execute();

    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir tipos numéricos
    foreach ($cursos as &$curso) {
        $curso['id'] = intval($curso['id']);
        $curso['precio'] = floatval($curso['precio']);
        $curso['activo'] = intval($curso['activo']);
    }

    // Construir respuesta paginada
    $response = Pagination::buildResponse(
        $cursos,
        $total,
        $paginationData['page'],
        $paginationData['limit']
    );

    echo json_encode($response);

} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
