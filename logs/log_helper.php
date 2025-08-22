<?php
// Reusable log helper for inserting activity logs
// Usage: create_log($conn, $userIdentifier, $action, $target, $details)
// userIdentifier: int user_id or string username; the function will resolve username to id if possible.

function create_log($conn, $userIdentifier, $action, $target = null, $details = null) {
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

    // Fallback: null user id allowed (for anonymous logs)
    $insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`, `created_at`) VALUES (?, ?, ?, ?, NOW())");
    if (!$insert) {
        return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
    }

    // Bind types: user_id int or null => use 'i' but provide null as null param via bind_param requires variable
    // We'll coerce null user id to 0 and store 0 when unknown (alternatively use NULL via separate query, but keep simple)
    // Check if userId is null; if so, bind as NULL using a NULL value by using 's' and null string "" in DB will convert depending on schema
    // Better approach: if userId is null, bind as null via mysqli_stmt::bind_param doesn't accept null type directly; use workaround with 'i' and null cast
    if ($userId === null) {
        $uid = null;
        // Use 'isss' and pass null (will insert empty string). If you prefer true NULL, use dynamic query setting to NULL; keep simple and insert empty string for now.
        $insert->bind_param("isss", $uid, $action, $target, $details);
    } else {
        $uid = $userId;
        $insert->bind_param("isss", $uid, $action, $target, $details);
    }

    $executed = $insert->execute();
    if ($executed && $insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "log created"];
    }

    $err = $conn->error;
    $insert->close();
    return ["success" => false, "message" => "failed to create log", "error" => $err];
}

?>