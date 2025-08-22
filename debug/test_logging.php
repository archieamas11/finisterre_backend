<?php
// 🧪 Simple test endpoint to debug logging issues in production
include __DIR__ . '/../config.php';
include_once __DIR__ . '/../logs/log_helper.php';

header('Content-Type: application/json');

// Test database connection
$tests = [];

// Test 1: Check database connection
$tests['database_connection'] = [
    'test' => 'Database Connection',
    'success' => false,
    'message' => '',
    'error' => ''
];

if ($conn) {
    try {
        $testResult = $conn->query("SELECT 1 as test");
        if ($testResult) {
            $tests['database_connection']['success'] = true;
            $tests['database_connection']['message'] = 'Database connection OK';
        } else {
            $tests['database_connection']['error'] = $conn->error;
        }
    } catch (Exception $e) {
        $tests['database_connection']['error'] = $e->getMessage();
    }
} else {
    $tests['database_connection']['error'] = 'No database connection';
}

// Test 2: Check if tbl_users table exists
$tests['users_table'] = [
    'test' => 'Users Table Check',
    'success' => false,
    'message' => '',
    'error' => ''
];

if ($conn) {
    try {
        $result = $conn->query("SELECT COUNT(*) as user_count FROM tbl_users LIMIT 1");
        if ($result) {
            $row = $result->fetch_assoc();
            $tests['users_table']['success'] = true;
            $tests['users_table']['message'] = 'Users table exists with ' . $row['user_count'] . ' users';
        } else {
            $tests['users_table']['error'] = $conn->error;
        }
    } catch (Exception $e) {
        $tests['users_table']['error'] = $e->getMessage();
    }
}

// Test 3: Check if tbl_logs table exists
$tests['logs_table'] = [
    'test' => 'Logs Table Check',
    'success' => false,
    'message' => '',
    'error' => ''
];

if ($conn) {
    try {
        $result = $conn->query("SELECT COUNT(*) as log_count FROM tbl_logs LIMIT 1");
        if ($result) {
            $row = $result->fetch_assoc();
            $tests['logs_table']['success'] = true;
            $tests['logs_table']['message'] = 'Logs table exists with ' . $row['log_count'] . ' logs';
        } else {
            $tests['logs_table']['error'] = $conn->error;
        }
    } catch (Exception $e) {
        $tests['logs_table']['error'] = $e->getMessage();
    }
}

// Test 4: Test create_log function with a known user ID (2)
$tests['log_creation'] = [
    'test' => 'Log Creation Test',
    'success' => false,
    'message' => '',
    'error' => ''
];

if ($conn) {
    try {
        $logResult = create_log($conn, 2, 'TEST', 'Debug Test', 'Testing log creation from debug endpoint');
        $tests['log_creation'] = array_merge($tests['log_creation'], $logResult);
    } catch (Exception $e) {
        $tests['log_creation']['error'] = $e->getMessage();
    }
}

// Additional environment info
$environment_info = [
    'php_version' => phpversion(),
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
    'script_filename' => __FILE__,
    'current_time' => date('Y-m-d H:i:s'),
];

echo json_encode([
    'status' => 'Debug endpoint active',
    'tests' => $tests,
    'environment' => $environment_info
], JSON_PRETTY_PRINT);

if ($conn) {
    $conn->close();
}
?>
