<?php
include __DIR__ . '/../config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['lot_id']) || empty($input['lot_id'])) {
    echo json_encode(["success" => false, "message" => "lot_id is required"]);
    exit();
}

$lot_id = trim($input['lot_id']);

// Search for lot_id in tbl_lot and join with tbl_plots to get complete info
$stmt = $conn->prepare("
    SELECT 
        l.lot_id,
        l.plot_id,
        l.niche_number,
        l.niche_status,
        l.lot_status,
        l.customer_id,
        p.block,
        p.category,
        p.coordinates,
        p.label,
        p.rows,
        p.columns,
        p.length,
        p.width,
        p.area,
        p.status as plot_status,
        GROUP_CONCAT(m.file_name) AS file_names
    FROM tbl_lot l
    INNER JOIN tbl_plots p ON l.plot_id = p.plot_id
    LEFT JOIN tbl_media m ON p.plot_id = m.plot_id
    WHERE l.lot_id = ?
    GROUP BY l.lot_id, p.plot_id
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->bind_param("i", $lot_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $lot_data = $result->fetch_assoc();
    
    // Convert comma-separated file names to array
    $lot_data['file_names_array'] = $lot_data['file_names'] ? explode(',', $lot_data['file_names']) : [];
    
    echo json_encode([
        "success" => true, 
        "message" => "Lot found", 
        "data" => $lot_data
    ]);
} else {
    echo json_encode([
        "success" => false, 
        "message" => "Lot not found with ID: $lot_id"
    ]);
}

$stmt->close();
$conn->close();
?>
