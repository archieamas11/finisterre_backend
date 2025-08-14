<?php
include __DIR__ . '/../config.php';
include_once __DIR__ . '/../format-utils.php';

// Get and decode input
$data = json_decode(file_get_contents('php://input'), true);

$skipFormat = ['dead_interment', 'dead_birth_date', 'dead_date_death'];
$data = formatData(
    $data,
    $skipFormat,
);$required_fields = ['dead_fullname', 'dead_gender', 'dead_interment', 'dead_birth_date', 'dead_date_death'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "message" => "Missing required field: $field"]);
        exit();
    }
}

// If lot_id is not provided, try to get it from plot_id and customer_id
if (empty($data['lot_id'])) {
    if (!empty($data['plot_id']) && !empty($data['customer_id'])) {
        $lotQuery = $conn->prepare("SELECT lot_id FROM tbl_lot WHERE plot_id = ? AND customer_id = ? LIMIT 1");
        if ($lotQuery) {
            $lotQuery->bind_param("ss", $data['plot_id'], $data['customer_id']);
            $lotQuery->execute();
            $lotQuery->bind_result($found_lot_id);
            if ($lotQuery->fetch()) {
                $data['lot_id'] = $found_lot_id;
                $lotQuery->close();
            } else {
                $lotQuery->close();
                echo json_encode(["success" => false, "message" => "No lot found for the provided plot_id and customer_id"]);
                exit();
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Missing required field: lot_id (or plot_id + customer_id)"]);
        exit();
    }
}

// Check for JSON decode errors
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit();
}

// Insert new deceased record
$insert = $conn->prepare("INSERT INTO `tbl_deceased`(`lot_id`, `dead_fullname`, `dead_gender`, `dead_interment`, `dead_birth_date`, `dead_date_death`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");

if (!$insert) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    $conn->close();
    exit();
}

$insert->bind_param(
    "ssssss",
    $data['lot_id'],
    $data['dead_fullname'],
    $data['dead_gender'],
    $data['dead_interment'],
    $data['dead_birth_date'],
    $data['dead_date_death']
);
$insert->execute();

if ($insert->affected_rows > 0) {
    // Check if niche_number exists for this lot_id
    $nicheCheck = $conn->prepare("SELECT niche_number FROM tbl_lot WHERE lot_id = ?");
    if ($nicheCheck) {
        $nicheCheck->bind_param("s", $data['lot_id']);
        $nicheCheck->execute();
        $nicheCheck->bind_result($niche_number);
        
        if ($nicheCheck->fetch()) {
            $nicheCheck->close();
            
            if (!empty($niche_number)) {
                // Has niche_number - update niche_status in tbl_lot to 'occupied'
                $updateNiche = $conn->prepare("UPDATE tbl_lot SET niche_status = 'occupied' WHERE lot_id = ?");
                if ($updateNiche) {
                    $updateNiche->bind_param("s", $data['lot_id']);
                    $updateNiche->execute();
                    $updateNiche->close();
                }
            } else {
                // No niche_number - update status in tbl_plots to 'occupied'
                $updatePlot = $conn->prepare("UPDATE tbl_plots SET status = 'occupied' WHERE plot_id = (SELECT plot_id FROM tbl_lot WHERE lot_id = ?)");
                if ($updatePlot) {
                    $updatePlot->bind_param("s", $data['lot_id']);
                    $updatePlot->execute();
                    $updatePlot->close();
                }
            }
        } else {
            $nicheCheck->close();
        }
    }

    echo json_encode(["success" => true, "message" => "Deceased record created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create deceased record"]);
}

$insert->close();
$conn->close();
?>