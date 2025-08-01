<?php
include __DIR__ . '/../config.php';
$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT plot_id FROM tbl_plots WHERE status = 'available'");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $plots = [];
    while ($row = $result->fetch_assoc()) {
        $plots[] = $row;
    }
    echo json_encode(["success" => true, "message" => "available plots found", "plots" => $plots]);
} else {
    echo json_encode(["success" => false, "message" => "no available plots found"]);
}
$conn->close();
?>