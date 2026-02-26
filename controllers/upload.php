<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../config/JWT.php';
include_once '../middleware/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido']);
    exit();
}

// Solo admins pueden subir imágenes
requireAdmin();

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'No se recibió ningún archivo o hubo un error en la subida']);
    exit();
}

$file      = $_FILES['imagen'];
$maxSize   = 5 * 1024 * 1024; // 5 MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];

// Validar tamaño
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'El archivo supera el tamaño máximo permitido (5 MB)']);
    exit();
}

// Validar tipo MIME real (no confiar en lo que envía el cliente)
$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Tipo de archivo no permitido. Use JPG, PNG o WebP']);
    exit();
}

// Validar extensión
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExts)) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Extensión no permitida']);
    exit();
}

// Crear carpeta si no existe
$uploadDir = __DIR__ . '/../uploads/imagenes/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Nombre único para evitar colisiones
$filename  = uniqid('img_', true) . '.' . $ext;
$destPath  = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['mensaje' => 'Error al guardar el archivo en el servidor']);
    exit();
}

// Construir URL pública
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$baseUrl  = $protocol . '://' . $host;
$url      = $baseUrl . '/uploads/imagenes/' . $filename;

http_response_code(201);
echo json_encode([
    'mensaje' => 'Imagen subida correctamente',
    'url'     => $url
]);
