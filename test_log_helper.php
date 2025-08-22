<?php
// Test script for log_helper.php
include __DIR__ . '/config.php';
include_once __DIR__ . '/logs/log_helper.php';

echo "Testing log_helper.php functionality...\n\n";

// Test 1: Valid username
echo "Test 1: Creating log with valid username 'admin'\n";
$result1 = create_log($conn, 'admin', 'LOGIN', 'System', 'Test login from admin user');
echo "Result: " . json_encode($result1) . "\n\n";

// Test 2: Valid user_id
echo "Test 2: Creating log with valid user_id 2\n";
$result2 = create_log($conn, 2, 'LOGIN', 'System', 'Test login from user ID 2');
echo "Result: " . json_encode($result2) . "\n\n";

// Test 3: Invalid username (should fallback to admin user ID 2)
echo "Test 3: Creating log with invalid username 'nonexistent'\n";
$result3 = create_log($conn, 'nonexistent', 'LOGIN', 'System', 'Test login from nonexistent user');
echo "Result: " . json_encode($result3) . "\n\n";

// Test 4: Null/empty username (should fallback to admin user ID 2)
echo "Test 4: Creating log with null username\n";
$result4 = create_log($conn, null, 'LOGIN', 'System', 'Test login from null user');
echo "Result: " . json_encode($result4) . "\n\n";

$conn->close();
echo "Tests completed.\n";
?>
