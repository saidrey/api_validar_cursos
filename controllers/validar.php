<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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

if($method === 'GET') {
    if(isset($_GET['tipo_documento']) && isset($_GET['documento'])) {
        $diploma->tipo_documento = $_GET['tipo_documento'];
        $diploma->documento = $_GET['documento'];
        $stmt = $diploma->validarPorDocumento();
        $diplomas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($diplomas) > 0) {
            echo json_encode([
                'valido' => true,
                'diplomas' => $diplomas
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'valido' => false,
                'mensaje' => 'No se encontraron diplomas para este documento'
            ]);
        }
    } elseif(isset($_GET['codigo'])) {
        $diploma->codigo_verificacion = $_GET['codigo'];
        $result = $diploma->validarPorCodigo();
        
        if($result) {
            echo json_encode([
                'valido' => true,
                'diploma' => $result
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'valido' => false,
                'mensaje' => 'Código de verificación inválido'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['mensaje' => 'Tipo de documento y número, o código de verificación requerido']);
    }
} else {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
}
