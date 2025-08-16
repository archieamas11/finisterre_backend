<?php
include __DIR__ . '/../config.php';
include_once __DIR__ . '/../format-utils.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$skipFormat = ['length', 'width', 'area', 'coordinates'];
$forceLowercase = [];
$data = formatData(
    $data,
    $skipFormat,
    $forceLowercase
);

$required_fields = ['category', 'coordinates'];
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

// Insert new plot
$insert = $conn->prepare("INSERT INTO `tbl_plots`(`block`, `category`, `length`, `width`, `area`, `coordinates`, `status`) VALUES (?, ?, ?, ?, ?, ?, 'available')");

if (!$insert) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    $conn->close();
    exit();
}

$insert->bind_param(
    "ssssss",
    $data['block'],
    $data['category'],
    $data['length'],
    $data['width'],
    $data['area'],
    $data['coordinates']
);
$insert->execute();

if ($insert->affected_rows > 0) {
    $plot_id = $conn->insert_id;
    echo json_encode(["success" => true, "message" => "Plot created successfully", "plot_id" => $plot_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create plot"]);
}

$insert->close();
$conn->close();
?>