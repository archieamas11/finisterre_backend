<?php
include_once __DIR__ . '/../format-utils.php';
// Usage: create_log($conn, $userIdentifier, $action, $target, $details)
// userIdentifier: int user_id or string username; the function will resolve username to id if possible.

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

    // Format target/details using shared formatter if available
    $formatted = formatData([
        'target' => $target ?? '',
        'details' => $details ?? ''
    ], [], [], null);
    $target = $formatted['target'] ?? $target;
    $details = $formatted['details'] ?? $details;

    // Fallback: null user id allowed (for anonymous logs)
    // Prepare insert that uses NULL for user_id if unknown (avoids bind_param null edge cases)
    if ($userId === null) {
        $insertSql = "INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`, `created_at`) VALUES (NULL, ?, ?, ?, NOW())";
        $insert = $conn->prepare($insertSql);
        if (!$insert) {
            return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
        }
        $insert->bind_param("sss", $action, $target, $details);
    } else {
        $insertSql = "INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`, `created_at`) VALUES (?, ?, ?, ?, NOW())";
        $insert = $conn->prepare($insertSql);
        if (!$insert) {
            return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
        }
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