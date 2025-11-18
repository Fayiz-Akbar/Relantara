<?php
session_start();
include 'config/db_connect.php';

// --- PERUBAHAN 1: Mengambil 6 kegiatan terbaru ---
$sql = "SELECT k.id_kegiatan, k.judul, k.lokasi, k.gambar_poster, p.nama_organisasi 
        FROM tbl_kegiatan k
        JOIN tbl_penyelenggara p ON k.id_penyelenggara = p.id_penyelenggara
        WHERE k.status_kegiatan = 'Published' AND k.deleted_at IS NULL
        ORDER BY k.tanggal_posting DESC
        LIMIT 6";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Relantara - Platform Volunteer Indonesia</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #FFFFFF;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .header {
            background-color: #FFFFFF;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            color: #4A90E2;
            margin: 0;
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
        }
        .header-nav {
            display: flex;
            align-items: center;
        }
        .header-nav a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            margin-left: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            display: inline-block;
        }
        .header-nav a.btn-login {
            border: 1px solid #4A90E2;
            color: #4A90E2;
        }
        .header-nav a.btn-login:hover {
            background-color: #4A90E2;
            color: #FFFFFF;
        }
        .header-nav a.btn-register {
            background-color: #F5A623;
            color: white;
            border: 1px solid #F5A623;
        }
        .header-nav a.btn-register:hover {
            background-color: #DA911F;
        }
        .menu-toggle {
            display: none;
            font-size: 2rem;
            background: none;
            border: none;
            color: #4A90E2;
            cursor: pointer;
        }

        /* --- PERUBAHAN 2: Hero Section dengan Background Foto --- */
        .hero {
            position: relative;
            padding: 8rem 2rem;
            text-align: center;
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            color: #FFFFFF;
        }
        .hero-text {
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-text h1 {
            margin: 0 0 1rem 0;
            font-size: 3.2rem;
            font-weight: 700;
            color: #FFFFFF;
        }
        .hero-text p {
            font-size: 1.25rem;
            color: #f1f1f1;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        /* --- PERUBAHAN 3: Memastikan CSS .btn-cta ada --- */
        .btn-cta {
            display: inline-block;
            background-color: #F5A623;
            color: white;
            padding: 1rem 2.5rem;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            font-size: 1.1rem;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-cta:hover {
            background-color: #DA911F;
            transform: translateY(-2px);
            color: white; 
        }
        
        .how-it-works {
            padding: 4rem 0;
        }
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-header h2 {
            font-size: 2.2rem;
            color: #333;
            margin: 0 0 0.5rem 0;
        }
        .section-header p {
            font-size: 1.1rem;
            color: #555;
            max-width: 700px;
            margin: 0 auto;
        }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            text-align: center;
        }
        .step-card {
            padding: 1.5rem;
        }
        .step-card .icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem auto;
            color: #4A90E2;
        }
        .step-card h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .step-card p {
            color: #555;
            line-height: 1.6;
        }

        .featured-kegiatan {
            padding: 4rem 0;
            background-color: #F0F4F8;
        }
        .kegiatan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #E0E0E0;
        }
        .card-content {
            padding: 1.5rem;
        }
        .card-content h3 {
            margin: 0 0 0.5rem 0;
            color: #333;
            font-size: 1.2rem;
            height: 3.6rem; 
            overflow: hidden;
        }
        .card-content .penyelenggara {
            color: #4A90E2;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .card-content .lokasi {
            color: #555;
            font-size: 0.9rem;
        }
        .card-content .btn-detail {
            display: inline-block;
            margin-top: 1rem;
            color: #F5A623;
            text-decoration: none;
            font-weight: bold;
        }

        .testimoni {
            padding: 4rem 0;
            background-color: #FFFFFF;
        }
        .testi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .testi-card {
            background: #F0F4F8;
            border-radius: 8px;
            padding: 2rem;
            font-style: italic;
            color: #444;
            border-left: 5px solid #4A90E2;
        }
        .testi-card p {
            margin: 0 0 1rem 0;
        }
        .testi-card strong {
            font-style: normal;
            color: #333;
        }
        
        .cta-section {
            background-color: #4A90E2;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }
        .cta-section h2 {
            font-size: 2.2rem;
            margin: 0 0 1rem 0;
        }
        .cta-section p {
            font-size: 1.1rem;
            color: #E0EFFF;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .footer {
            text-align: center;
            padding: 3rem 2rem;
            background-color: #333;
            color: #ccc;
        }
        .footer .social-icons {
            margin-top: 1rem;
        }
        .footer .social-icons a {
            margin: 0 0.5rem;
        }
        .footer .social-icons svg {
            width: 24px;
            height: 24px;
            fill: #ccc;
            transition: fill 0.2s ease, transform 0.2s ease;
        }
        .footer .social-icons a:hover svg {
            fill: #FFFFFF;
            transform: scale(1.1);
        }
        .footer p {
            margin: 0;
        }
        
        #scrollTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4A90E2;
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        #scrollTopBtn:hover { 
            background: #357ABD;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .header .container {
                flex-wrap: wrap;
            }
            .menu-toggle {
                display: block;
            }
            .header-nav {
                display: none;
                flex-direction: column;
                width: 100%;
                text-align: left;
                background: #FFFFFF;
                position: absolute;
                top: 70px;
                left: 0;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            }
            .header-nav.active {
                display: flex;
            }
            .header-nav a {
                margin: 0;
                padding: 1rem 2rem;
                border-bottom: 1px solid #F0F4F8;
            }
            .header-nav a.btn-login,
            .header-nav a.btn-register {
                border: none;
                margin: 0;
            }
            .header-nav a.btn-login:hover {
                background-color: #F0F4F8;
                color: #4A90E2;
            }
            .header-nav a.btn-register {
                background-color: #F5A623;
                color: white;
            }

            .hero {
                text-align: center;
            }
            
            .steps-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Relantara</a>
            <button class="menu-toggle" id="menuToggle">&#9776;</button>
            <nav class="header-nav" id="navMenu">
                <a href="kegiatan.php">Cari Kegiatan</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="admin/index.php" class="btn-login">Dashboard</a>
                    <?php elseif ($_SESSION['role'] == 'penyelenggara'): ?>
                        <a href="penyelenggara/index.php" class="btn-login">Dashboard Saya</a>
                    <?php elseif ($_SESSION['role'] == 'relawan'): ?>
                        <a href="relawan/index.php" class="btn-login">Dashboard Saya</a>
                    <?php endif; ?>
                    <a href="proses/logout.php" class="btn-register">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Daftar</a>
                <?php endif; ?>

            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-text">
                    <h1>Temukan Misi Baikmu</h1>
                    <p>Gabung dengan ribuan relawan dan komunitas untuk menciptakan dampak positif di sekitarmu.</p>
                    <a href="kegiatan.php" class="btn-cta">Mulai Cari Kegiatan</a>
                </div>
            </div>
        </section>

        <section class="how-it-works">
            <div class="container">
                <div class="section-header">
                    <h2>Semua Menjadi Mudah</h2>
                    <p>Hanya butuh 3 langkah sederhana untuk memulai perjalanan sosialmu.</p>
                </div>
                <div class="steps-grid">
                    <div class="step-card">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <h3>1. Cari Kegiatan</h3>
                        <p>Temukan ratusan kegiatan sosial dan kemanusiaan yang sesuai dengan minat dan lokasimu.</p>
                    </div>
                    <div class="step-card">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <h3>2. Daftar Online</h3>
                        <p>Baca detail, lihat benefit, dan kirim pendaftaranmu langsung melalui platform kami.</p>
                    </div>
                    <div class="step-card">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <h3>3. Beraksi & Berdampak</h3>
                        <p>Setelah diterima, datang ke lokasi, beraksi bersama relawan lain, dan buat perubahan!</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="featured-kegiatan">
            <div class="container">
                <div class="section-header">
                    <h2>Kegiatan Terbaru</h2>
                    <p>Jangan lewatkan kesempatan emas ini. Daftar sekarang!</p>
                </div>

                <div class="kegiatan-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="card">
                                <img class="card-image" src="uploads/poster_kegiatan/<?php echo htmlspecialchars($row['gambar_poster'] ?? 'default_poster.jpg'); ?>" alt="Poster Kegiatan">
                                <div class="card-content">
                                    <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                                    <div class="penyelenggara"><?php echo htmlspecialchars($row['nama_organisasi']); ?></div>
                                    <div class="lokasi"><?php echo htmlspecialchars($row['lokasi']); ?></div>
                                    <a href="detail_kegiatan.php?id=<?php echo $row['id_kegiatan']; ?>" class="btn-detail">Lihat Detail & Daftar &rarr;</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align:center; grid-column: 1 / -1; color: #555;">Belum ada kegiatan yang dipublikasikan saat ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="testimoni">
            <div class="container">
                <div class="section-header">
                    <h2>Suara dari Relawan</h2>
                    <p>Lihat apa yang mereka katakan tentang pengalaman mereka.</p>
                </div>
                <div class="testi-grid">
                    <div class="testi-card">
                        <p>"Ikut kegiatan dari Relantara benar-benar membuka wawasan sosial saya! Platformnya mudah digunakan."</p>
                        <strong>- Relawan Pendidikan</strong>
                    </div>
                    <div class="testi-card">
                        <p>"Platform ini bikin cari kegiatan sosial jadi gampang banget! Akhirnya bisa nemu yang sesuai."</p>
                        <strong>- Relawan Lingkungan</strong>
                    </div>
                    <div class="testi-card">
                        <p>"Sebagai penyelenggara, kami sangat terbantu dalam mencari relawan yang berkualitas. Terima kasih Relantara!"</p>
                        <strong>- Penyelenggara Komunitas</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="container">
                <h2>Siap Membuat Perubahan?</h2>
                <p>Jadilah bagian dari gerakan kebaikan. Daftarkan dirimu sebagai relawan atau publikasikan kegiatanmu hari ini.</p>
                <a href="register.php" class="btn-cta">Mulai Sekarang</a>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Relantara. Dibuat dengan semangat kolaborasi.</p>
        <div class="social-icons">
            <a href="#" title="Facebook">
                <svg viewBox="0 0 24 24"><path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.81C10.44 7.31 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C18.34 21.21 22 17.06 22 12.06C22 6.53 17.5 2.04 12 2.04Z"/></svg>
            </a>
            <a href="#" title="Instagram">
                <svg viewBox="0 0 24 24"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4C22 19.4 19.4 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2m-.2 2C5.6 4 4 5.6 4 7.8v8.4C4 18.4 5.6 20 7.8 20h8.4C18.4 20 20 18.4 20 16.2V7.8C20 5.6 18.4 4 16.2 4H7.6m4.6 4c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4m0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2m4.5-2.5c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .4-1 1-1"/></svg>
            </a>
            <a href="#" title="Twitter">
                <svg viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.67.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98-3.56-.18-6.73-1.89-8.84-4.48-.37.63-.58 1.37-.58 2.15 0 1.49.76 2.81 1.91 3.58-.7-.02-1.36-.21-1.94-.53v.05c0 2.08 1.48 3.82 3.44 4.21-.36.1-.74.15-1.14.15-.28 0-.55-.03-.81-.08.55 1.7 2.14 2.94 4.03 2.97-1.47 1.15-3.32 1.83-5.33 1.83-.35 0-.69-.02-1.03-.06 1.9 1.22 4.16 1.93 6.58 1.93 7.88 0 12.2-6.54 12.2-12.2 0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
            </a>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>