<?php
include __DIR__ . '/../config.php';

// Try to get plot_id from JSON body or POST form data
$data = json_decode(file_get_contents('php://input'), true);
$plotId = null;
if (isset($data['plot_id'])) {
    $plotId = $data['plot_id'];
} elseif (isset($_POST['plot_id'])) {
    $plotId = $_POST['plot_id'];
}

if (!$plotId) {
    echo json_encode(["success" => false, "message" => "Invalid id or missing plot_id"]);
    exit();
}

$stmt = $conn->prepare("SELECT file_name FROM tbl_media WHERE plot_id = ?");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->bind_param("i", $plotId);

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $fileNames = [];
    while ($row = $result->fetch_assoc()) {
        $fileNames[] = $row['file_name'];
    }
    echo json_encode([
        "success" => true,
        "message" => "available plots found",
        "plot_id" => $plotId,
        "file_names" => $fileNames
    ]);
} else {
    echo json_encode(["success" => false, "message" => "no available plots found"]);
}
$conn->close();
?>