<?php
include __DIR__ . '/../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = isset($data['username']) ? $data['username'] : '';
$new_password = isset($data['new_password']) ? $data['new_password'] : '';

// Debug: log received username and new_password
if ($username === '' || $new_password === '') {
    echo json_encode([
        "success" => false,
        "message" => "Missing username or new_password",
    ]);
    exit();
}

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Prepare update statement
$stmt = $conn->prepare("UPDATE tbl_users SET password = ? WHERE username = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "SQL error",
        "error" => $conn->error
    ]);
    exit();
}
$stmt->bind_param("ss", $hashed_password, $username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        "success" => true,
        "message" => "Password reset successful"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "User not found or password not changed"
    ]);
}
$conn->close();
?>