<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401); 
    echo json_encode([
        'status' => 'unauthorized',
        'message' => 'Akses ditolak. Anda harus login untuk mengakses endpoint ini.'
    ]);
    exit;
}

function checkRoleApi($allowed_roles = []) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'forbidden',
            'message' => 'Akses ditolak. Peran Anda (' . htmlspecialchars($_SESSION['role']) . ') tidak memiliki izin.'
        ]);
        exit;
    }
}
?>