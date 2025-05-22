<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtils {
    private static $secret = 'clave-super-secreta'; // Cámbiala por algo más fuerte

    public static function generarToken($datos, $expiraEnMinutos = 60) {
        $ahora = time();
        $payload = [
            'iat' => $ahora,
            'exp' => $ahora + (60 * $expiraEnMinutos),
            'data' => $datos
        ];

        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public static function verificarToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret, 'HS256'));
            return (array)$decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
}
