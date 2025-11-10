<?php
/*
 * FILE: _testing/relawan_tester.php (Versi 1.0 - JSON API)
 * FUNGSI: Menggunakan Fetch API (JavaScript) untuk menguji endpoint
 * Relawan (Register, Login, Daftar Kegiatan, Update Profil).
 */

// FUNGSI: Mulai sesi di paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// FUNGSI: Sertakan koneksi DB (untuk Data Viewer)
include '../config/db_connect.php';

// FUNGSI: Logika Logout (satu-satunya form submit normal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_action'])) {
    if ($_POST['session_action'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: relawan_tester.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Relawan Tester v1.0 (JSON API) - Relantara</title>
    <style>
        :root {
            --primary-color: #34A853; /* Warna hijau untuk Relawan */
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
        
        /* Tombol */
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.95rem; font-weight: bold; text-decoration: none; display: inline-block; width: 100%; }
        .btn:hover { opacity: 0.8; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-green { background-color: var(--success-color); color: white; }
        .btn-red { background-color: var(--danger-color); color: white; }
        .btn-blue { background-color: var(--info-color); color: white; }
        .btn-orange { background-color: var(--warning-color); color: white; }
        .btn-logout { width: auto; }
        
        /* Tabel */
        .table-wrapper { max-height: 300px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 4px; }
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
        .message-default { background-color: #E8F5E9; border: 1px solid var(--success-color); color: var(--success-color); }
        
        /* Output JSON */
        pre { background: #2d2d2d; color: #f1f1f1; padding: 1rem; border-radius: 4px; min-height: 150px; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <header>
        <h1>🧪 Relawan Tester v1.0 (JSON API)</h1>
    </header>

    <main>
        <div class="card col-full">
            <h2>👥 Manajemen Akun Relawan</h2>
            <div class="main-grid">
                
                <div>
                    <h3>Uji: Registrasi Akun (JSON)</h3>
                    <form class="api-form" action="../proses/proses_register_relawan.php" method="POST">
                        <div class="form-grid">
                            <label for="reg_nama">Nama Lengkap</label>
                            <input type="text" id="reg_nama" name="nama_lengkap" required>
                        </div>
                        <div class="form-grid">
                            <label for="reg_email">Email</label>
                            <input type="email" id="reg_email" name="email" required>
                        </div>
                        <div class="form-grid">
                            <label for="reg_pass">Password</label>
                            <input type="password" id="reg_pass" name="password" required>
                        </div>
                        <div class="form-grid">
                            <label for="reg_conf">Konfirm Pass</label>
                            <input type="password" id="reg_conf" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-green">Daftar Akun Baru (JSON)</button>
                    </form>
                </div>
                
                <div>
                    <h3>Uji: Login Akun (Submit Normal)</h3>
                    <form action="../proses/proses_login.php" method="POST">
                        <div class="form-grid">
                            <label for="la_user">Email</label>
                            <input type="text" id="la_user" name="email_username" required placeholder="Email relawan">
                        </div>
                        <div class="form-grid">
                            <label for="la_pass">Password</label>
                            <input type="password" id="la_pass" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-blue">Jalankan Login</button>
                        <small style="display:block; margin-top:0.5rem; text-align:center;">
                            *Login adalah submit normal (NON-JSON) untuk mengatur cookie sesi.
                            Kamu akan di-redirect, harap kembali ke halaman ini manual.
                        </small>
                    </form>
                </div>
            </div>
        </div>

        <div class="card col-full">
            <h2>Status Sesi & Pesan Balasan</h2>
            <div class="status-card">
                <div class="message message-default" id="message-box">
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'relawan') {
                        echo "Sesi terautentikasi sebagai Relawan: " . htmlspecialchars($_SESSION['nama']);
                    } else if (isset($_SESSION['role'])) {
                        echo "Sesi terautentikasi sebagai " . $_SESSION['role'] . ". Harap login sebagai Relawan.";
                    } else {
                        echo "Sesi tidak terautentikasi. Harap login.";
                    }
                    ?>
                </div>
                
                <form action="relawan_tester.php" method="POST" style="margin:0; min-width: 150px;">
                    <input type="hidden" name="session_action" value="logout">
                    <button type="submit" class="btn btn-red btn-logout">Logout (Submit Normal)</button>
                </form>
            </div>
        </div>

        <div class="card col-full">
            <h2>Data Viewer (GET)
                <button class="btn btn-blue" style="width: auto; float: right;" onclick="window.location.reload()">Refresh Data</button>
            </h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'relawan'): ?>
                <p>Menampilkan data yang relevan untuk Relawan (ID: <?php echo $_SESSION['user_id']; ?>)</p>
                <div class="main-grid">
                    <div>
                        <h3>Profil Saya</h3>
                        <div class="table-wrapper">
                            <table class="content-table">
                                <thead><tr><th>Nama</th><th>Email</th><th>Bio</th><th>Keahlian</th></tr></thead>
                                <tbody>
                                    <?php
                                    $id_r = $_SESSION['user_id'];
                                    $res_prof = $conn->prepare("SELECT nama_lengkap, email, bio, keahlian FROM tbl_relawan WHERE id_relawan = ?");
                                    $res_prof->bind_param("i", $id_r);
                                    $res_prof->execute();
                                    $result_prof = $res_prof->get_result();
                                    if($row = $result_prof->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['bio']); ?></td>
                                        <td><?php echo htmlspecialchars($row['keahlian']); ?></td>
                                    </tr>
                                    <?php endif; $res_prof->close(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h3>Pendaftaran Saya</h3>
                        <div class="table-wrapper">
                            <table class="content-table">
                                <thead><tr><th>ID Daftar</th><th>Judul Kegiatan</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php
                                    $id_r = $_SESSION['user_id'];
                                    $res_keg = $conn->prepare("SELECT p.id_pendaftaran, k.judul, p.status_pendaftaran 
                                                               FROM tbl_pendaftaran p
                                                               JOIN tbl_kegiatan k ON p.id_kegiatan = k.id_kegiatan
                                                               WHERE p.id_relawan = ?
                                                               ORDER BY p.tanggal_daftar DESC");
                                    $res_keg->bind_param("i", $id_r);
                                    $res_keg->execute();
                                    $result_keg = $res_keg->get_result();
                                    while($row = $result_keg->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id_pendaftaran']; ?></td>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status_pendaftaran']); ?></td>
                                    </tr>
                                    <?php endwhile; $res_keg->close(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h3>Kegiatan Ter-publish (untuk Mendaftar)</h3>
                        <div class="table-wrapper">
                            <table class="content-table">
                                <thead><tr><th>ID</th><th>Judul Kegiatan</th><th>Lokasi</th></tr></thead>
                                <tbody>
                                    <?php
                                    $res_pub = $conn->query("SELECT id_kegiatan, judul, lokasi FROM tbl_kegiatan WHERE status_kegiatan = 'Published' AND deleted_at IS NULL ORDER BY tanggal_posting DESC");
                                    while($row = $res_pub->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id_kegiatan']; ?></td>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p>Silakan login sebagai Relawan untuk melihat data.</p>
            <?php endif; ?>
        </div>

        <div class="card col-full">
            <h2>Aksi Relawan (C-U)</h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'relawan'): ?>
                <p>Jalankan aksi C-U-D. Respon JSON akan muncul di "Output JSON" di bawah.</p>
                
                <div class="main-grid">
                    <div>
                        <h3>Uji: Daftar Kegiatan (CREATE)</h3>
                        <form class="api-form" action="../relawan/proses_daftar_kegiatan.php" method="POST">
                            <div class="form-grid">
                                <label for="c_id_keg">ID Kegiatan</label>
                                <input type="number" id="c_id_keg" name="id_kegiatan" required placeholder="Lihat tabel 'Kegiatan Ter-publish'">
                            </div>
                            <div class="form-grid">
                                <label for="c_alasan">Alasan Bergabung</label>
                                <textarea id="c_alasan" name="alasan_bergabung" rows="3" placeholder="Opsional"></textarea>
                            </div>
                            <button type="submit" class="btn btn-green">Daftar Kegiatan (JSON)</button>
                        </form>
                    </div>
                    
                    <div>
                        <h3>Uji: Update Profil Saya (UPDATE)</h3>
                        <form class="api-form" action="../relawan/proses_update_profil.php" method="POST">
                            <div class="form-grid">
                                <label for="u_nama">Nama Lengkap</label>
                                <input type="text" id="u_nama" name="nama_lengkap" required value="<?php echo $_SESSION['nama']; ?>">
                            </div>
                            <div class="form-grid">
                                <label for="u_bio">Bio</label>
                                <textarea id="u_bio" name="bio" rows="3" placeholder="Ceritakan tentang diri Anda"></textarea>
                            </div>
                            <div class="form-grid">
                                <label for="u_keahlian">Keahlian</label>
                                <input type="text" id="u_keahlian" name="keahlian" placeholder="e.g., Desain Grafis, Public Speaking">
                            </div>
                            <button type="submit" class="btn btn-blue">Update Profil (JSON)</button>
                        </form>
                    </div>

                </div>
            <?php else: ?>
                <div style="text-align: center;">
                    <h2 style="color: var(--danger-color);">Akses Ditolak</h2>
                    <p>Silakan "Login sebagai Relawan" (submit normal) untuk mengaktifkan action tester.</p>
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
            <p>Ini adalah *state* `$_SESSION` saat halaman ini di-*render*.</p>
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
                    headers: { 'Accept': 'application/json' }
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
                
                // FUNGSI: Jika update profil sukses, update nama di header sesi
                if(data.status === 'success' && data.new_session_nama) {
                    messageBox.textContent += ` (Nama di sesi telah diperbarui ke '${data.new_session_nama}')`;
                    // Refresh halaman untuk melihat nama baru di header
                    setTimeout(() => window.location.reload(), 1500);
                }

            } catch (error) {
                messageBox.className = 'message message-error';
                messageBox.textContent = 'Error Jaringan atau Respon Bukan JSON: ' + error.message;
                jsonOutput.textContent = 'Fetch Gagal:\n' + error;
            } finally {
                button.disabled = false;
                // Mengembalikan teks tombol
                const originalText = button.textContent; // Ambil teks asli dari tombol
                if (originalText.includes('Daftar Akun')) button.textContent = 'Daftar Akun Baru (JSON)';
                else if (originalText.includes('Daftar Kegiatan')) button.textContent = 'Daftar Kegiatan (JSON)';
                else if (originalText.includes('Update Profil')) button.textContent = 'Update Profil (JSON)';
                else button.textContent = 'Jalankan (JSON)';
            }
        }
    </script>
</body>
</html>
<?php
// Selalu tutup koneksi di akhir
$conn->close();
?>