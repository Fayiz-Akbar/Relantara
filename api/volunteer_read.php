<?php

header('Content-Type: application/json');
include '../config/db_connect.php';
$sql = "SELECT * FROM tbl_volunteer ORDER BY tanggal_posting DESC";


$result = mysqli_query($conn, $sql);

$volunteers = [];

if ($result && mysqli_num_rows($result) > 0) {
    
    while ($row = mysqli_fetch_assoc($result)) {
        
        $volunteers[] = $row;
    }

    
    echo json_encode([
        'status' => 'success',
        'message' => 'Data volunteer berhasil diambil',
        'data' => $volunteers
    ]);

} else {
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak ditemukan atau terjadi kesalahan',
        'data' => [] 
    ]);
}


mysqli_close($conn);

?>