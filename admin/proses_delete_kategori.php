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

    $id_kategori = $_POST['id_kategori'] ?? null;

    if ($id_kategori) {
        $sql = "UPDATE tbl_kategori SET deleted_at = NOW() WHERE id_kategori = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_kategori);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "Kategori (ID: $id_kategori) berhasil di-soft-delete.";
            } else {
                $response['status'] = 'info';
                $response['message'] = 'Tidak ada data yang diubah (ID tidak ditemukan).';
            }
        } else {
            throw new Exception('Eksekusi database gagal: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("'id_kategori' wajib diisi.");
    }

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>