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

    $id_kegiatan = $_POST['id_kegiatan'] ?? null;
    $status_baru = $_POST['status_baru'] ?? '';
    $allowed_status = ['Pending', 'Published', 'Rejected', 'Completed', 'Cancelled'];

    if (!$id_kegiatan || !in_array($status_baru, $allowed_status)) {
        throw new Exception("'id_kegiatan' dan 'status_baru' (yang valid) wajib diisi.");
    }

    $sql = "UPDATE tbl_kegiatan SET status_kegiatan = ? WHERE id_kegiatan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status_baru, $id_kegiatan);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = "Status kegiatan (ID: $id_kegiatan) berhasil diubah menjadi '$status_baru'.";
        } else {
            $response['status'] = 'info';
            $response['message'] = 'Tidak ada data yang diubah. (ID tidak ditemukan atau status sudah sama).';
        }
    } else {
        throw new Exception('Eksekusi database gagal: ' . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>