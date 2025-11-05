<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung - Relantara</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #FFFFFF;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            width: 100%;
            max-width: 700px;
            text-align: center;
        }
        .container h1 {
            color: #4A90E2;
            margin-bottom: 0.5rem;
        }
        .container p {
            color: #555;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .choice-container {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .choice-box {
            flex-basis: 48%;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }
        .choice-box svg {
            width: 50px;
            height: 50px;
            color: #4A90E2;
            margin-bottom: 1rem;
        }
        .choice-box h2 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .choice-box .desc {
            margin: 0;
            color: #555;
            font-size: 0.9rem;
        }
        .choice-box:hover {
            border-color: #F5A623;
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .text-center {
            text-align: center;
            margin-top: 2rem;
            color: #555;
        }
        .text-center a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gabung Bersama Relantara</h1>
        <p>Pilih peran Anda untuk memulai.</p>
        
        <div class="choice-container">
            <a href="register_relawan.php" class="choice-box">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <h2>Saya Relawan</h2>
                <p class="desc">Saya ingin mencari dan mendaftar di berbagai kegiatan sosial.</p>
            </a>
            
            <a href="register_penyelenggara.php" class="choice-box">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21v-4.501c0-1.105.895-2.002 2-2.002h1.5v-1.08c0-1.105.895-2.002 2-2.002h4.5c1.105 0 2 .897 2 2.002V14.5h1.5c1.105 0 2 .897 2 2.002V21m-9.002-10.5h.008v.008H12v-.008z" />
                     <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18v-4.501c0-1.105-.895-2.002-2-2.002h-2.5V11.118c0-1.105-.895-2.002-2-2.002h-4.5c-1.105 0-2 .897-2 2.002v3.38H5c-1.105 0-2 .897-2 2.002V21z" />
                </svg>
                <h2>Kami Penyelenggara</h2>
                <p class="desc">Kami (Perusahaan/Komunitas) ingin mempublikasikan kegiatan.</p>
            </a>
        </div>

        <div class="text-center">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>