<?php
include __DIR__ . '/../config.php';
// Require a valid JWT before proceeding
require_once __DIR__ . '/../auth/jwt.php';
require_auth(false);
header('Content-Type: application/json; charset=utf-8');

// Compute total chambers capacity using SQL to avoid PHP loops
$totalSql = "SELECT SUM(COALESCE(CAST(`rows` AS UNSIGNED), 0) * COALESCE(CAST(`columns` AS UNSIGNED), 0)) AS total FROM tbl_plots WHERE category = 'chambers'";
$totalRes = $conn->query($totalSql);
$totalPlots = 0;
if ($totalRes && $row = $totalRes->fetch_assoc()) {
    $totalPlots = (int)($row['total'] ?? 0);
}

// Count occupied niches only within chamber plots
$occupiedSql = "SELECT COUNT(*) AS occupied FROM tbl_lot l JOIN tbl_plots p ON p.plot_id = l.plot_id WHERE p.category = 'chambers' AND l.niche_status = 'occupied'";
$occRes = $conn->query($occupiedSql);
$occupiedPlots = 0;
if ($occRes && $row = $occRes->fetch_assoc()) {
    $occupiedPlots = (int)($row['occupied'] ?? 0);
}

// Count reserved niches only within chamber plots
$reservedSql = "SELECT COUNT(*) AS reserved FROM tbl_lot l JOIN tbl_plots p ON p.plot_id = l.plot_id WHERE p.category = 'chambers' AND l.niche_status = 'reserved'";
$resRes = $conn->query($reservedSql);
$reservedPlots = 0;
if ($resRes && $row = $resRes->fetch_assoc()) {
    $reservedPlots = (int)($row['reserved'] ?? 0);
}

// Derive available ensuring non-negative
$availablePlots = max(0, $totalPlots - $occupiedPlots - $reservedPlots);

echo json_encode([
    'success'   => true,
    'message'   => 'Chambers stats computed',
    'total'     => $totalPlots,
    'available' => $availablePlots,
    'occupied'  => $occupiedPlots,
    'reserved'  => $reservedPlots,
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>