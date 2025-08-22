<?php
// Simple test endpoint for debugging login with logging
include __DIR__ . '/config.php';
include_once __DIR__ . '/logs/log_helper.php';

header('Content-Type: application/json');

// Simple test to verify login and logging functionality
echo json_encode([
    "message" => "Backend is working",
    "timestamp" => date('Y-m-d H:i:s'),
    "database_connected" => $conn->connect_error ? false : true,
    "log_test" => create_log($conn, 'admin', 'LOGIN', 'System', 'Backend test log')
]);

$conn->close();
?>
