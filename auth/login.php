<?php
include __DIR__ . '/../config.php';
include_once __DIR__ . '/../logs/log_helper.php';

// Ensure we return JSON for errors
header("Content-Type: application/json; charset=utf-8");

// Require Composer autoload if available. In production it's common to forget composer install,
// which causes a fatal error. Return a JSON 500 with a helpful message instead.
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server configuration error: dependencies not installed (vendor/autoload.php missing)."], JSON_UNESCAPED_SLASHES);
    // Optionally log the problem server-side
    if (function_exists('create_log')) {
        @create_log($conn ?? null, 'system', 'ERROR', 'System', 'Vendor autoload missing on login.php');
    }
    exit();
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$data = json_decode(file_get_contents('php://input'), true);
$username = isset($data['username']) ? $data['username'] : '';
$password = isset($data['password']) ? $data['password'] : '';

// Validate input
if ($username === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Missing username or password"]);
    exit();
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT user_id, password, isAdmin FROM tbl_users WHERE username = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        // Generate JWT token using environment variables
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60 * 24); // 24 hours
        
        $payload = array(
            "iss" => JWT_ISSUER,
            "aud" => JWT_AUDIENCE,
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "user_id" => $user['user_id'],
            "username" => $username,
            "isAdmin" => (bool)$user['isAdmin']
        );
        
        $token = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
        
        // Log admin login server-side (only when isAdmin == 1)
        if (!empty($user['isAdmin']) && (int)$user['isAdmin'] === 1) {
            create_log($conn, $username, 'LOGIN', 'System', "{$username} logged in");
        }

        echo json_encode([
            "success" => true,
            "message" => $user['isAdmin'] ? "Admin login successful" : "User login successful",
            "token" => $token,
            "isAdmin" => (bool)$user['isAdmin']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>