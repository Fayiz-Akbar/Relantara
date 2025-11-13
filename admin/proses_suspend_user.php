<?php

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

try {
    checkRole(['admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $user_id = $_POST['user_id'] ?? null;
    $tipe_user = $_POST['tipe_user'] ?? '';
    $allowed_types = ['relawan', 'penyelenggara'];

    if ($user_id && in_array($tipe_user, $allowed_types)) {
        
        $tabel = ($tipe_user === 'relawan') ? 'tbl_relawan' : 'tbl_penyelenggara';
        $kolom_id = ($tipe_user === 'relawan') ? 'id_relawan' : 'id_penyelenggara';

        $sql = "UPDATE $tabel SET deleted_at = NOW() WHERE $kolom_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "User $tipe_user (ID: $user_id) berhasil di-suspend (soft delete).";
            } else {
                $response['status'] = 'info';
                $response['message'] = 'Tidak ada data yang diubah. User mungkin sudah di-suspend atau ID tidak ditemukan.';
            }
        } else {
            throw new Exception('Eksekusi database gagal: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("'user_id' dan 'tipe_user' ('relawan' atau 'penyelenggara') wajib diisi.");
    }

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>