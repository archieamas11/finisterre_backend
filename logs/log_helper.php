<?php
function create_log($conn, $userIdentifier, $action, $target, $details)
{
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
            return ["success" => false, "message" => "Failed to prepare user lookup", "error" => $conn->error];
        }
        
        $ustmt->bind_param("s", $userIdentifier);
        if (!$ustmt->execute()) {
            error_log("create_log: Failed to execute user lookup - " . $ustmt->error);
            $ustmt->close();
            return ["success" => false, "message" => "Failed to execute user lookup", "error" => $ustmt->error];
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

    // ⚠️ Validate action is allowed
    $allowedActions = ['ADD', 'UPDATE', 'DELETE', 'LOGIN'];
    if (!in_array($action, $allowedActions)) {
        error_log("create_log: Invalid action '$action'. Allowed: " . implode(', ', $allowedActions));
        return ["success" => false, "message" => "Invalid action"];
    }

    // ⚠️ Check if tbl_logs table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'tbl_logs'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        error_log("create_log: tbl_logs table does not exist");
        return ["success" => false, "message" => "Logs table does not exist"];
    }

    $insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        error_log("create_log: Failed to prepare insert statement - " . $conn->error);
        return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
    }

    $insert->bind_param("isss", $userId, $action, $target, $details);

    $executed = $insert->execute();
    if ($executed && $insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "log created"];
    }

    $err = $conn->error;
    $insertError = $insert->error;
    $insert->close();
    
    // ⚡️ Log the specific error for debugging
    error_log("create_log: Failed to insert log - Connection Error: $err, Insert Error: $insertError");
    
    return ["success" => false, "message" => "failed to create log", "error" => $err, "insert_error" => $insertError];
}