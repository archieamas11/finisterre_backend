<?php
include __DIR__ . '/../config.php';
$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT category FROM tbl_plots WHERE status = 'available' AND plot_id = ?");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->bind_param("s", $data['plot_id']);

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