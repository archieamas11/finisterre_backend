<?php
// Always send CORS headers
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    if (preg_match('/^http:\\/\\/localhost:\\d+$/', $origin)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


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
// Debug: log received username
if ($username === '') {
    echo json_encode(["success" => false, "message" => "Missing username", "debug_username" => $username]);
    exit();
}

$stmt = $conn->prepare("SELECT username FROM tbl_users WHERE username = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($username);
    $stmt->fetch();
    echo json_encode(["success" => true, "message" => "User found", "username" => $username]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
$conn->close();
?>