<?php
include __DIR__ . '/../config.php';
// Require a valid JWT before proceeding
require_once __DIR__ . '/../auth/jwt.php';
// returns payload when token is present
$payload = require_auth(false);
// include log helper
include_once __DIR__ . '/../logs/log_helper.php';
include_once __DIR__ . '/../format-utils.php';

// Get and decode input
$data = json_decode(file_get_contents('php://input'), true);
$skipFormat = ['contact_number', 'birth_date', 'status', 'gender'];
$forceLowercase = ['email'];
$data = formatData(
    $data,
    $skipFormat,
    $forceLowercase
);
$required_fields = ['customer_id', 'last_name', 'first_name', 'middle_name', 'address', 'contact_number', 'email', 'birth_date', 'gender', 'religion', 'citizenship', 'status', 'occupation'];
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

// Update customer using prepared statement
$update = $conn->prepare("UPDATE `tbl_customers` SET `last_name`=?, `first_name`=?, `middle_name`=?, `address`=?, `contact_number`=?, `email`=?, `birth_date`=?, `gender`=?, `religion`=?, `citizenship`=?, `status`=?, `occupation`=?, `updated_at`=NOW() WHERE `customer_id`=?");

if (!$update) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    $conn->close();
    exit();
}

$update->bind_param(
    "ssssssssssssi",
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
    $data['occupation'],
    $data['customer_id']
);

if ($update->execute()) {
    // Log admin action if applicable (non-blocking)
    // $logResult = null;
    // if (!empty($payload) && ($payload->isAdmin ?? false)) {
    //     $userIdentifier = $payload->username ?? ($payload->user_id ?? null);
    //     if ($userIdentifier) {
    //         $action = 'UPDATE';
    //         $target = "Customer C-{$data['customer_id']}";
    //         $details = "Updated customer: {$data['first_name']} {$data['last_name']}";
    //         $logResult = create_log($conn, $userIdentifier, $action, $target, $details);
    //     }
    // }

    echo json_encode([
        'success' => true,
        'message' => 'Customer updated successfully',
        'id' => $data['customer_id'],
        'log' => $logResult
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$update->close();
$conn->close();
?>