<?php
// Test script to check login functionality
include 'config.php';
include 'logs/log_helper.php';

echo "Testing database connection...\n";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
} else {
    echo "Database connected successfully!\n";
}

echo "\nTesting user lookup...\n";
$username = 'admin';
$stmt = $conn->prepare("SELECT user_id, password, isAdmin FROM tbl_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "User found: ID=" . $user['user_id'] . ", isAdmin=" . $user['isAdmin'] . "\n";
    
    // Test password verification
    $password = 'admin';
    if (password_verify($password, $user['password'])) {
        echo "Password verification: SUCCESS\n";
        
        // Test logging function
        echo "\nTesting log function...\n";
        echo "Calling create_log with params:\n";
        echo "  userIdentifier: '$username'\n";
        echo "  action: 'LOGIN'\n";
        echo "  target: 'System'\n";
        echo "  details: 'Test log entry'\n\n";
        
        $logResult = create_log($conn, $username, 'LOGIN', 'System', 'Test log entry');
        echo "Log result: " . json_encode($logResult, JSON_PRETTY_PRINT) . "\n";
        
        // Also test with user_id directly
        echo "\nTesting log function with user_id directly...\n";
        $logResult2 = create_log($conn, $user['user_id'], 'LOGIN', 'System', 'Test log with user_id');
        echo "Log result 2: " . json_encode($logResult2, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "Password verification: FAILED\n";
    }
} else {
    echo "User not found!\n";
}

$stmt->close();
$conn->close();
echo "\nTest completed.\n";
?>
