<?php
// log_helper.php
function create_log($conn, $userIdentifier, $action, $target, $details)
{
    // ⚠️ Add explicit error reporting for debugging
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    
    // ⚠️ Check if connection is valid
    if (!$conn || $conn->connect_error) {
        error_log("create_log: Database connection error - " . ($conn ? $conn->connect_error : 'Connection is null'));
        return ["success" => false, "message" => "Database connection error"];
    }

    $userId = null;

    if (is_numeric($userIdentifier)) {
        $userId = (int)$userIdentifier;
    } elseif (!empty($userIdentifier)) {
        // Try to find user_id by username
        $ustmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
        if (!$ustmt) {
            error_log("create_log: Failed to prepare user lookup statement - " . $conn->error);
            return ["success" => false, "message" => "Failed to prepare user lookup"];
        }
        
        if (!$ustmt->bind_param("s", $userIdentifier)) {
            error_log("create_log: Failed to bind parameter for user lookup - " . $ustmt->error);
            $ustmt->close();
            return ["success" => false, "message" => "Failed to bind user lookup parameter"];
        }
        
        if (!$ustmt->execute()) {
            error_log("create_log: Failed to execute user lookup - " . $ustmt->error);
            $ustmt->close();
            return ["success" => false, "message" => "Failed to execute user lookup"];
        }
        
        $ures = $ustmt->get_result();
        if (!$ures) {
            error_log("create_log: Failed to get result from user lookup - " . $ustmt->error);
            $ustmt->close();
            return ["success" => false, "message" => "Failed to get user lookup result"];
        }
        
        if ($row = $ures->fetch_assoc()) {
            $userId = $row['user_id'];
        }
        $ustmt->close();
    }

    if ($userId === null) {
        error_log("create_log: User not found for identifier: " . $userIdentifier);
        return ["success" => false, "message" => "User not found"];
    }

    // ⚠️ Validate action is allowed
    $allowedActions = ['ADD', 'UPDATE', 'DELETE', 'LOGIN'];
    if (!in_array($action, $allowedActions, true)) {
        error_log("create_log: Invalid action '$action'. Allowed: " . implode(', ', $allowedActions));
        return ["success" => false, "message" => "Invalid action"];
    }

    // ⚠️ Remove problematic table existence check that might cause issues on production
    // Production environments sometimes have restricted permissions for SHOW TABLES or information_schema
    
    // ⚠️ Sanitize inputs to prevent encoding issues
    $action = trim($action);
    $target = trim($target);
    $details = trim($details);
    
    // ⚠️ Ensure proper encoding
    if (function_exists('mb_convert_encoding')) {
        $target = mb_convert_encoding($target, 'UTF-8', 'UTF-8');
        $details = mb_convert_encoding($details, 'UTF-8', 'UTF-8');
    }

    $insert = $conn->prepare("INSERT INTO tbl_logs (user_id, action, target, details) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        error_log("create_log: Failed to prepare insert statement - " . $conn->error);
        return ["success" => false, "message" => "SQL error preparing insert"];
    }

    if (!$insert->bind_param("isss", $userId, $action, $target, $details)) {
        error_log("create_log: Failed to bind parameters for insert - " . $insert->error);
        $insert->close();
        return ["success" => false, "message" => "Failed to bind insert parameters"];
    }

    $executed = $insert->execute();
    
    if (!$executed) {
        $err = $conn->error;
        $insertError = $insert->error;
        error_log("create_log: Execute failed - Connection Error: '$err', Insert Error: '$insertError'");
        $insert->close();
        return ["success" => false, "message" => "Failed to execute insert"];
    }
    
    if ($insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "log created"];
    }

    error_log("create_log: No rows affected - this shouldn't happen if execute() succeeded");
    $insert->close();
    return ["success" => false, "message" => "No rows were inserted"];
}