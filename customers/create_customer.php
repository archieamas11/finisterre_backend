<?php
include __DIR__ . '/../config.php';
// Require a valid JWT before proceeding
require_once __DIR__ . '/../auth/jwt.php';
require_auth(false);
include_once __DIR__ . '/../format-utils.php';

$data = json_decode(file_get_contents('php://input'), true);
$skipFormat = ['contact_number', 'birth_date', 'status', 'gender'];
$forceLowercase = ['email'];
$data = formatData(
    $data,
    $skipFormat,
    $forceLowercase
);

$required_fields = ['last_name', 'first_name', 'middle_name', 'address', 'contact_number', 'email', 'birth_date', 'gender', 'religion', 'citizenship', 'status', 'occupation'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "message" => "Missing required field: $field"]);
        exit();
    }
}

// Check for JSON decode errors
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit();
}

// Check if customer already exists using prepared statement
$stmt = $conn->prepare("SELECT 1 FROM `tbl_customers` WHERE `last_name`=? AND `first_name`=? AND `middle_name`=?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}
$stmt->bind_param("sss", $data['last_name'], $data['first_name'], $data['middle_name']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Customer already exists"]);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Insert new customer
$insert = $conn->prepare("INSERT INTO `tbl_customers`(`last_name`, `first_name`, `middle_name`, `address`, `contact_number`, `email`, `birth_date`, `gender`, `religion`, `citizenship`, `status`, `occupation`, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

if (!$insert) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    $conn->close();
    exit();
}

$insert->bind_param(
    "ssssssssssss",
    $data['last_name'],
    $data['first_name'],
    $data['middle_name'],
    $data['address'],
    $data['contact_number'],
    $data['email'],
    $data['birth_date'],
    $data['gender'],
    $data['religion'],
    $data['citizenship'],
    $data['status'],
    $data['occupation']
);
$insert->execute();

if ($insert->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Customer created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create customer"]);
}

$insert->close();
$conn->close();
?>