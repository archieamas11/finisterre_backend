<?php
// Content-Type for all JSON responses from auth middleware helpers
header("Content-Type: application/json; charset=utf-8");

// Bring in config (loads env, constants, DB if needed)
require_once __DIR__ . '/../config.php';

// Load JWT library
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

if (!function_exists('auth_respond_json')) {
    function auth_respond_json(int $status, array $data): void {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}

if (!function_exists('get_authorization_header')) {
    function get_authorization_header(): ?string {
        // Try standard server var
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }
        // Fallback for Apache
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    return trim($value);
                }
            }
        }
        return null;
    }
}

if (!function_exists('get_bearer_token')) {
    function get_bearer_token(): ?string {
        $header = get_authorization_header();
        if ($header && preg_match('/^Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

if (!function_exists('verify_jwt')) {
    function verify_jwt(?string $token) {
        if (!$token) {
            auth_respond_json(401, [
                'success' => false,
                'message' => 'Missing Bearer token',
            ]);
        }
        try {
            $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
            // Basic claim validation (iss, aud)
            if (($decoded->iss ?? null) !== JWT_ISSUER || ($decoded->aud ?? null) !== JWT_AUDIENCE) {
                auth_respond_json(401, [
                    'success' => false,
                    'message' => 'Invalid token claims',
                ]);
            }
            return $decoded; // stdClass
        } catch (ExpiredException $e) {
            auth_respond_json(401, [
                'success' => false,
                'message' => 'Token expired',
            ]);
        } catch (SignatureInvalidException $e) {
            auth_respond_json(401, [
                'success' => false,
                'message' => 'Invalid token signature',
            ]);
        } catch (Throwable $e) {
            auth_respond_json(401, [
                'success' => false,
                'message' => 'Invalid token',
            ]);
        }
    }
}

if (!function_exists('require_auth')) {
    function require_auth(bool $adminRequired = false) {
        $token = get_bearer_token();
        $payload = verify_jwt($token);
        $isAdmin = (bool)($payload->isAdmin ?? false);
        if ($adminRequired && !$isAdmin) {
            auth_respond_json(403, [
                'success' => false,
                'message' => 'Forbidden: Admin access required',
            ]);
        }
        return $payload;
    }
}
