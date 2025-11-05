<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar sebagai Relawan</title>
    </head>
<body>
    <h1>Form Pendaftaran Relawan</h1>
    <p>Sudah punya akun? <a href="login.php">Login</a></p>

    <form action="proses/proses_register_relawan.php" method="POST">
        <div>
            <label for="nama_lengkap">Nama Lengkap:</label><br>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
        </div>
        <div>
            <label for="email">Email:</label><br>
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