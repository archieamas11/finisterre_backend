<?php
include __DIR__ . '/../config.php';

$stmt = $conn->prepare("SELECT * FROM tbl_plots_col");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $multiplePlots = [];
    while ($row = $result->fetch_assoc()) {
        $multiplePlots[] = $row;
    }
    echo json_encode(["success" => true, "message" => "multiple plots found", "plots" => $multiplePlots]);
} else {
    echo json_encode(["success" => false, "message" => "no multiple plots found"]);
}
$conn->close();
?>