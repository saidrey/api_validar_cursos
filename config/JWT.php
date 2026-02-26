<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {
    private static $secret_key;
    private static $algorithm = 'HS256';
    private static $expiration_time = 86400; // 24 horas en segundos

    /**
     * Generar token JWT
     * @param array $user_data Datos del usuario (id, email, rol)
     * @return string Token JWT
     */
    private static function getSecretKey(): string {
        if (!self::$secret_key) {
            self::$secret_key = getenv('JWT_SECRET') ?: 'diplomas_app_secret_key_change_in_production';
        }
        return self::$secret_key;
    }

    public static function generateToken($user_data) {
        $issued_at = time();
        $expiration = $issued_at + self::$expiration_time;

        $payload = [
            'iat' => $issued_at,           // Issued at (cuándo se creó)
            'exp' => $expiration,          // Expiration (cuándo expira)
            'user_id' => $user_data['id'],
            'email' => $user_data['email'],
            'rol' => $user_data['rol']
        ];

        return JWT::encode($payload, self::getSecretKey(), self::$algorithm);
    }

    /**
     * Verificar y decodificar token JWT
     * @param string $token Token JWT
     * @return array|null Datos del usuario o null si es inválido
     */
    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecretKey(), self::$algorithm));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtener token del header Authorization
     * @return string|null Token o null si no existe
     */
    public static function getBearerToken() {
        // getallheaders() funciona en local, pero en hosting compartido
        // Apache pasa el header como variable de entorno vía .htaccess RewriteRule
        $authHeader = null;

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        if (!$authHeader) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION']
                ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
                ?? null;
        }

        if ($authHeader) {
            $matches = [];
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
