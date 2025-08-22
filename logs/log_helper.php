<?php
include __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

function create_log($conn, $userIdentifier, $action, $target, $details)
{
    $userId = null; // Ensure $userId is defined

    // Resolve user id if username provided
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
                $userId = $row['user_id'];
            }
            $ustmt->close();
        }
    }

    if ($userId === null) {
        return ["success" => false, "message" => "User not found"];
    }

    $insert = $conn->prepare("INSERT INTO tbl_logs (`user_id`, `action`, `target`, `details`) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        return ["success" => false, "message" => "SQL error preparing insert", "error" => $conn->error];
    }

    $insert->bind_param("isss", $userId, $action, $target, $details); // Correct parameter types

    $executed = $insert->execute();
    if ($executed && $insert->affected_rows > 0) {
        $insert->close();
        return ["success" => true, "message" => "log created"];
    }

    $err = $conn->error;
    $insert->close();
    return ["success" => false, "message" => "failed to create log", "error" => $err];
}