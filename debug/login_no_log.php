<?php
// 🔧 Simplified login without logging for production debugging
include __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';
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
        
        // ⚠️ NO LOGGING - this is for production debugging only
        
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
