<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['penyelenggara']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profil.php");
    exit;
}

$id_penyelenggara = $_SESSION['user_id'];
$nama_organisasi = $_POST['nama_organisasi'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$kontak_email = $_POST['kontak_email'] ?? '';
$kontak_telp = $_POST['kontak_telp'] ?? '';

if (empty($nama_organisasi)) {
    $_SESSION['message'] = "Nama Organisasi tidak boleh kosong.";
    header("Location: profil.php");
    exit;
}

$logo_query = "";
$params = [$nama_organisasi, $deskripsi, $alamat, $kontak_email, $kontak_telp, $id_penyelenggara];
$types = "sssssi";

if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $target_dir = "../uploads/logo_penyelenggara/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    
    if (in_array(strtolower($ext), $allowed_ext)) {
        $nama_file = "org_" . $id_penyelenggara . "_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $nama_file)) {
            $logo_query = ", logo = ?";
            array_splice($params, 5, 0, $nama_file);
            $types = "ssssssi";
        }
    }
}

$sql = "UPDATE tbl_penyelenggara SET nama_organisasi = ?, deskripsi = ?, alamat = ?, kontak_email = ?, kontak_telp = ? $logo_query WHERE id_penyelenggara = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['message'] = "Profil organisasi berhasil diperbarui.";
    $_SESSION['nama'] = $nama_organisasi;
} else {
    $_SESSION['message'] = "Gagal memperbarui profil: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: profil.php");
exit;
?>
