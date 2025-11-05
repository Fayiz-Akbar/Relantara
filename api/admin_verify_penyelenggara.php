<?php
include './api_auth_guard.php'; 
include '../config/db_connect.php';

checkRoleApi(['admin']); 

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penyelenggara = $_POST['id_penyelenggara'] ?? null;
    $status_baru = $_POST['status_baru'] ?? '';

    $allowed_status = ['Verified', 'Rejected'];

    if ($id_penyelenggara && in_array($status_baru, $allowed_status)) {
        
        $sql = "UPDATE tbl_penyelenggara 
                SET status_verifikasi = ? 
                WHERE id_penyelenggara = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_penyelenggara);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $response['status'] = 'success';
                $response['message'] = "Status penyelenggara (ID: $id_penyelenggara) berhasil diubah menjadi '$status_baru'.";
            } else {
                $response['message'] = 'Tidak ada data yang diubah. ID tidak ditemukan.';
            }
        } else {
            http_response_code(500);
            $response['message'] = 'Eksekusi database gagal: ' . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(400);
        $response['message'] = "Input tidak valid. Pastikan 'id_penyelenggara' dan 'status_baru' ('Verified' atau 'Rejected') dikirim.";
    }
} else {
    http_response_code(405);
    $response['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

mysqli_close($conn);
echo json_encode($response);
?>