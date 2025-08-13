<?php
include __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

// Total serenity plots = all plots not in chambers or columbarium
$totalSql = "SELECT COUNT(*) AS total FROM tbl_plots WHERE category NOT IN ('chambers', 'columbarium')";
$totalRes = $conn->query($totalSql);
$totalPlots = 0;
if ($totalRes && $row = $totalRes->fetch_assoc()) {
    $totalPlots = (int)($row['total'] ?? 0);
}

// Occupied serenity plots by plot status
$occupiedSql = "SELECT COUNT(*) AS occupied FROM tbl_plots WHERE category NOT IN ('chambers', 'columbarium') AND status = 'occupied'";
$occRes = $conn->query($occupiedSql);
$occupiedPlots = 0;
if ($occRes && $row = $occRes->fetch_assoc()) {
    $occupiedPlots = (int)($row['occupied'] ?? 0);
}

// Reserved serenity plots by plot status
$reservedSql = "SELECT COUNT(*) AS reserved FROM tbl_plots WHERE category NOT IN ('chambers', 'columbarium') AND status = 'reserved'";
$resRes = $conn->query($reservedSql);
$reservedPlots = 0;
if ($resRes && $row = $resRes->fetch_assoc()) {
    $reservedPlots = (int)($row['reserved'] ?? 0);
}

// Derive available ensuring non-negative
$availablePlots = max(0, $totalPlots - $occupiedPlots - $reservedPlots);

echo json_encode([
    'success'   => true,
    'message'   => 'Serenity stats computed',
    'total'     => $totalPlots,
    'available' => $availablePlots,
    'occupied'  => $occupiedPlots,
    'reserved'  => $reservedPlots,
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>