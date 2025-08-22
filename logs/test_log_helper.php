<?php
// ⚡️ Test script for log_helper.php
include __DIR__ . '/../config.php';
include_once __DIR__ . '/../logs/log_helper.php';

echo "Testing log_helper.php functionality...\n\n";

// Test 1: Valid user ID
echo "Test 1: Valid user ID (2)\n";
$result1 = create_log($conn, 2, 'ADD', 'Test Target', 'Test logging with valid user ID');
echo "Result: " . json_encode($result1, JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Valid username
echo "Test 2: Valid username (assuming 'admin' exists)\n";
$result2 = create_log($conn, 'admin', 'UPDATE', 'Test Target', 'Test logging with valid username');
echo "Result: " . json_encode($result2, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Invalid user ID
echo "Test 3: Invalid user ID (99999)\n";
$result3 = create_log($conn, 99999, 'DELETE', 'Test Target', 'Test logging with invalid user ID');
echo "Result: " . json_encode($result3, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Invalid username
echo "Test 4: Invalid username ('nonexistent')\n";
$result4 = create_log($conn, 'nonexistent', 'LOGIN', 'Test Target', 'Test logging with invalid username');
echo "Result: " . json_encode($result4, JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Null user identifier
echo "Test 5: Null user identifier\n";
$result5 = create_log($conn, null, 'ADD', 'Test Target', 'Test logging with null user');
echo "Result: " . json_encode($result5, JSON_PRETTY_PRINT) . "\n\n";

// Test 6: Missing action
echo "Test 6: Missing action\n";
$result6 = create_log($conn, 2, '', 'Test Target', 'Test logging with empty action');
echo "Result: " . json_encode($result6, JSON_PRETTY_PRINT) . "\n\n";

$conn->close();
echo "Test completed!\n";
?>
