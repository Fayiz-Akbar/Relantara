<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar sebagai Penyelenggara</title>
    </head>
<body>
    <h1>Form Pendaftaran Penyelenggara</h1>
    <p>Daftar sebagai perusahaan, komunitas, atau NGO untuk mempublikasikan kegiatan.</p>
    <p>Sudah punya akun? <a href="login.php">Login</a></p>

    <form action="proses/proses_register_penyelenggara.php" method="POST">
        <div>
            <label for="nama_organisasi">Nama Penyelenggara (Perusahaan/Komunitas/NGO):</label><br>
            <input type="text" id="nama_organisasi" name="nama_organisasi" required>
        </div>
        <div>
            <label for="email">Email Kontak:</label><br>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>
        <div>
            <label for="confirm_password">Konfirmasi Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
        </div>
        <br>
        <button type="submit">Daftar</button>
    </form>
</body>
</html>