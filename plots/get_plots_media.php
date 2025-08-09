<?php
include __DIR__ . '/../config.php';

// Try to get plot_id from JSON body or POST form data
$data = json_decode(file_get_contents('php://input'), true);
$plotId = null;
if (isset($data['plot_id'])) {
    $plotId = $data['plot_id'];
} elseif (isset($_POST['plot_id'])) {
    $plotId = $_POST['plot_id'];
}

if (!$plotId) {
    echo json_encode(["success" => false, "message" => "Invalid id or missing plot_id"]);
    exit();
}

$stmt = $conn->prepare("
SELECT m.file_name, c.first_name, c.last_name, c.email, c.contact_number
FROM tbl_lot l
JOIN tbl_customers c ON l.customer_id = c.customer_id
JOIN tbl_media m ON l.plot_id = m.plot_id
WHERE l.plot_id = ?
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error", "error" => $conn->error]);
    exit();
}

$stmt->bind_param("i", $plotId);

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $fileNames = [];
    $customer = null;

    while ($row = $result->fetch_assoc()) {
        $fileNames[] = $row['file_name'];
        if (!$customer) {
            $customer = [
                "fullname" => $row['first_name'] . ' ' . $row['last_name'],  // combined here
                "email" => $row['email'],
                "contact" => $row['contact']
            ];
        }
    }
    echo json_encode([
        "success" => true,
        "message" => "available plots found",
        "plot_id" => $plotId,
        "file_names" => $fileNames,
        "customer" => $customer
    ]);
} else {
    echo json_encode(["success" => false, "message" => "no available plots found"]);
}
$conn->close();
?>