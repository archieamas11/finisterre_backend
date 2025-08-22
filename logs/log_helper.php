<?php
include_once __DIR__ . '/../format-utils.php';
function create_log($conn, $userIdentifier, $action, $target = null, $details = null)
{
    // Resolve user id if username provided
    $userId = null;
    if (is_numeric($userIdentifier)) {
        $userId = (int)$userIdentifier;
    } elseif (!empty($userIdentifier)) {
        // Try to find user_id by username without relying on get_result()
        $ustmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
        if ($ustmt) {
            $ustmt->bind_param("s", $userIdentifier);
            $ustmt->execute();
            $fetchedUserId = null;
            $ustmt->bind_result($fetchedUserId);
            if ($ustmt->fetch()) {
                $userId = (int)$fetchedUserId;
            }
            $ustmt->close();
        }
    }

    // 🔐 Security: If user not found, skip logging to prevent database errors
    if ($userId === null) {
        return ["success" => false, "message" => "User not found - cannot create log"];
    }

    // Format target/details - skip formatting to avoid unnecessary overhead in logging
    $target = $target ?? '';
    $details = $details ?? '';

    // Insert log with valid user_id
    $insertSql = "INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`, `created_at`) VALUES (?, ?, ?, ?, NOW())";
    $insert = $conn->prepare($insertSql);
    if (!$insert) {
        return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
    }
    
    $insert->bind_param("isss", $userId, $action, $target, $details);
    $executed = $insert->execute();
    
    if ($executed && $insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "log created"];
    }

    $err = $conn->error;
    $insert->close();
    return ["success" => false, "message" => "failed to create log", "error" => $err];
}