<?php
include './api_auth_guard.php';
include '../config/db_connect.php';

checkRoleApi(['admin']);

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kegiatan = $_POST['id_kegiatan'] ?? null;

    if ($id_kegiatan) {
        $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() WHERE id_kegiatan = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_kegiatan);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 'success';
            $response['message'] = "Kegiatan (ID: $id_kegiatan) berhasil di-soft-delete.";
        } else {
            http_response_code(500);
            $response['message'] = 'Eksekusi database gagal.';
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(400);
        $response['message'] = "'id_kegiatan' wajib diisi.";
    }
} else {
    http_response_code(405);
    $response['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

echo json_encode($response);
mysqli_close($conn);
?>