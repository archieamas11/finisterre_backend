<?php

function create_log($conn, $userIdentifier, $action, $target, $details)
{
    // ⚠️ Early validation to prevent any database errors from affecting main flow
    if (!$conn || !is_object($conn)) {
        error_log("create_log: Invalid database connection provided");
        return ["success" => false, "message" => "Invalid database connection"];
    }

    if (empty($userIdentifier) || empty($action) || empty($target)) {
        error_log("create_log: Missing required parameters");
        return ["success" => false, "message" => "Missing required parameters"];
    }

    $userId = null;

    try {
        if (is_numeric($userIdentifier)) {
            $userId = (int)$userIdentifier;
        } elseif (!empty($userIdentifier)) {
            // Try to find user_id by username
            $ustmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
            if (!$ustmt) {
                error_log("create_log: Failed to prepare user lookup statement: " . $conn->error);
                return ["success" => false, "message" => "Database error preparing user lookup"];
            }

            $ustmt->bind_param("s", $userIdentifier);
            if (!$ustmt->execute()) {
                error_log("create_log: Failed to execute user lookup: " . $ustmt->error);
                $ustmt->close();
                return ["success" => false, "message" => "Database error executing user lookup"];
            }

            // Always use get_result for consistency and better error handling
            $ures = $ustmt->get_result();
            if ($ures && ($row = $ures->fetch_assoc())) {
                $userId = (int)$row['user_id'];
            }
            $ustmt->close();
        }

        if ($userId === null) {
            error_log("create_log: User not found for identifier: " . $userIdentifier);
            return ["success" => false, "message" => "User not found"];
        }

        // 🔐 Prepare and execute the log insertion
        $insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`) VALUES (?, ?, ?, ?)");
        if (!$insert) {
            error_log("create_log: Failed to prepare log insert statement: " . $conn->error);
            return ["success" => false, "message" => "SQL error preparing insert"];
        }

        $insert->bind_param("isss", $userId, $action, $target, $details);
        
        if (!$insert->execute()) {
            $error = $insert->error;
            error_log("create_log: Failed to execute log insert: " . $error);
            $insert->close();
            return ["success" => false, "message" => "Failed to create log", "error" => $error];
        }

        $success = $insert->affected_rows > 0;
        $insert->close();

        if ($success) {
            return ["success" => true, "message" => "Log created successfully"];
        } else {
            error_log("create_log: No rows affected during log insertion");
            return ["success" => false, "message" => "No rows affected during log creation"];
        }

    } catch (Exception $e) {
        error_log("create_log: Exception occurred: " . $e->getMessage());
        return ["success" => false, "message" => "Exception occurred", "error" => $e->getMessage()];
    } catch (Error $e) {
        error_log("create_log: Fatal error occurred: " . $e->getMessage());
        return ["success" => false, "message" => "Fatal error occurred", "error" => $e->getMessage()];
    }
}