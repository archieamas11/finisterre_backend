<?php
include __DIR__ . '/../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing customer ID']);
    exit;
}

$id = intval($data['id']);

$stmt = $mysqli->prepare("DELETE FROM tbl_customers WHERE customer_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}

$stmt->close();
$mysqli->close();
?>