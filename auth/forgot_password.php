<?php
include __DIR__ . '/../config.php';

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