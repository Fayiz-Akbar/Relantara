<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    
    header('Content-Type: application/json');
    http_response_code(401); 
    
    echo json_encode([
        'status' => 'unauthorized',
        'message' => 'Akses ditolak. Silakan login terlebih dahulu.'
    ]);
    
    die();
}
?>