<?php
include_once __DIR__ . '/../config/JWT.php';

/**
 * Middleware: Requiere autenticación
 * Valida que el token JWT sea válido
 * @return array Datos del usuario decodificados del token
 */
function requireAuth() {
    $token = JWTHandler::getBearerToken();
    
    if (!$token) {
        http_response_code(401);
        echo json_encode(['mensaje' => 'Token no proporcionado. Debes iniciar sesión.']);
        exit();
    }
    
    $decoded = JWTHandler::validateToken($token);
    
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(['mensaje' => 'Token inválido o expirado. Por favor inicia sesión nuevamente.']);
        exit();
    }
    
    return $decoded; // Retorna datos del usuario
}

/**
 * Middleware: Requiere rol de administrador
 * Valida token y verifica que el usuario sea admin
 * @return array Datos del usuario decodificados del token
 */
function requireAdmin() {
    $user = requireAuth();
    
    if ($user['rol'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['mensaje' => 'Acceso denegado. Se requiere rol de administrador.']);
        exit();
    }
    
    return $user;
}

/**
 * Middleware: Requiere ser el mismo usuario o admin
 * Valida que el usuario autenticado sea el dueño del recurso o admin
 * @param int $resource_user_id ID del usuario dueño del recurso
 * @return array Datos del usuario decodificados del token
 */
function requireOwnerOrAdmin($resource_user_id) {
    $user = requireAuth();
    
    if ($user['user_id'] != $resource_user_id && $user['rol'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['mensaje' => 'Acceso denegado. No tienes permisos para este recurso.']);
        exit();
    }
    
    return $user;
}
