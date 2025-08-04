<?php
include __DIR__ . '/../config.php';
$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT 
  d.*, 
  CONCAT(c.first_name, ' ', c.last_name) AS full_name,
  p.block,
  p.category,
  l.*
FROM tbl_deceased AS d
LEFT JOIN tbl_lot AS l ON d.lot_id = l.lot_id
LEFT JOIN tbl_customers AS c ON l.customer_id = c.customer_id
LEFT JOIN tbl_plots AS p ON p.plot_id = l.plot_id;
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $deceased = [];
    while ($row = $result->fetch_assoc()) {
        $deceased[] = $row;
    }
    echo json_encode(["success" => true, "message" => "deceased records found", "deceased" => $deceased]);
} else {
    echo json_encode(["success" => false, "message" => "no deceased records found"]);
}
$conn->close();
?>