<?php

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

try {
    checkRole(['admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        throw new Exception('Metode tidak diizinkan atau aksi tidak diset.');
    }

    $action = $_POST['action'];
    $nama_kategori = $_POST['nama_kategori'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    if (empty($nama_kategori)) {
        throw new Exception('Nama kategori wajib diisi.');
    }

    if ($action === 'add') {
        $sql = "INSERT INTO tbl_kategori (nama_kategori, deskripsi) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nama_kategori, $deskripsi);
        
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = "Kategori '$nama_kategori' berhasil ditambahkan.";
            $response['new_kategori_id'] = $conn->insert_id;
        } else {
            throw new Exception('Gagal menambahkan kategori: ' . $stmt->error);
        }
        $stmt->close();

    } elseif ($action === 'update' && isset($_POST['id_kategori'])) {
        $id_kategori = $_POST['id_kategori'];
        $sql = "UPDATE tbl_kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nama_kategori, $deskripsi, $id_kategori);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "Kategori (ID: $id_kategori) berhasil diperbarui.";
            } else {
                $response['status'] = 'info';
                $response['message'] = 'Tidak ada data yang diubah.';
            }
        } else {
            throw new Exception('Gagal memperbarui kategori: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception('Aksi tidak valid atau ID Kategori tidak ada untuk update.');
    }

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>