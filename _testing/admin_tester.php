<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_action'])) {
    if ($_POST['session_action'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: admin_tester.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tester (JSON API) - Relantara</title>
    <style>
        :root {
            --primary-color: #4A90E2;
            --success-color: #34A853;
            --danger-color: #C62828;
            --warning-color: #F5A623;
            --info-color: #0277BD;
            --grey-light: #F4F6F8;
            --grey-dark: #555;
            --text-color: #333;
            --border-color: #E0E0E0;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--grey-light); margin: 0; color: var(--text-color); }
        header { background-color: var(--primary-color); color: white; padding: 1.5rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        header h1 { margin: 0; font-size: 1.8rem; }
        main { max-width: 1400px; margin: 2rem auto; padding: 0 2rem; }
        .card { background-color: #FFFFFF; padding: 1.5rem; border-radius: 8px; box-shadow: var(--card-shadow); margin-bottom: 1.5rem; }
        .card h2 { margin-top: 0; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; }
        .card h3 { color: var(--text-color); margin-top: 1rem; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
        .col-full { grid-column: 1 / -1; }
        
        .main-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; }
        
        /* Form */
        .form-section { padding-bottom: 1rem; border-bottom: 1px dashed var(--border-color); margin-bottom: 1.5rem; }
        .form-section:last-child { border-bottom: none; margin-bottom: 0; }
        .form-grid { display: grid; grid-template-columns: 120px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem; }
        .form-grid label { font-weight: 500; color: var(--grey-dark); }
        .form-grid input, .form-grid textarea, .form-grid select { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95rem; }
        .radio-group label { display: inline-block; margin-right: 1rem; font-weight: normal; }
        
        /* Tombol */
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.95rem; font-weight: bold; text-decoration: none; display: inline-block; width: 100%; }
        .btn:hover { opacity: 0.8; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-green { background-color: var(--success-color); color: white; }
        .btn-red { background-color: var(--danger-color); color: white; }
        .btn-blue { background-color: var(--primary-color); color: white; }
        .btn-orange { background-color: var(--warning-color); color: white; }
        .btn-logout { width: auto; }
        
        /* Tabel */
        .table-wrapper { max-height: 250px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 4px; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table th, .content-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .content-table th { background-color: var(--grey-light); position: sticky; top: 0; }
        .content-table tr:last-child td { border-bottom: none; }
        
        /* Pesan & Status */
        .status-card { display: flex; justify-content: space-between; align-items: center; }
        .message { flex-grow: 1; padding: 1rem; margin-right: 1rem; border-radius: 4px; text-align: center; font-weight: 500; }
        .message-error { background-color: #FFEBEE; border: 1px solid var(--danger-color); color: var(--danger-color); }
        .message-success { background-color: #E8F5E9; border: 1px solid var(--success-color); color: var(--success-color); }
        .message-info { background-color: #FFF8E1; border: 1px solid var(--warning-color); color: var(--warning-color); }
        .message-default { background-color: #E3F2FD; border: 1px solid var(--info-color); color: var(--info-color); }
        
        /* Output JSON */
        pre { background: #2d2d2d; color: #f1f1f1; padding: 1rem; border-radius: 4px; min-height: 150px; max-height: 400px; overflow-y: auto; }
        
        @media (max-width: 900px) {
            .main-grid { grid-template-columns: 1fr; }
            .status-card { flex-direction: column; }
            .message { margin-right: 0; margin-bottom: 1rem; width: 100%; }
        }
    </style>
</head>
<body>
    <header>
        <h1>🧪 Admin Tester (JSON API)</h1>
    </header>

    <main>
        <div class="card col-full">
            <h2>🛡️ Manajemen Admin & Sesi</h2>
            <div class="main-grid">
                <div>
                    <h3>Status Sesi & Pesan Balasan</h3>
                    <div class="status-card">
                        <div class="message message-default" id="message-box">
                            <?php
                            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                echo "Sesi terautentikasi sebagai Admin. Siap menguji aksi.";
                            } else {
                                echo "Sesi tidak terautentikasi. Harap login.";
                            }
                            ?>
                        </div>
                        
                        <form action="admin_tester.php" method="POST" style="margin:0; min-width: 150px;">
                            <input type="hidden" name="session_action" value="logout">
                            <button type="submit" class="btn btn-red btn-logout">Logout (Submit Normal)</button>
                        </form>
                    </div>
                </div>
                
                <div>
                    <h3>Uji: Login Akun Admin (Submit Normal)</h3>
                    <form action="../proses/proses_login.php" method="POST">
                        <div class="form-grid">
                            <label for="la_user">Username</label>
                            <input type="text" id="la_user" name="email_username" required>
                        </div>
                        <div class="form-grid">
                            <label for="la_pass">Password</label>
                            <input type="password" id="la_pass" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-blue">Jalankan Login</button>
                        <small style="display:block; margin-top:0.5rem; text-align:center;">
                            *Login adalah submit normal (NON-JSON) untuk mengatur cookie sesi.
                        </small>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card col-full">
            <h2>Data Viewer (GET)
                <button class="btn btn-blue" style="width: auto; float: right;" onclick="window.location.reload()">Refresh Data</button>
            </h2>
            <p>Data *live* dari database. Klik "Refresh Data" setelah menjalankan aksi untuk melihat perubahan.</p>
            <div class="main-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <div>
                    <h3>Admin</h3>
                    <div class="table-wrapper">
                        <table class="content-table">
                            <thead><tr><th>ID</th><th>Username</th><th>Nama</th></tr></thead>
                            <tbody>
                                <?php
                                $res_admin = $conn->query("SELECT id_admin, username, nama_lengkap FROM tbl_admin WHERE deleted_at IS NULL");
                                while($row = $res_admin->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $row['id_admin']; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3>Penyelenggara </h3>
                    <div class="table-wrapper">
                        <table class="content-table">
                            <thead><tr><th>ID</th><th>Nama</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php
                                $res_pending = $conn->query("SELECT id_penyelenggara, nama_organisasi, status_verifikasi FROM tbl_penyelenggara WHERE deleted_at IS NULL");
                                while($row = $res_pending->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $row['id_penyelenggara']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_organisasi']); ?></td>
                                    <td><span style="color: var(--warning-color);"><?php echo $row['status_verifikasi']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3>Relawan (Aktif)</h3>
                    <div class="table-wrapper">
                        <table class="content-table">
                            <thead><tr><th>ID</th><th>Nama</th><th>Email</th></tr></thead>
                            <tbody>
                                <?php
                                $res_relawan = $conn->query("SELECT id_relawan, nama_lengkap, email FROM tbl_relawan WHERE deleted_at IS NULL");
                                while($row = $res_relawan->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $row['id_relawan']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h3>Kegiatan (Aktif)</h3>
                    <div class="table-wrapper">
                        <table class="content-table">
                            <thead><tr><th>ID</th><th>Judul</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php
                                $res_keg = $conn->query("SELECT id_kegiatan, judul, status_kegiatan FROM tbl_kegiatan WHERE deleted_at IS NULL");
                                while($row = $res_keg->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $row['id_kegiatan']; ?></td>
                                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status_kegiatan']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3>Kategori (Aktif)</h3>
                    <div class="table-wrapper">
                        <table class="content-table">
                            <thead><tr><th>ID</th><th>Nama Kategori</th></tr></thead>
                            <tbody>
                                <?php
                                $res_kat = $conn->query("SELECT id_kategori, nama_kategori FROM tbl_kategori WHERE deleted_at IS NULL");
                                while($row = $res_kat->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $row['id_kategori']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card col-full">
            <h2>Action Tester (Aksi C-U-D via Fetch API)</h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <p>Jalankan aksi C-U-D. Respon JSON akan muncul di "Output JSON" di bawah.</p>
                
                <div class="main-grid">
                    <div>
                        <div class="card">
                            <h3>🏢 Manajemen Penyelenggara</h3>
                            <div class="form-section">
                                <h4>Uji: Verifikasi Penyelenggara</h4>
                                <form class="api-form" action="../admin/proses_verifikasi.php" method="POST">
                                    <div class="form-grid">
                                        <label for="pv_id">ID Penyelenggara</label>
                                        <input type="number" id="pv_id" name="id_penyelenggara" required placeholder="Lihat tabel 'Pending'">
                                    </div>
                                    <div class="form-grid">
                                        <label>Aksi</label>
                                        <div class="radio-group">
                                            <label><input type="radio" name="status_baru" value="Verified" checked> Verified</label>
                                            <label><input type="radio" name="status_baru" value="Rejected"> Rejected</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-green">Jalankan Verifikasi (JSON)</button>
                                </form>
                            </div>
                            <div class="form-section">
                                <h4>Uji: Suspend Penyelenggara</h4>
                                <form class="api-form" action="../admin/proses_suspend_user.php" method="POST">
                                    <input type="hidden" name="tipe_user" value="penyelenggara">
                                    <div class="form-grid">
                                        <label for="su_id_p">User ID</label>
                                        <input type="number" id="su_id_p" name="user_id" required placeholder="ID Penyelenggara">
                                    </div>
                                    <button type="submit" class="btn btn-orange">Jalankan Suspend (JSON)</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>👥 Manajemen Relawan</h3>
                            <div class="form-section">
                                <h4>Uji: Suspend Relawan</h4>
                                <form class="api-form" action="../admin/proses_suspend_user.php" method="POST">
                                    <input type="hidden" name="tipe_user" value="relawan">
                                    <div class="form-grid">
                                        <label for="su_id_r">User ID</label>
                                        <input type="number" id="su_id_r" name="user_id" required placeholder="ID Relawan">
                                    </div>
                                    <button type="submit" class="btn btn-orange">Jalankan Suspend (JSON)</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>🛡️ Manajemen Admin (Lanjutan)</h3>
                            <div class="form-section">
                                <h4>Uji: Buat Akun Admin</h4>
                                <form class="api-form" action="../admin/proses_create_admin.php" method="POST">
                                    <div class="form-grid">
                                        <label for="ca_nama">Nama Lengkap</label>
                                        <input type="text" id="ca_nama" name="nama_lengkap" required>
                                    </div>
                                    <div class="form-grid">
                                        <label for="ca_user">Username</label>
                                        <input type="text" id="ca_user" name="username" required>
                                    </div>
                                    <div class="form-grid">
                                        <label for="ca_pass">Password</label>
                                        <input type="password" id="ca_pass" name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-green">Buat Admin Baru (JSON)</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="card">
                            <h3>🗓️ Manajemen Kegiatan</h3>
                            <div class="form-section">
                                <h4>Uji: Update Status Kegiatan</h4>
                                <form class="api-form" action="../admin/proses_update_status_kegiatan.php" method="POST">
                                    <div class="form-grid">
                                        <label for="usk_id">ID Kegiatan</label>
                                        <input type="number" id="usk_id" name="id_kegiatan" required placeholder="Contoh: 1">
                                    </div>
                                    <div class="form-grid">
                                        <label for="usk_status">Status Baru</label>
                                        <select id="usk_status" name="status_baru" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Published">Published</option>
                                            <option value="Rejected">Rejected</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-blue">Update Status (JSON)</button>
                                </form>
                            </div>
                            <div class="form-section">
                                <h4>Uji: Hapus Kegiatan</h4>
                                <form class="api-form" action="../admin/proses_delete_kegiatan.php" method="POST">
                                    <div class="form-grid">
                                        <label for="dk_id">ID Kegiatan</label>
                                        <input type="number" id="dk_id" name="id_kegiatan" required placeholder="Lihat tabel kegiatan">
                                    </div>
                                    <button type="submit" class="btn btn-red">Hapus Kegiatan (JSON)</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <h3>🏷️ Manajemen Kategori</h3>
                            <div class="form-section">
                                <h4>Uji: Buat Kategori</h4>
                                <form class="api-form" action="../admin/proses_kategori.php" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <div class="form-grid">
                                        <label for="ck_nama">Nama Kategori</label>
                                        <input type="text" id="ck_nama" name="nama_kategori" required placeholder="Contoh: Pendidikan">
                                    </div>
                                    <button type="submit" class="btn btn-green">Buat Kategori (JSON)</button>
                                </form>
                            </div>
                            <div class="form-section">
                                <h4>Uji: Update Kategori</h4>
                                <form class="api-form" action="../admin/proses_kategori.php" method="POST">
                                    <input type="hidden" name="action" value="update">
                                    <div class="form-grid">
                                        <label for="uk_id">ID Kategori</label>
                                        <input type="number" id="uk_id" name="id_kategori" required placeholder="Lihat tabel kategori">
                                    </div>
                                    <div class="form-grid">
                                        <label for="uk_nama">Nama Baru</label>
                                        <input type="text" id="uk_nama" name="nama_kategori" required>
                                    </div>
                                    <button type="submit" class="btn btn-blue">Update Kategori (JSON)</button>
                                </form>
                            </div>
                            <div class="form-section">
                                <h4>Uji: Hapus Kategori</h4>
                                <form class="api-form" action="../admin/proses_delete_kategori.php" method="POST">
                                    <div class="form-grid">
                                        <label for="dk_id_kat">ID Kategori</label>
                                        <input type="number" id="dk_id_kat" name="id_kategori" required placeholder="Lihat tabel kategori">
                                    </div>
                                    <button type="submit" class="btn btn-red">Hapus Kategori (JSON)</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            <?php else: ?>
                <div style="text-align: center;">
                    <h2 style="color: var(--danger-color);">Akses Ditolak</h2>
                    <p>Silakan "Login sebagai Admin" (submit normal) untuk mengaktifkan action tester.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card col-full">
            <h2>Output JSON (Respon dari Aksi Terakhir)</h2>
            <p>Respon JSON mentah dari *file* proses akan muncul di sini setelah kamu menjalankan sebuah aksi.</p>
            <pre id="json-output">{ "status": "info", "message": "Menunggu aksi..." }</pre>
        </div>
        
        <div class="card col-full">
            <h2>Status Sesi PHP (Saat Halaman di-Load)</h2>
            <p>Ini adalah *state* `$_SESSION` saat halaman ini di-*render*. Gunakan tombol "Refresh Data" di atas untuk me-*reload* status ini setelah login.</p>
            <pre><?php
                echo json_encode($_SESSION ?? ["status" => "Tidak ada sesi aktif"], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            ?></pre>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiForms = document.querySelectorAll('.api-form');
            apiForms.forEach(form => {
                form.addEventListener('submit', handleApiSubmit);
            });
        });

        async function handleApiSubmit(event) {
            event.preventDefault();
            
            const form = event.target;
            const button = form.querySelector('button[type="submit"]');
            const messageBox = document.getElementById('message-box');
            const jsonOutput = document.getElementById('json-output');

            button.disabled = true;
            button.textContent = 'Memproses...';
            messageBox.className = 'message message-info';
            messageBox.textContent = `Mengirim request ke: ${form.action}`;
            jsonOutput.textContent = 'Menunggu respon...';

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'Accept': 'application/json' 
                    }
                });

                const data = await response.json();

                messageBox.textContent = data.message || 'Aksi selesai.';
                if (data.status === 'success') {
                    messageBox.className = 'message message-success';
                } else if (data.status === 'info') {
                    messageBox.className = 'message message-info';
                } else {
                    messageBox.className = 'message message-error';
                }

                jsonOutput.textContent = JSON.stringify(data, null, 2); 

            } catch (error) {
                messageBox.className = 'message message-error';
                messageBox.textContent = 'Error Jaringan atau Respon Bukan JSON: ' + error.message;
                jsonOutput.textContent = 'Fetch Gagal:\n' + error;
            } finally {
                button.disabled = false;
                if (button.classList.contains('btn-green')) button.textContent = 'Jalankan (JSON)';
                else if (button.classList.contains('btn-blue')) button.textContent = 'Jalankan (JSON)';
                else if (button.classList.contains('btn-orange')) button.textContent = 'Jalankan (JSON)';
                else if (button.classList.contains('btn-red')) button.textContent = 'Jalankan (JSON)';
                else button.textContent = 'Jalankan (JSON)';
            }
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>