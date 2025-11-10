<?php
/*
 * FILE: admin/proses_verifikasi.php (JSON-API Version)
 * FUNGSI: Memverifikasi penyelenggara dan merespon dengan JSON.
 */

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

try {
    checkRole(['admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $id_penyelenggara = $_POST['id_penyelenggara'] ?? null;
    $status_baru = $_POST['status_baru'] ?? '';
    $allowed_status = ['Verified', 'Rejected'];

    if ($id_penyelenggara && in_array($status_baru, $allowed_status)) {
        
        $sql = "UPDATE tbl_penyelenggara 
                SET status_verifikasi = ? 
                WHERE id_penyelenggara = ? AND status_verifikasi = 'Pending'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status_baru, $id_penyelenggara);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "Status penyelenggara (ID: $id_penyelenggara) berhasil diubah menjadi '$status_baru'.";
            } else {
                $response['status'] = 'info';
                $response['message'] = 'Tidak ada data yang diubah. ID tidak ditemukan atau status bukan Pending.';
            }
        } else {
            throw new Exception('Eksekusi database gagal: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("Input tidak valid. Pastikan 'id_penyelenggara' dan 'status_baru' ('Verified' atau 'Rejected') dikirim.");
    }

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>