<?php
include './api_auth_guard.php'; 
include '../config/db_connect.php'; 

checkRoleApi(['admin']); 

$sql = "SELECT id_penyelenggara, nama_organisasi, email, deskripsi, tanggal_daftar 
        FROM tbl_penyelenggara 
        WHERE status_verifikasi = 'Pending' 
        ORDER BY tanggal_daftar ASC"; 

$result = mysqli_query($conn, $sql);
$organizers = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $organizers[] = $row;
    }
    echo json_encode([
        'status' => 'success',
        'message' => 'Data penyelenggara pending berhasil diambil.',
        'data' => $organizers
    ]);
} else {
    http_response_code(500); 
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>