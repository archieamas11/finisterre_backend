<?php
// test_log_production.php - Minimal test for production debugging
// Add this file to your production server to test the log_helper functionality

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep this 0 for production
ini_set('log_errors', 1);

$response = [
    'success' => false,
    'message' => 'Starting test...',
    'tests' => [],
    'errors' => []
];

try {
    // Test 1: Include config
    $response['tests']['config_loaded'] = false;
    include_once __DIR__ . '/config.php';
    $response['tests']['config_loaded'] = true;

    // Test 2: Database connection
    $response['tests']['db_connected'] = false;
    if (isset($conn) && $conn && !$conn->connect_error) {
        $response['tests']['db_connected'] = true;
        
        // Test 3: Check charset
        $response['tests']['charset_set'] = $conn->set_charset("utf8mb4");
        
        // Test 4: Load log helper
        $response['tests']['log_helper_loaded'] = false;
        include_once __DIR__ . '/logs/log_helper.php';
        $response['tests']['log_helper_loaded'] = function_exists('create_log');
        
        if ($response['tests']['log_helper_loaded']) {
            // Test 5: Check tables exist
            $userTableCheck = $conn->query("SHOW TABLES LIKE 'tbl_users'");
            $response['tests']['tbl_users_exists'] = ($userTableCheck && $userTableCheck->num_rows > 0);
            
            $logTableCheck = $conn->query("SHOW TABLES LIKE 'tbl_logs'");
            $response['tests']['tbl_logs_exists'] = ($logTableCheck && $logTableCheck->num_rows > 0);
            
            // Test 6: Check admin user
            $response['tests']['admin_user_exists'] = false;
            $adminCheck = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = 'admin' LIMIT 1");
            if ($adminCheck) {
                $adminCheck->execute();
                $adminResult = $adminCheck->get_result();
                $response['tests']['admin_user_exists'] = ($adminResult->num_rows > 0);
                $adminCheck->close();
                
                // Test 7: Try to create a test log
                if ($response['tests']['admin_user_exists']) {
                    $response['tests']['log_creation_attempted'] = true;
                    $logResult = create_log($conn, 'admin', 'LOGIN', 'System', 'Production test log - ' . date('Y-m-d H:i:s'));
                    $response['tests']['log_created'] = $logResult['success'] ?? false;
                    $response['tests']['log_result'] = $logResult;
                }
            } else {
                $response['errors'][] = 'Failed to prepare admin check: ' . $conn->error;
            }
        }
        
        $conn->close();
    } else {
        $response['errors'][] = 'Database connection failed: ' . ($conn ? $conn->connect_error : 'Connection object not created');
    }
    
    // Determine overall success
    $allCriticalTestsPassed = $response['tests']['config_loaded'] && 
                            $response['tests']['db_connected'] && 
                            $response['tests']['log_helper_loaded'] && 
                            $response['tests']['tbl_users_exists'] && 
                            $response['tests']['tbl_logs_exists'];
    
    if ($allCriticalTestsPassed) {
        $response['success'] = true;
        $response['message'] = 'All critical tests passed';
    } else {
        $response['message'] = 'Some critical tests failed';
    }
    
} catch (Exception $e) {
    $response['errors'][] = 'Exception: ' . $e->getMessage();
    $response['message'] = 'Test failed with exception';
} catch (Error $e) {
    $response['errors'][] = 'Fatal Error: ' . $e->getMessage();
    $response['message'] = 'Test failed with fatal error';
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
