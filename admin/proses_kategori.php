<?php

include '../core/auth_guard.php';
include '../config/db_connect.php';

session_start();

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
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => "Kategori '$nama_kategori' berhasil ditambahkan."
            ];
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
                $_SESSION['notification'] = [
                    'type' => 'success',
                    'message' => "Kategori '$nama_kategori' berhasil diperbarui."
                ];
            } else {
                $_SESSION['notification'] = [
                    'type' => 'info',
                    'message' => 'Tidak ada data yang diubah.'
                ];
            }
        } else {
            throw new Exception('Gagal memperbarui kategori: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception('Aksi tidak valid atau ID Kategori tidak ada untuk update.');
    }

} catch (Exception $e) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
}

$conn->close();
header('Location: manage_kategori.php');
exit;
?>