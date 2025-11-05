<?php
include './api_auth_guard.php';
include '../config/db_connect.php';

checkRoleApi(['admin']);

$status_filter = $_GET['status'] ?? 'all'; 

$sql = "SELECT id_kegiatan, id_penyelenggara, judul, status_kegiatan, created_at 
        FROM tbl_kegiatan 
        WHERE deleted_at IS NULL"; 

if ($status_filter !== 'all') {
    $sql .= " AND status_kegiatan = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $status_filter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

$kegiatan = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $kegiatan[] = $row;
    }
    echo json_encode([
        'status' => 'success',
        'filter' => $status_filter,
        'data' => $kegiatan
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data.']);
}

if (isset($stmt)) mysqli_stmt_close($stmt);
mysqli_close($conn);
?>