<?php
include __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth/jwt.php';
require_auth(false);

$query = "
    SELECT
        plot_id,
        `rows`,
        `columns`
    FROM
        tbl_plots
    WHERE
        `rows` IS NOT NULL AND `rows` <> ''
        AND `columns` IS NOT NULL AND `columns` <> ''
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "SQL error",
        "error" => $conn->error
    ]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $niche = [];
    while ($row = $result->fetch_assoc()) {
        $niche[] = $row;
    }
    echo json_encode([
        "success" => true,
        "message" => "niche data found",
        "plots" => $niche
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "no niche data found"
    ]);
}

$conn->close();
?>