<?php
// log_helper.php
function create_log($conn, $userIdentifier, $action, $target, $details)
{
    // ⚠️ Check if connection is valid
    if (!$conn || $conn->connect_error) {
        error_log("create_log: Database connection error - " . ($conn ? $conn->connect_error : 'Connection is null'));
        return ["success" => false, "message" => "Database connection error"];
    }

    // 🔐 Additional connection health check for production environments
    if (!$conn->ping()) {
        error_log("create_log: Database connection is not responsive (ping failed)");
        return ["success" => false, "message" => "Database connection not responsive"];
    }

    // 🔐 Set charset to utf8mb4 to prevent encoding issues
    if (!$conn->set_charset("utf8mb4")) {
        error_log("create_log: Failed to set charset to utf8mb4 - " . $conn->error);
        return ["success" => false, "message" => "Character set error"];
    }

    $userId = null;

    if (is_numeric($userIdentifier)) {
        $userId = (int)$userIdentifier;
        
        // ⚠️ Verify the user_id exists to prevent foreign key constraint errors
        $userCheck = $conn->prepare("SELECT user_id FROM tbl_users WHERE user_id = ? LIMIT 1");
        if (!$userCheck) {
            error_log("create_log: Failed to prepare user ID verification - " . $conn->error);
            return ["success" => false, "message" => "Failed to prepare user verification"];
        }
        
        $userCheck->bind_param("i", $userId);
        if (!$userCheck->execute()) {
            error_log("create_log: Failed to execute user ID verification - " . $userCheck->error);
            $userCheck->close();
            return ["success" => false, "message" => "Failed to execute user verification"];
        }
        
        $userResult = $userCheck->get_result();
        if ($userResult->num_rows === 0) {
            $userId = null;
        }
        $userCheck->close();
        
    } elseif (!empty($userIdentifier)) {
        // Try to find user_id by username
        $ustmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
        if (!$ustmt) {
            error_log("create_log: Failed to prepare user lookup statement - " . $conn->error);
            return ["success" => false, "message" => "Failed to prepare user lookup"];
        }
        
        $ustmt->bind_param("s", $userIdentifier);
        if (!$ustmt->execute()) {
            error_log("create_log: Failed to execute user lookup - " . $ustmt->error);
            $ustmt->close();
            return ["success" => false, "message" => "Failed to execute user lookup"];
        }
        
        $ures = $ustmt->get_result();
        if ($row = $ures->fetch_assoc()) {
            $userId = $row['user_id'];
        }
        $ustmt->close();
    }

    if ($userId === null) {
        error_log("create_log: User not found for identifier: " . $userIdentifier);
        return ["success" => false, "message" => "User not found"];
    }

    // ⚠️ Validate action matches exactly with ENUM values
    $allowedActions = ['ADD', 'UPDATE', 'DELETE', 'LOGIN'];
    $action = strtoupper(trim($action)); // Normalize action
    if (!in_array($action, $allowedActions, true)) {
        error_log("create_log: Invalid action '$action'. Allowed: " . implode(', ', $allowedActions));
        return ["success" => false, "message" => "Invalid action"];
    }

    // ⚠️ Sanitize and validate input lengths
    $target = trim($target);
    if (strlen($target) > 100) {
        $target = substr($target, 0, 100);
        error_log("create_log: Target truncated to 100 characters");
    }
    
    if (empty($target)) {
        error_log("create_log: Target cannot be empty");
        return ["success" => false, "message" => "Target cannot be empty"];
    }

    // ⚠️ Check if tbl_logs table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'tbl_logs'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        error_log("create_log: tbl_logs table does not exist");
        return ["success" => false, "message" => "Logs table does not exist"];
    }

    // 🛠️ Prepare insert with explicit column order
    $insert = $conn->prepare("INSERT INTO tbl_logs (user_id, action, target, details, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$insert) {
        error_log("create_log: Failed to prepare insert statement - " . $conn->error);
        return ["success" => false, "message" => "SQL error preparing insert"];
    }

    $insert->bind_param("isss", $userId, $action, $target, $details);

    if (!$insert->execute()) {
        $insertError = $insert->error;
        $connError = $conn->error;
        $errno = $conn->errno;
        $insert->close();
        
        // ⚡️ Log specific error details for debugging
        error_log("create_log: Failed to insert log - MySQL Error #$errno: $connError | Insert Error: $insertError");
        
        return ["success" => false, "message" => "Failed to create log", "error_code" => $errno, "error" => $connError];
    }

    if ($insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "Log created"];
    }

    $insert->close();
    error_log("create_log: Insert executed but no rows were affected");
    return ["success" => false, "message" => "No rows affected"];
}