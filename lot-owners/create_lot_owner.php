<?php
include __DIR__ . '/../config.php';

// Get and decode input
$data = json_decode(file_get_contents('php://input'), true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit();
}

// Validate required fields for tbl_lot
$required_fields = [
    'customer_id', 'plot_id', 'niche_number'
];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        echo json_encode(["success" => false, "message" => "Missing required field: $field"]);
        exit();
    }
}

// Insert new lot ownership
$insert = $conn->prepare(
    "INSERT INTO `tbl_lot` (
        `customer_id`, `plot_id`, `niche_number`, `niche_status`, `created_at`, `updated_at`
    ) VALUES (?, ?, ?, ?, NOW(), NOW())"
);

if (!$insert) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    $conn->close();
    exit();
}

$niche_status = 'reserved'; 
$insert->bind_param(
    "ssss",
    $data['customer_id'],
    $data['plot_id'],
    $data['niche_number'],
    $niche_status
);

$insert->execute();

if ($insert->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Lot ownership created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create lot ownership", "error" => $insert->error]);
}

$insert->close();
$conn->close();
?>