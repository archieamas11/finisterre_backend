<?php
include __DIR__ . '/../config.php';
// Require a valid JWT before proceeding
require_once __DIR__ . '/../auth/jwt.php';
require_auth(false);
header('Content-Type: application/json; charset=utf-8');

// Read JSON body (optional filters)
$data = json_decode(file_get_contents('php://input'), true) ?: [];

$limit = isset($data['limit']) && is_numeric($data['limit']) ? (int)$data['limit'] : 100;
if ($limit <= 0 || $limit > 1000) {
    $limit = 100;
}

$userId = isset($data['userId']) && $data['userId'] !== '' ? (int)$data['userId'] : null;
$isAdmin = isset($data['isAdmin']) && ($data['isAdmin'] === 0 || $data['isAdmin'] === 1 || $data['isAdmin'] === '0' || $data['isAdmin'] === '1') ? (int)$data['isAdmin'] : null;
$search = isset($data['search']) && $data['search'] !== '' ? trim((string)$data['search']) : null;

// Build SQL to fetch users
$sql = "SELECT
    u.user_id,
    u.username,
    u.isAdmin,
    u.isArchive,
    u.created_at,
    u.updated_at
FROM tbl_users AS u";

$conditions = [];
$params = [];
$types = '';

if ($userId !== null) {
    $conditions[] = 'u.user_id = ?';
    $params[] = $userId;
    $types .= 'i';
}

if ($isAdmin !== null) {
    $conditions[] = 'u.isAdmin = ?';
    $params[] = $isAdmin;
    $types .= 'i';
}

if ($search !== null) {
    // Search in username (and optionally customer_id or other fields if needed)
    $conditions[] = '(u.username LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $types .= 's';
}

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY u.created_at DESC LIMIT ?';
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL prepare error", "error" => $conn->error]);
    exit();
}

if (!empty($params)) {
    // bind_param requires variables, so unpack into an array of references
    $bindNames = [];
    $bindNames[] = $types;
    foreach ($params as $k => $v) {
        // ensure each param is a variable
        $bindNames[] = &$params[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $bindNames);
}

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "SQL execute error", "error" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode(["success" => true, "message" => "Users fetched", "users" => $users]);

$stmt->close();
$conn->close();
?>