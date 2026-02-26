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
    $filtroActivo = $soloActivos ? 'WHERE d.activo = 1' : '';

    // Subquery para evitar conflicto WHERE cuando se combina filtro + búsqueda
    $innerQuery = "SELECT d.id, d.curso_id, d.nombre_estudiante, d.tipo_documento, d.documento,
                          d.fecha_emision, d.codigo_verificacion, d.activo, d.fecha_creacion,
                          c.nombre as curso_nombre
                   FROM diplomas d
                   LEFT JOIN cursos c ON d.curso_id = c.id
                   $filtroActivo";

    $baseQuery = "SELECT * FROM ($innerQuery) as t";

    // Campos donde buscar (sin alias de tabla, vienen del subquery)
    $searchFields = ['nombre_estudiante', 'documento', 'curso_nombre'];

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

    $diplomas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($diplomas as &$diploma) {
        $diploma['id'] = intval($diploma['id']);
        $diploma['curso_id'] = intval($diploma['curso_id']);
        $diploma['activo'] = intval($diploma['activo']);
    }

    echo json_encode(Pagination::buildResponse(
        $diplomas,
        $total,
        $paginationData['page'],
        $paginationData['limit']
    ));

} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
