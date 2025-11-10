<?php
include '../core/auth_guard.php';
checkRole(['admin']);
include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    header("Location: manage_kategori.php");
    exit;
}

$action = $_POST['action'];
$nama_kategori = $_POST['nama_kategori'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';

if (empty($nama_kategori)) {
    $_SESSION['message'] = "Nama kategori wajib diisi.";
    header("Location: manage_kategori.php");
    exit;
}

if ($action === 'add') {
    // === PROSES TAMBAH (CREATE) ===
    $sql = "INSERT INTO tbl_kategori (nama_kategori, deskripsi) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nama_kategori, $deskripsi);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Kategori berhasil ditambahkan.";
    } else {
        $_SESSION['message'] = "Gagal menambahkan kategori: " . $stmt->error;
    }
    $stmt->close();

} elseif ($action === 'update' && isset($_POST['id_kategori'])) {
    // === PROSES EDIT (UPDATE) ===
    $id_kategori = $_POST['id_kategori'];
    $sql = "UPDATE tbl_kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nama_kategori, $deskripsi, $id_kategori);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Kategori berhasil diperbarui.";
    } else {
        $_SESSION['message'] = "Gagal memperbarui kategori: " . $stmt->error;
    }
    $stmt->close();

} else {
    $_SESSION['message'] = "Aksi tidak valid.";
}

$conn->close();
header("Location: manage_kategori.php");
exit;
?>