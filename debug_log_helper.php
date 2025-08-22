<?php
// Enhanced debugging test for log_helper functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== LOG HELPER DEBUG TEST ===\n";

// Test 1: Include the log helper
echo "1. Loading log_helper.php...\n";
try {
    include_once __DIR__ . '/logs/log_helper.php';
    echo "   ✓ Loaded successfully\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check function exists
echo "2. Checking if create_log function exists...\n";
if (function_exists('create_log')) {
    echo "   ✓ Function exists\n";
} else {
    echo "   ✗ Function not found\n";
    exit(1);
}

// Test 3: Load config and try database connection
echo "3. Testing database connection...\n";
try {
    include_once __DIR__ . '/config.php';
    if (isset($conn) && $conn && !$conn->connect_error) {
        echo "   ✓ Database connected successfully\n";
        
        // Test 4: Check if tbl_users exists
        echo "4. Checking if tbl_users table exists...\n";
        $result = $conn->query("SHOW TABLES LIKE 'tbl_users'");
        if ($result && $result->num_rows > 0) {
            echo "   ✓ tbl_users table exists\n";
            
            // Test 5: Check if admin user exists
            echo "5. Checking if admin user exists...\n";
            $stmt = $conn->prepare("SELECT user_id, username FROM tbl_users WHERE username = 'admin' LIMIT 1");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    echo "   ✓ Admin user found with ID: " . $user['user_id'] . "\n";
                    
                    // Test 6: Check if tbl_logs table exists
                    echo "6. Checking if tbl_logs table exists...\n";
                    $logsCheck = $conn->query("SHOW TABLES LIKE 'tbl_logs'");
                    if ($logsCheck && $logsCheck->num_rows > 0) {
                        echo "   ✓ tbl_logs table exists\n";
                        
                        // Test 7: Try calling create_log function
                        echo "7. Testing create_log function...\n";
                        $logResult = create_log($conn, 'admin', 'LOGIN', 'System', 'Debug test log');
                        if ($logResult['success']) {
                            echo "   ✓ Log created successfully: " . $logResult['message'] . "\n";
                        } else {
                            echo "   ✗ Log creation failed: " . $logResult['message'] . "\n";
                            if (isset($logResult['error'])) {
                                echo "      Error details: " . $logResult['error'] . "\n";
                            }
                            if (isset($logResult['error_code'])) {
                                echo "      Error code: " . $logResult['error_code'] . "\n";
                            }
                        }
                    } else {
                        echo "   ✗ tbl_logs table not found\n";
                    }
                } else {
                    echo "   ✗ Admin user not found\n";
                }
                $stmt->close();
            } else {
                echo "   ✗ Failed to prepare admin user check: " . $conn->error . "\n";
            }
        } else {
            echo "   ✗ tbl_users table not found\n";
        }
    } else {
        echo "   ✗ Database connection failed: " . ($conn ? $conn->connect_error : 'Connection object not created') . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Configuration error: " . $e->getMessage() . "\n";
}

echo "=== END DEBUG TEST ===\n";
?>
