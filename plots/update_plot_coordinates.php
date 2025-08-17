<?php
include __DIR__ . '/../config.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

$plot_id = $input['plot_id'] ?? null;
$coordinates = $input['coordinates'] ?? null; // expected format: "lng, lat"

if (!$plot_id || !$coordinates) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields: plot_id and coordinates']);
    exit();
}

// Basic validation: coordinates should be two numbers separated by comma
if (!preg_match('/^\s*-?\d{1,3}\.\d+\s*,\s*-?\d{1,2}\.\d+\s*$/', $coordinates)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid coordinates format. Expecting "lng, lat"']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE tbl_plots SET coordinates = ? WHERE plot_id = ?");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('ss', $coordinates, $plot_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        // plot might not exist or coordinates unchanged
        echo json_encode(['success' => true, 'message' => 'No changes or plot not found']);
        exit();
    }

    echo json_encode(['success' => true, 'message' => 'Coordinates updated']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update: ' . $e->getMessage()]);
}

$conn->close();
?>