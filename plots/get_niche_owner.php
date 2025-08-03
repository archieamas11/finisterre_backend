<?php
include __DIR__ . '/../config.php';
$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT
    n.*,
    c.customer_id,
    CONCAT(
        c.first_name,
        ' ',
        c.middle_name,
        ' ',
        c.last_name
    ) AS customer_name,
    c.email,
    c.contact_number,
    d.deceased_id,
    d.dead_fullname,
    d.dead_birth_date,
    d.dead_date_death,
    d.dead_interment
FROM
    tbl_lot AS l
JOIN tbl_customers AS c
    ON c.customer_id = l.customer_id
LEFT JOIN tbl_niche AS n
    ON l.niche_id = n.niche_id
LEFT JOIN tbl_deceased AS d
    ON d.deceased_id = l.lot_id
WHERE l.niche_id IS NOT NULL");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $LotOwners = [];
    while ($row = $result->fetch_assoc()) {
        $LotOwners[] = $row;
    }
    echo json_encode(["success" => true, "message" => "lot data found", "lotOwners" => $LotOwners]);
} else {
    echo json_encode(["success" => false, "message" => "lot not found"]);
}
$conn->close();
?>