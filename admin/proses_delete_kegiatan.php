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

    if ($id_kegiatan) {
        $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() WHERE id_kegiatan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_kegiatan);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "Kegiatan (ID: $id_kegiatan) berhasil di-soft-delete.";
            } else {
                $response['status'] = 'info';
                $response['message'] = 'Tidak ada data yang diubah (ID tidak ditemukan).';
            }
        } else {
            throw new Exception('Eksekusi database gagal: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("'id_kegiatan' wajib diisi.");
    }

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>