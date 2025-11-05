<?php
header('Content-Type: application/json');
include '../config/auth_check.php';
include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul = $_POST['judul'] ?? '';
    $penyelenggara = $_POST['penyelenggara'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
    $link_pendaftaran = $_POST['link_pendaftaran'] ?? '';
    $tanggal_create = date('Y-m-d');

    $penyelenggara_safe = preg_replace('/[^\w\-]+/', '_', $penyelenggara);
    $penyelenggara_safe = trim($penyelenggara_safe, '_');

    if (empty($penyelenggara_safe)) {
        $penyelenggara_safe = 'event';
    }

    $nama_file_gambar = null; 
    if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] == 0) {
        
        $target_dir = "../uploads/";
        $ekstensi = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
        $nama_file_gambar = "poster_" . $penyelenggara_safe . "_" . $tanggal_create . "." . $ekstensi;
        $target_file = $target_dir . $nama_file_gambar;

        if (file_exists($target_file)) {
            $nama_file_gambar = "poster_" . $penyelenggara_safe . "_" . $tanggal_create . "_" . uniqid() . "." . $ekstensi;
            $target_file = $target_dir . $nama_file_gambar;
        }

        if (!move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
            $response['message'] = 'Gagal mengupload gambar poster.';
            echo json_encode($response);
            exit; 
        }
    }

    $sql = "INSERT INTO tbl_volunteer (
                judul, penyelenggara, deskripsi, lokasi, 
                tanggal_mulai, tanggal_selesai, link_pendaftaran, gambar_poster
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

 
    mysqli_stmt_bind_param($stmt, "ssssssss",
        $judul,
        $penyelenggara,
        $deskripsi,
        $lokasi,
        $tanggal_mulai,
        $tanggal_selesai,
        $link_pendaftaran,
        $nama_file_gambar
    );

    if (mysqli_stmt_execute($stmt)) {
        $response['status'] = 'success';
        $response['message'] = 'Data volunteer baru berhasil ditambahkan.';
        $response['inserted_id'] = mysqli_insert_id($conn);
    } else {
        $response['message'] = 'Gagal mengeksekusi kueri: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);

} else {
    $response['message'] = 'Metode request tidak valid. Harap gunakan POST.';
}
mysqli_close($conn);
echo json_encode($response);

?>