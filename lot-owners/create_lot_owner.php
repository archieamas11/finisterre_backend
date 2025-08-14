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
    'customer_id', 'plot_id'
];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        echo json_encode(["success" => false, "message" => "Missing required field: $field"]);
        exit();
    }
}

// Derive niche values: only set status when niche_number is provided, else NULLs
$nicheNumber = (isset($data['niche_number']) && $data['niche_number'] !== '') ? $data['niche_number'] : null;
$nicheStatus = $nicheNumber !== null ? 'reserved' : null;

// If niche_number is empty or null, update status in tbl_plots to "reserved"
if ($nicheNumber === null) {
    $plot_id = $data['plot_id'];
    $update_plot_stmt = $conn->prepare("UPDATE tbl_plots SET status = 'reserved' WHERE plot_id = ?");
    if ($update_plot_stmt) {
        $update_plot_stmt->bind_param("s", $plot_id);
        $update_plot_stmt->execute();
        $update_plot_stmt->close();
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

$insert->bind_param(
    "ssss",
    $data['customer_id'],
    $data['plot_id'],
    $nicheNumber,
    $nicheStatus
);

$insert->execute();

if ($insert->affected_rows > 0) {
    // Get the newly created lot_id
    $lot_id = $conn->insert_id;

    // Retrieve customer first_name and last_name
    $customer_id = $data['customer_id'];
    $customer_stmt = $conn->prepare("SELECT first_name, last_name FROM tbl_customers WHERE customer_id = ? LIMIT 1");
    if ($customer_stmt) {
        $customer_stmt->bind_param("s", $customer_id);
        $customer_stmt->execute();
        $customer_stmt->bind_result($first_name, $last_name);
        if ($customer_stmt->fetch()) {
            $customer_stmt->close();

            // Check if user already exists for this customer_id
            $user_check_stmt = $conn->prepare("SELECT customer_id FROM tbl_users WHERE customer_id = ? LIMIT 1");
            if ($user_check_stmt) {
                $user_check_stmt->bind_param("s", $customer_id);
                $user_check_stmt->execute();
                $user_check_stmt->store_result();
                if ($user_check_stmt->num_rows === 0) {
                    // Create username and password
                    $username = $lot_id;
                    $password_raw = ucfirst(strtolower($last_name)) . '123';
                    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

                    $user_insert_stmt = $conn->prepare("INSERT INTO tbl_users (username, password, customer_id, isAdmin, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                    if ($user_insert_stmt) {
                        $isAdmin = 0;
                        $user_insert_stmt->bind_param("sssi", $username, $password_hashed, $customer_id, $isAdmin);
                        $user_insert_stmt->execute();
                        $user_insert_stmt->close();
                    }
                }
                $user_check_stmt->close();
            }
        } else {
            $customer_stmt->close();
        }
    }
    echo json_encode(["success" => true, "message" => "Lot ownership created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create lot ownership", "error" => $insert->error]);
}

$insert->close();
$conn->close();
?>