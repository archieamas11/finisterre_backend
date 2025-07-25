<?php
// Always send CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include __DIR__ . '/../config.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$username = isset($data['username']) ? $data['username'] : '';
$password = isset($data['password']) ? $data['password'] : '';

// Debug: log received username and password
if ($username === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Missing username or password", "debug_username" => $username, "debug_password" => $password]);
    exit();
}

$stmt = $conn->prepare("SELECT password, isAdmin FROM tbl_users WHERE username = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashed_password, $isAdmin);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
        if ($isAdmin) {
            echo json_encode(["success" => true, "message" => "Admin login successful", "isAdmin" => true]);
        } else {
            echo json_encode(["success" => true, "message" => "User login successful", "isAdmin" => false]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>