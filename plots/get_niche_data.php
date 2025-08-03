<?php
include __DIR__ . '/../config.php';
$data = json_decode(file_get_contents('php://input'), true);

$plot_id = $data['plot_id'] ?? null;
if (!$plot_id) {
    echo json_encode(["success" => false, "message" => "Missing plot_id"]);
    exit();
}

$stmt = $conn->prepare("SELECT
    n.niche_id,
    n.niche_number,
    n.status,
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
    tbl_niche n
LEFT JOIN tbl_lot l ON l.niche_id = n.niche_id
LEFT JOIN tbl_customers c ON c.customer_id = l.customer_id
LEFT JOIN tbl_deceased d ON d.lot_id = l.lot_id
LEFT JOIN tbl_plots p ON p.plot_id = n.plot_id
WHERE n.plot_id = ?
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