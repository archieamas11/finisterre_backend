<?php
include __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth/jwt.php';
require_auth(false);
$data = json_decode(file_get_contents('php://input'), true);

$plot_id = $data['plot_id'] ?? null;
$stmt = $conn->prepare("SELECT * FROM tbl_niche WHERE plot_id = $plot_id
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $LotOwners = [];
    while ($row = $result->fetch_assoc()) {
        $LotOwners[] = $row;
    }
    echo json_encode(["success" => true, "message" => "lot data found", "lotOwners" => $LotOwners]);
} else {
    echo json_encode(["success" => false, "message" => "lot not found"]);
}
$conn->close();
?>