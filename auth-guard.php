<?php
require_once __DIR__ . '/../../libs/src/JWT.php';
require_once __DIR__ . '/../../libs/src/Key.php';
// Gerekirse diğer Exception dosyalarını da ekle
require_once __DIR__ . '/../../libs/src/JWTExceptionWithPayloadInterface.php';
require_once __DIR__ . '/../../libs/src/ExpiredException.php';
require_once __DIR__ . '/../../libs/src/SignatureInvalidException.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validate_token() {
    // core.php içinde JWT_SECRET_KEY tanımlı olmalı
    require_once __DIR__ . '/core.php';

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["message" => "Access denied. No token provided."]);
        exit();
    }

    // "Bearer <token>" formatını kontrol et
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["message" => "Access denied. Malformed token."]);
        exit();
    }

    $jwt = $matches[1];
    if (!$jwt) {
        http_response_code(401);
        echo json_encode(["message" => "Access denied. Token missing."]);
        exit();
    }

    try {
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
        return $decoded->data; // id, role gibi bilgiler burada olacak
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Access denied. Invalid token.", "error" => $e->getMessage()]);
        exit();
    }
}
?>
