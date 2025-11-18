<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_action'])) {
    if ($_POST['session_action'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: penyelenggara_tester.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Penyelenggara Tester v1.0 (JSON API) - Relantara</title>
    <style>
        :root {
            --primary-color: #F5A623;
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
        .checkbox-group { border: 1px solid #ccc; border-radius: 4px; padding: 0.75rem; max-height: 150px; overflow-y: auto; background-color: #fafafa; }
        .checkbox-label { display: block; margin-bottom: 0.5rem; font-weight: normal; }
        
        /* Tombol */
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.95rem; font-weight: bold; text-decoration: none; display: inline-block; width: 100%; }
        .btn:hover { opacity: 0.8; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-green { background-color: var(--success-color); color: white; }
        .btn-red { background-color: var(--danger-color); color: white; }
        .btn-blue { background-color: var(--info-color); color: white; } /* Ubah ke info */
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
        .message-default { background-color: #E3F2FD; border: 1px solid var(--info-color); color: var(--info-color); }
        
        /* Output JSON */
        pre { background: #2d2d2d; color: #f1f1f1; padding: 1rem; border-radius: 4px; min-height: 150px; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <header>
        <h1>🧪 Penyelenggara Tester v1.0 (JSON API)</h1>
    </header>

    <main>
        <div class="card col-full">
            <h2>🏢 Manajemen Akun Penyelenggara</h2>
            <div class="main-grid">
                
                <div>
                    <h3>Uji: Registrasi Akun (JSON)</h3>
                    <form class="api-form" action="../proses/proses_register_penyelenggara.php" method="POST">
                        <div class="form-grid">
                            <label for="reg_nama">Nama Organisasi</label>
                            <input type="text" id="reg_nama" name="nama_organisasi" required>
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
                            <input type="text" id="la_user" name="email_username" required placeholder="Email penyelenggara">
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
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'penyelenggara') {
                        echo "Sesi terautentikasi sebagai Penyelenggara: " . htmlspecialchars($_SESSION['nama']);
                    } else if (isset($_SESSION['role'])) {
                        echo "Sesi terautentikasi sebagai " . $_SESSION['role'] . ". Harap login sebagai Penyelenggara.";
                    } else {
                        echo "Sesi tidak terautentikasi. Harap login.";
                    }
                    ?>
                </div>
                
                <form action="penyelenggara_tester.php" method="POST" style="margin:0; min-width: 150px;">
                    <input type="hidden" name="session_action" value="logout">
                    <button type="submit" class="btn btn-red btn-logout">Logout (Submit Normal)</button>
                </form>
            </div>
        </div>

        <div class="card col-full">
            <h2>Data Viewer (GET)
                <button class="btn btn-blue" style="width: auto; float: right;" onclick="window.location.reload()">Refresh Data</button>
            </h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'penyelenggara'): ?>
                <p>Menampilkan data yang relevan untuk Penyelenggara (ID: <?php echo $_SESSION['user_id']; ?>)</p>
                <div class="main-grid">
                    <div>
                        <h3>Kegiatan Saya (Aktif)</h3>
                        <div class="table-wrapper">
                            <table class="content-table">
                                <thead><tr><th>ID</th><th>Judul</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php
                                    $id_p = $_SESSION['user_id'];
                                    $res_keg = $conn->prepare("SELECT id_kegiatan, judul, status_kegiatan FROM tbl_kegiatan WHERE deleted_at IS NULL AND id_penyelenggara = ?");
                                    $res_keg->bind_param("i", $id_p);
                                    $res_keg->execute();
                                    $result = $res_keg->get_result();
                                    while($row = $result->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id_kegiatan']; ?></td>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status_kegiatan']); ?></td>
                                    </tr>
                                    <?php endwhile; $res_keg->close(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h3>Daftar Kategori (Referensi)</h3>
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
            <?php else: ?>
                <p>Silakan login sebagai Penyelenggara untuk melihat data.</p>
            <?php endif; ?>
        </div>

        <div class="card col-full">
            <h2>🗓️ Manajemen Kegiatan (C-D)</h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'penyelenggara'): ?>
                <p>Jalankan aksi C-U-D. Respon JSON akan muncul di "Output JSON" di bawah.</p>
                
                <div class="main-grid">
                    <div>
                        <h3>Uji: Buat Kegiatan Baru (CREATE)</h3>
                        <form class="api-form" action="../penyelenggara/proses_add_kegiatan.php" method="POST" enctype="multipart/form-data">
                            <div class="form-grid"><label for="c_judul">Judul</label><input type="text" id="c_judul" name="judul" required></div>
                            <div class="form-grid"><label for="c_desk">Deskripsi</label><textarea id="c_desk" name="deskripsi" rows="3" required></textarea></div>
                            <div class="form-grid"><label for="c_lokasi">Lokasi</label><input type="text" id="c_lokasi" name="lokasi" required></div>
                            <div class="form-grid"><label for="c_mulai">Tgl Mulai</label><input type="date" id="c_mulai" name="tanggal_mulai" required></div>
                            <div class="form-grid"><label for="c_selesai">Tgl Selesai</label><input type="date" id="c_selesai" name="tanggal_selesai" required></div>
                            <div class="form-grid"><label for="c_kuota">Kuota</label><input type="number" id="c_kuota" name="kuota" value="0"></div>
                            <div class="form-grid"><label for="c_benefit">Benefit</label><input type="text" id="c_benefit" name="benefit" placeholder="e.g., Sertifikat, Konsumsi"></div>
                            <div class="form-grid"><label for="c_status">Status</label>
                                <select id="c_status" name="status_kegiatan" required>
                                    <option value="Pending">Ajukan (Pending)</option>
                                    <option value="Draft">Simpan (Draft)</option>
                                </select>
                            </div>
                            <div class="form-grid"><label for="c_poster">Poster</label><input type="file" id="c_poster" name="gambar_poster" accept="image/*"></div>
                            
                            <label style="font-weight: 500; color: var(--grey-dark); margin: 1rem 0 0.5rem 0; display: block;">Kategori (Pilih 1 atau lebih)</label>
                            <div class="checkbox-group">
                                <?php
                                $res_kat_cb = $conn->query("SELECT id_kategori, nama_kategori FROM tbl_kategori WHERE deleted_at IS NULL");
                                while($row = $res_kat_cb->fetch_assoc()):
                                ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="kategori_ids[]" value="<?php echo $row['id_kategori']; ?>">
                                    <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                </label>
                                <?php endwhile; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-green" style="margin-top: 1.5rem;">Buat Kegiatan (JSON)</button>
                        </form>
                    </div>
                    
                    <div>
                        <h3>Uji: Hapus Kegiatan (DELETE)</h3>
                        <form class="api-form" action="../penyelenggara/proses_delete_kegiatan.php" method="POST">
                            <div class="form-grid">
                                <label for="d_id">ID Kegiatan</label>
                                <input type="number" id="d_id" name="id_kegiatan" required placeholder="Lihat tabel 'Kegiatan Saya'">
                            </div>
                            <button type="submit" class="btn btn-red">Hapus Kegiatan (JSON)</button>
                        </form>

                        <h3 style="margin-top: 2rem;">Uji: Update Kegiatan (Segera)</h3>
                        <p>File `proses_update_kegiatan.php` belum dibuat. Kita akan membuatnya setelah fungsi Create dan Delete ini teruji dengan baik.</p>
                    </div>

                </div>
            <?php else: ?>
                <div style="text-align: center;">
                    <h2 style="color: var(--danger-color);">Akses Ditolak</h2>
                    <p>Silakan "Login sebagai Penyelenggara" (submit normal) untuk mengaktifkan action tester.</p>
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

            } catch (error) {
                messageBox.className = 'message message-error';
                messageBox.textContent = 'Error Jaringan atau Respon Bukan JSON: ' + error.message;
                jsonOutput.textContent = 'Fetch Gagal:\n' + error;
            } finally {
                button.disabled = false;
                const originalText = form.querySelector('button[type="submit"]').textContent;
                if (originalText.includes('Daftar')) button.textContent = 'Daftar Akun Baru (JSON)';
                else if (originalText.includes('Buat')) button.textContent = 'Buat Kegiatan (JSON)';
                else if (originalText.includes('Hapus')) button.textContent = 'Hapus Kegiatan (JSON)';
                else button.textContent = 'Jalankan (JSON)';
            }
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>