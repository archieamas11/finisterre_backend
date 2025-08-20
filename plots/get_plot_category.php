<?php
include __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth/jwt.php';
require_auth(false);
$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT DISTINCT category FROM tbl_plots");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    echo json_encode(["success" => true, "message" => "Categories found", "categories" => $categories]);
} else {
    echo json_encode(["success" => false, "message" => "No categories found"]);
}
$conn->close();
?>