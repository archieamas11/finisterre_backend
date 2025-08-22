<?php
// Test script to debug the logs table issue
include 'config.php';

echo "=== Debugging tbl_logs Issue ===\n\n";

// Check if table exists and show its structure
$result = $conn->query("DESCRIBE tbl_logs");
if ($result) {
    echo "Table structure for tbl_logs:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . ": " . $row['Type'] . " (" . $row['Null'] . ", " . $row['Key'] . ", Default: " . ($row['Default'] ?? 'NULL') . ")\n";
    }
    echo "\n";
} else {
    echo "Error getting table structure: " . $conn->error . "\n";
}

// Test the exact values we're trying to insert
echo "Testing INSERT with exact values:\n";
$userId = 2;
$action = 'LOGIN';
$target = 'System';
$details = 'Admin Logged in';

echo "Values: user_id=$userId, action='$action', target='$target', details='$details'\n\n";

// Try the insert
$insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`) VALUES (?, ?, ?, ?)");
if ($insert) {
    $insert->bind_param("isss", $userId, $action, $target, $details);
    
    if ($insert->execute()) {
        echo "INSERT SUCCESS: affected_rows = " . $insert->affected_rows . "\n";
    } else {
        echo "INSERT FAILED: " . $insert->error . "\n";
    }
    $insert->close();
} else {
    echo "PREPARE FAILED: " . $conn->error . "\n";
}

$conn->close();
echo "\nTest completed.\n";
?>
