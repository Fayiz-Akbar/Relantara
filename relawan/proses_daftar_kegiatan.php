<?php

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

try {
    checkRole(['relawan']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $id_relawan = $_SESSION['user_id'];
    $id_kegiatan = $_POST['id_kegiatan'] ?? null;
    $alasan_bergabung = $_POST['alasan_bergabung'] ?? '';

    if (empty($id_kegiatan)) {
        throw new Exception('ID Kegiatan wajib diisi.');
    }
    $sql = "INSERT INTO tbl_pendaftaran (id_relawan, id_kegiatan, alasan_bergabung, status_pendaftaran) 
            VALUES (?, ?, ?, 'Pending')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $id_relawan, $id_kegiatan, $alasan_bergabung);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = "Berhasil mendaftar ke kegiatan (ID: $id_kegiatan). Status pendaftaran Anda 'Pending'.";
        $response['new_pendaftaran_id'] = $stmt->insert_id;
    } else {
        if ($conn->errno == 1062) {
            throw new Exception("Anda sudah terdaftar di kegiatan ini.");
        } else {
            throw new Exception("Eksekusi database gagal: " . $stmt->error);
        }
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