<?php
header('Content-Type: application/json');
include '../config/auth_check.php';
include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_volunteer = $_POST['id_volunteer'] ?? null;

    if (!$id_volunteer) {
        $response['message'] = 'ID Volunteer tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    $judul = $_POST['judul'] ?? '';
    $penyelenggara = $_POST['penyelenggara'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
    $link_pendaftaran = $_POST['link_pendaftaran'] ?? '';

    $sql_select_old = "SELECT gambar_poster FROM tbl_volunteer WHERE id_volunteer = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select_old);
    mysqli_stmt_bind_param($stmt_select, "i", $id_volunteer);
    mysqli_stmt_execute($stmt_select);
    $result_old = mysqli_stmt_get_result($stmt_select);
    $data_old = mysqli_fetch_assoc($result_old);
    
    $nama_file_gambar = $data_old['gambar_poster'] ?? null;

    if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] == 0) {
        if (!empty($nama_file_gambar) && file_exists("../uploads/" . $nama_file_gambar)) {
            unlink("../uploads/" . $nama_file_gambar);
        }

        $target_dir = "../uploads/";
        $ekstensi = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
        $tanggal_create = date('Y-m-d'); 
        
        $penyelenggara_safe = preg_replace('/[^\w\-]+/', '_', $penyelenggara);
        $penyelenggara_safe = trim($penyelenggara_safe, '_');
        if (empty($penyelenggara_safe)) $penyelenggara_safe = 'event';

        $nama_file_gambar_baru = "poster_" . $penyelenggara_safe . "_" . $tanggal_create . "_" . uniqid() . "." . $ekstensi;
        $target_file = $target_dir . $nama_file_gambar_baru;

        if (move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
            $nama_file_gambar = $nama_file_gambar_baru;
        } else {
            $response['upload_warning'] = 'Data teks berhasil diupdate, tapi gambar baru gagal diupload.';
        }
    }
    $sql_update = "UPDATE tbl_volunteer SET 
                        judul = ?, 
                        penyelenggara = ?, 
                        deskripsi = ?, 
                        lokasi = ?, 
                        tanggal_mulai = ?, 
                        tanggal_selesai = ?, 
                        link_pendaftaran = ?, 
                        gambar_poster = ? 
                   WHERE id_volunteer = ?";

    $stmt_update = mysqli_prepare($conn, $sql_update);
    
    mysqli_stmt_bind_param($stmt_update, "ssssssssi",
        $judul,
        $penyelenggara,
        $deskripsi,
        $lokasi,
        $tanggal_mulai,
        $tanggal_selesai,
        $link_pendaftaran,
        $nama_file_gambar,
        $id_volunteer
    );

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Data volunteer berhasil diperbarui.';
        } else {
            $response['status'] = 'info';
            $response['message'] = 'Tidak ada perubahan data yang terdeteksi.';
        }
    } else {
        $response['message'] = 'Gagal mengeksekusi kueri update: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt_select);
    mysqli_stmt_close($stmt_update);

} else {
    $response['message'] = 'Metode request tidak valid. Harap gunakan POST.';
}

mysqli_close($conn);
echo json_encode($response);
?>