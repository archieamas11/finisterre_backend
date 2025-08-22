<?php
// Usage: create_log($conn, $userIdentifier, $action, $target, $details)
// userIdentifier: int user_id or string username; the function will resolve username to id if possible.

function create_log($conn, $userIdentifier, $action, $target = null, $details = null)
{
    // Resolve user id if username provided
    $userId = null;
    if (is_numeric($userIdentifier)) {
        $userId = (int)$userIdentifier;
    } elseif (!empty($userIdentifier)) {
        // Try to find user_id by username
        $ustmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
        if ($ustmt) {
            $ustmt->bind_param("s", $userIdentifier);
            $ustmt->execute();
            $ures = $ustmt->get_result();
            if ($row = $ures->fetch_assoc()) {
                $userId = (int)$row['user_id'];
            }
            $ustmt->close();
        }
    }

    // Handle null user_id: since database user_id is NOT NULL, use a default system user
    if ($userId === null) {
        // Use a default system/anonymous user ID (you can create a dedicated system user)
        // For now, using the admin user ID (2) as fallback - you may want to create a dedicated system user
        $userId = 2; // or create a system user with ID 1
    }

    // Prepare statement - let MySQL handle the timestamp
    $insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
    }

    // Bind parameters: user_id(int), action(string), target(string), details(string)
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