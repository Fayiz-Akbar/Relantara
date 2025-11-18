<?php


include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

try {
    checkRole(['penyelenggara']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $id_penyelenggara = $_SESSION['user_id'];
    $id_kegiatan = $_POST['id_kegiatan'] ?? null;

    if (!$id_kegiatan) {
        throw new Exception("'id_kegiatan' wajib diisi.");
    }

    $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() 
            WHERE id_kegiatan = ? AND id_penyelenggara = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_kegiatan, $id_penyelenggara);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = "Kegiatan (ID: $id_kegiatan) berhasil di-soft-delete.";
        } else {
            $response['status'] = 'info';
            $response['message'] = 'Tidak ada data yang diubah (ID tidak ditemukan atau Anda bukan pemilik).';
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