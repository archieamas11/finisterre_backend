<?php
include __DIR__ . '/../config.php';
// // Require a valid JWT before proceeding
// require_once __DIR__ . '/../auth/jwt.php';
// require_auth(false);
$data = json_decode(file_get_contents('php://input'), true);

$plot_id = $data['plot_id'] ?? null;
if (!$plot_id) {
    echo json_encode(["success" => false, "message" => "Missing plot_id"]);
    exit();
}

$stmt = $conn->prepare("SELECT
    l.niche_number,
    l.niche_status,
    l.lot_id,
    p.`rows`,
    p.`columns`,
    c.customer_id,
    CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name) AS customer_name,
    c.email,
    c.contact_number,
    d.deceased_id,
    d.dead_fullname,
    d.dead_birth_date,
    d.dead_date_death,
    d.dead_interment
FROM
    tbl_lot l
LEFT JOIN tbl_customers c ON c.customer_id = l.customer_id
LEFT JOIN tbl_deceased d ON d.lot_id = l.lot_id
LEFT JOIN tbl_plots p ON p.plot_id = l.plot_id
WHERE l.plot_id = ?
ORDER BY p.rows, p.columns
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->bind_param("i", $plot_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $nicheData = [];
    while ($row = $result->fetch_assoc()) {
        $nicheData[] = $row;
    }
    echo json_encode(["success" => true, "message" => "nicheData found", "nicheData" => $nicheData]);
} else {
    echo json_encode(["success" => false, "message" => "nicheData not found"]);
}
$conn->close();
?>