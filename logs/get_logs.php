<?php
include __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

// Read JSON body (optional filters)
$data = json_decode(file_get_contents('php://input'), true) ?: [];

$limit = isset($data['limit']) && is_numeric($data['limit']) ? (int)$data['limit'] : 100;
if ($limit <= 0 || $limit > 1000) {
    $limit = 100;
}

$userId = isset($data['userId']) && $data['userId'] !== '' ? (int)$data['userId'] : null;
$action = isset($data['action']) && $data['action'] !== '' ? strtoupper(trim((string)$data['action'])) : null;
$search = isset($data['search']) && $data['search'] !== '' ? trim((string)$data['search']) : null;

// Build SQL
$sql = "SELECT 
    l.log_id,
    l.user_id,
    u.username,
    l.action,
    l.target,
    l.details,
    l.created_at
FROM tbl_logs AS l
LEFT JOIN tbl_users AS u ON u.user_id = l.user_id";

$conditions = [];
$params = [];
$types = '';

if ($userId !== null) {
    $conditions[] = 'l.user_id = ?';
    $params[] = $userId;
    $types .= 'i';
}

if ($action !== null) {
    $conditions[] = 'l.action = ?';
    $params[] = $action;
    $types .= 's';
}

if ($search !== null) {
    // Search in username, target and details
    $conditions[] = '(u.username LIKE ? OR l.target LIKE ? OR l.details LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY l.created_at DESC LIMIT ?';
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL prepare error", "error" => $conn->error]);
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "SQL execute error", "error" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$logs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

echo json_encode(["success" => true, "message" => "Logs fetched", "logs" => $logs]);

$stmt->close();
$conn->close();
?>