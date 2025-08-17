<?php
include __DIR__ . '/../config.php';
include_once __DIR__ . '/../format-utils.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$skipFormat = ['coordinates'];
$forceLowercase = [];
$data = formatData(
    $data,
    $skipFormat,
    $forceLowercase
);

$required_fields = ['rows', 'columns', 'coordinates'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "message" => "Missing required field: $field"]);
        exit();
    }
}

// Check for JSON decode errors
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input: " . json_last_error_msg()]);
    exit();
}

$category = "Chambers";
$block = null;
$length = null;
$width = null;
$area = null;
$status = null;

// Insert new Memorial Chambers plot
$insert = $conn->prepare("INSERT INTO `tbl_plots`(`category`, `rows`, `columns`, `coordinates`, `status`) VALUES (?, ?, ?, ?, ?)");

if (!$insert) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    $conn->close();
    exit();
}

$insert->bind_param(
    "sssss",
    $category,
    $data['rows'],
    $data['columns'],
    $data['coordinates'],
    $status
);
$insert->execute();

if ($insert->affected_rows > 0) {
    $plot_id = $conn->insert_id;
    echo json_encode(["success" => true, "message" => "Memorial Chambers plot created successfully", "plot_id" => $plot_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create Memorial Chambers plot"]);
}

$insert->close();
$conn->close();
?>