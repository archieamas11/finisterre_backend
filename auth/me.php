<?php
require_once __DIR__ . '/jwt.php';

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$payload = require_auth(false);

// Return basic user info from token
$response = [
    'success' => true,
    'message' => 'Authenticated',
    'user' => [
        'user_id' => $payload->user_id ?? null,
        'username' => $payload->username ?? null,
        'isAdmin' => (bool)($payload->isAdmin ?? false),
        'iat' => $payload->iat ?? null,
        'exp' => $payload->exp ?? null,
    ],
];

echo json_encode($response);
exit();