<?php
include __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');
// Require a valid JWT before proceeding
// require_once __DIR__ . '/../auth/jwt.php';
// require_auth(false);

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$requestedId = isset($data['id']) ? trim((string)$data['id']) : null;

// Build SQL with optional filter by customer_id
$sql = "SELECT
    c.*,
    l.lot_id,
    l.niche_number,
    l.plot_id,
    p.block,
    p.plot_id AS lot_plot_id,
    p.category,
    d.deceased_id,
    d.dead_fullname,
    d.dead_date_death,
    d.dead_interment
FROM tbl_customers AS c
LEFT JOIN tbl_lot AS l 
    ON c.customer_id = l.customer_id
LEFT JOIN tbl_plots AS p 
    ON p.plot_id = l.plot_id
LEFT JOIN tbl_deceased AS d
    ON l.lot_id = d.lot_id
WHERE c.isArchive != 1";

if ($requestedId !== null && $requestedId !== '') {
    $sql .= " AND c.customer_id = ?";
}

$sql .= ";";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL prepare error", "error" => $conn->error]);
    exit();
}

if ($requestedId !== null && $requestedId !== '') {
    $stmt->bind_param('s', $requestedId);
}

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "SQL execute error", "error" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $customersById = [];

    while ($row = $result->fetch_assoc()) {
        $cid = $row['customer_id'];

        if (!isset($customersById[$cid])) {
            // Start with all customer columns, remove flat lot columns
            $customer = $row;
            unset($customer['lot_id'], $customer['niche_number'], $customer['plot_id'], $customer['block'], $customer['lot_plot_id'], $customer['category'], $customer['deceased_id'], $customer['dead_fullname'], $customer['dead_date_death'], $customer['dead_interment']);
            $customer['lot_info'] = [];
            $customersById[$cid] = $customer;
        }

        $hasGrave = isset($row['lot_plot_id']) && $row['lot_plot_id'] !== null && $row['lot_plot_id'] !== '';
        $hasNiche = isset($row['niche_number']) && $row['niche_number'] !== null && $row['niche_number'] !== '';

        if ($hasGrave || $hasNiche) {
            // Check if this lot already exists in the array
            $lotExists = false;
            $lotIndex = -1;
            foreach ($customersById[$cid]['lot_info'] as $index => $existingLot) {
                if ($existingLot['plot_id'] == $row['plot_id'] && $existingLot['niche_number'] == $row['niche_number']) {
                    $lotExists = true;
                    $lotIndex = $index;
                    break;
                }
            }

            if (!$lotExists) {
                $lotInfo = [
                    'niche_number' => $row['niche_number'],
                    'plot_id' => isset($row['plot_id']) && $row['plot_id'] !== '' ? (int)$row['plot_id'] : null,
                    'block' => $row['block'],
                    'lot_plot_id' => isset($row['lot_plot_id']) && $row['lot_plot_id'] !== '' ? (int)$row['lot_plot_id'] : null,
                    'category' => $row['category'],
                    'deceased_info' => []
                ];
                $customersById[$cid]['lot_info'][] = $lotInfo;
                $lotIndex = count($customersById[$cid]['lot_info']) - 1;
            }

            // Add deceased information if it exists
            if (isset($row['deceased_id']) && $row['deceased_id'] !== null && $row['deceased_id'] !== '') {
                $deceasedInfo = [
                    'deceased_id' => $row['deceased_id'],
                    'dead_fullname' => $row['dead_fullname'],
                    'dead_date_death' => $row['dead_date_death'],
                    'dead_interment' => $row['dead_interment']
                ];
                
                // Check if this deceased record is already added
                $deceasedExists = false;
                foreach ($customersById[$cid]['lot_info'][$lotIndex]['deceased_info'] as $existingDeceased) {
                    if ($existingDeceased['deceased_id'] == $row['deceased_id']) {
                        $deceasedExists = true;
                        break;
                    }
                }
                
                if (!$deceasedExists) {
                    $customersById[$cid]['lot_info'][$lotIndex]['deceased_info'][] = $deceasedInfo;
                }
            }
        }
    }

    $customers = array_values($customersById);
    echo json_encode(["success" => true, "message" => "Customers found", "customers" => $customers]);
} else {
    echo json_encode(["success" => false, "message" => "Customer not found", "customers" => []]);
}

$stmt->close();
$conn->close();
?>