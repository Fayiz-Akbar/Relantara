<?php
include './api_auth_guard.php';
include '../config/db_connect.php';

checkRoleApi(['admin']);

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $tipe_user = $_POST['tipe_user'] ?? ''; // Wajib: 'relawan' atau 'penyelenggara'

    $allowed_types = ['relawan', 'penyelenggara'];

    if ($user_id && in_array($tipe_user, $allowed_types)) {
        
        $tabel = ($tipe_user === 'relawan') ? 'tbl_relawan' : 'tbl_penyelenggara';
        $kolom_id = ($tipe_user === 'relawan') ? 'id_relawan' : 'id_penyelenggara';

        $sql = "UPDATE $tabel SET deleted_at = NOW() WHERE $kolom_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 'success';
            $response['message'] = "User $tipe_user (ID: $user_id) berhasil di-suspend (soft delete).";
        } else {
            http_response_code(500);
            $response['message'] = 'Eksekusi database gagal.';
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(400);
        $response['message'] = "'user_id' dan 'tipe_user' ('relawan' atau 'penyelenggara') wajib diisi.";
    }
} else {
    http_response_code(405);
    $response['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

echo json_encode($response);
mysqli_close($conn);
?>