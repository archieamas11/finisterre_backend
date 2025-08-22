<?php
function create_log($conn, $userIdentifier, $action, $target = null, $details = null) {
    // ⚡️ Validate database connection first
    if (!$conn || $conn->connect_error) {
        return ["success" => false, "message" => "Database connection error", "error" => "Invalid connection"];
    }
    
    // 🔐 Validate required parameters
    if (empty($action)) {
        return ["success" => false, "message" => "Action is required", "error" => "Missing action parameter"];
    }
    
    // 🔍 Resolve user id if username provided
    $userId = null;
    if (is_numeric($userIdentifier)) {
        $userId = (int)$userIdentifier;
        
        // ⚠️ Verify user exists in database
        $checkStmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE user_id = ? LIMIT 1");
        if ($checkStmt) {
            $checkStmt->bind_param("i", $userId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                $userId = null; // User doesn't exist
            }
            $checkStmt->close();
        }
    } elseif (!empty($userIdentifier)) {
        // 🔍 Try to find user_id by username
        $ustmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
        if ($ustmt) {
            $ustmt->bind_param("s", $userIdentifier);
            if ($ustmt->execute()) {
                $ures = $ustmt->get_result();
                if ($row = $ures->fetch_assoc()) {
                    $userId = (int)$row['user_id'];
                }
            }
            $ustmt->close();
        }
    }
    
    // ⚠️ If user_id is still null, we cannot create the log due to NOT NULL constraint
    if ($userId === null) {
        return ["success" => false, "message" => "Invalid user - cannot create log without valid user_id", "error" => "User validation failed"];
    }
    
    // 🛠️ Prepare insert statement with proper error handling
    $insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`, `created_at`) VALUES (?, ?, ?, ?, NOW())");
    if (!$insert) {
        return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
    }
    
    // 🔐 Bind parameters and execute
    $insert->bind_param("isss", $userId, $action, $target, $details);
    
    if (!$insert->execute()) {
        $err = $conn->error;
        $insert->close();
        return ["success" => false, "message" => "Failed to execute log insert", "error" => $err];
    }
    
    // ✅ Check if row was actually inserted
    if ($insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "Log created successfully", "log_id" => $conn->insert_id];
    }
    
    $insert->close();
    return ["success" => false, "message" => "No rows affected - log not created", "error" => "Insert failed"];
}

?>