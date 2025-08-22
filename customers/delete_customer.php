<?php
include __DIR__ . '/../config.php';
// Require a valid JWT before proceeding
require_once __DIR__ . '/../auth/jwt.php';
include_once __DIR__ . '/../logs/log_helper.php';
$payload = require_auth(false);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing customer ID']);
    exit;
}

$id = intval($data['id']);

// 🔍 Get customer name for logging before deletion
$customer_name = '';
$nameStmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) as full_name FROM tbl_customers WHERE customer_id = ?");
if ($nameStmt) {
    $nameStmt->bind_param("i", $id);
    $nameStmt->execute();
    $nameResult = $nameStmt->get_result();
    if ($row = $nameResult->fetch_assoc()) {
        $customer_name = $row['full_name'];
    }
    $nameStmt->close();
}

$stmt = $conn->prepare("DELETE FROM tbl_customers WHERE customer_id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'SQL error preparing statement', 'error' => $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Log admin action if applicable (non-blocking)
    $logResult = null;
    if (!empty($payload) && ($payload->isAdmin ?? false)) {
        $userIdentifier = $payload->username ?? ($payload->user_id ?? null);
        if ($userIdentifier) {
            $action = 'DELETE';
            $target = "Customer C-{$id}";
            $details = "Deleted customer: {$customer_name}";
            $logResult = create_log($conn, $userIdentifier, $action, $target, $details);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Customer deleted successfully', 'log' => $logResult]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Delete failed', 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>