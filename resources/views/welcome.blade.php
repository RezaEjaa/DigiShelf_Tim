<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digishelf - Digital Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --wood-dark: #5D4037;
            --wood-medium: #8D6E63;
            --wood-light: #A1887F;
            --cream: #F5F1E8;
            --cream-dark: #E8DCC8;
            --accent: #D4A574;
            --text-dark: #3E2723;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }
        
        /* HEADER */
        .header {
            background: #2c201b;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 15px 0;
            box-shadow: 0 2px 16px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-buttons {
            display: flex;
            gap: 15px;
        }
        
        .btn-login,
        .btn-daftar {
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .btn-login {
            background: transparent;
            border: 2px solid var(--wood-medium);
            color: var(--wood-medium);
        }
        
        .btn-login:hover {
            background: var(--wood-medium);
            color: white;
        }
        
        .btn-daftar {
            background: var(--wood-dark);
            color: white;
            border: 2px solid var(--wood-dark);
        }
        
        .btn-daftar:hover {
            background: var(--wood-medium);
            border-color: var(--wood-medium);
        }
        
        .mobile-user-btn {
            display: none;
            width: 45px;
            height: 45px;
            background: var(--wood-dark);
            color: white;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .mobile-user-btn:hover {
            background: var(--wood-medium);
            transform: scale(1.05);
        }
        
        /* HERO SECTION */
        .hero {
            background: linear-gradient(150deg, #4A3728 0%, #3A2A1E 50%, #2E2018 100%);
            padding: 80px 30px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23CFBB99' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        
        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        /* HERO TEXT — semua putih/cream agar terbaca di atas bg coklat */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.88rem;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
            margin-bottom: 24px;
        }

        .hero-left h1 {
            font-family: 'Crimson Pro', serif;
            font-size: 3.5rem;
            font-weight: 700;
            color: #FFF8EE;
            margin-bottom: 25px;
            line-height: 1.2;
            text-shadow: 0 2px 12px rgba(0,0,0,0.15);
        }
        
        .hero-left p {
            font-size: 1.1rem;
            color: rgba(255,248,230,0.8);
            line-height: 1.8;
            margin-bottom: 35px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FFF8EE;
            color: var(--text-dark);
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-hero-primary:hover {
            background: var(--accent);
            color: var(--text-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #FFF8EE;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            border: 2px solid rgba(255,248,230,0.5);
            transition: all 0.3s;
        }

        .btn-hero-secondary:hover {
            background: rgba(255,248,230,0.12);
            border-color: rgba(255,248,230,0.8);
        }

        /* STATS */
        .hero-stats {
            display: flex;
            gap: 0;
        }

        .hero-stat-item {
            padding-right: 32px;
            margin-right: 32px;
            border-right: 1px solid rgba(255,248,230,0.25);
        }

        .hero-stat-item:last-child {
            border-right: none;
            padding-right: 0;
            margin-right: 0;
        }

        .hero-stat-item .stat-number {
            font-family: 'Crimson Pro', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #FFF8EE;
            line-height: 1;
            margin-bottom: 4px;
        }

        .hero-stat-item .stat-label {
            font-size: 0.8rem;
            color: rgba(255,248,230,0.65);
            font-weight: 500;
            letter-spacing: 0.02em;
        }
        
        .hero-right {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .hero-logo {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 60px rgba(93,64,55,0.3);
        }
        
        .hero-logo img {
            width: 250px;
        }
        
        /* KEUNGGULAN */
        .keunggulan {
            background: white;
            padding: 80px 30px;
        }
        
        .keunggulan-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-tag {
            display: inline-block;
            padding: 8px 20px;
            background: var(--cream);
            border-radius: 20px;
            color: var(--wood-medium);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .section-title {
            font-family: 'Crimson Pro', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .keunggulan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        
        .keunggulan-card {
            background: var(--cream);
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .keunggulan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        }
        
        .keunggulan-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }
        
        .keunggulan-icon i {
            font-size: 36px;
            color: white;
        }
        
        .keunggulan-card h3 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
        }
        
        .keunggulan-card p {
            color: #666;
            line-height: 1.7;
            text-align: justify;
        }
        
        /* BUKU TERBARU */
        .buku-terbaru {
            background: var(--cream);
            padding: 80px 30px;
        }
        
        .buku-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .bookshelf-wrapper {
            background: linear-gradient(to bottom, #8B6F47 0%, #7A5F3D 50%, #6F5539 100%);
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
            position: relative;
        }
        
        .bookshelf-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(90deg,transparent,transparent 2px,rgba(0,0,0,0.05) 2px,rgba(0,0,0,0.05) 4px);
            pointer-events: none;
            border-radius: 20px;
        }
        
        .books-display {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        
        .book-item {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 3px 3px 12px rgba(0,0,0,0.3);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .book-item:hover {
            transform: translateY(-12px) rotate(2deg);
            box-shadow: 5px 12px 25px rgba(0,0,0,0.4);
        }
        
        .book-cover-display {
            width: 100%;
            aspect-ratio: 2 / 3;
            background: linear-gradient(135deg, #A1887F, #8D6E63);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .book-cover-display img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-cover-display i {
            font-size: 48px;
            color: rgba(255,255,255,0.5);
        }
        
        .shelf-line {
            height: 15px;
            background: linear-gradient(to bottom, #6F5539 0%, #5C4A31 50%, #6F5539 100%);
            border-radius: 5px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .shelf-line::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 8px;
            background: rgba(0,0,0,0.2);
            filter: blur(6px);
        }
        
        /* FOOTER */
        .footer {
            background: #2c201b;
            color: rgba(255,255,255,0.8);
            padding: 60px 30px 30px;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 50px;
            margin-bottom: 40px;
        }
        
        .footer-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .footer-brand img {
            height: 45px;
            width: auto;
        }
        
        .footer-brand-text p {
            line-height: 1.7;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.6);
        }
        
        .footer-section h4 {
            color: white;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s;
            font-size: 0.95rem;
        }
        
        .footer-links a:hover {
            color: var(--accent);
        }
        
        .footer-contact p {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 25px;
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        
        /* TENTANG */
        .tentang {
            background: white;
            padding: 100px 30px;
        }

        .tentang-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .tentang-left {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tentang-card {
            background: linear-gradient(145deg, #4A3728, #3A2A1E);
            border-radius: 24px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(58,42,30,0.35);
            text-align: center;
        }

        .tentang-card-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .tentang-card-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .tentang-card h3 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #FFF8EE;
            margin-bottom: 6px;
        }

        .tentang-card-sub {
            font-size: 0.9rem;
            color: rgba(255,248,230,0.55);
            margin-bottom: 35px;
        }

        .tentang-card-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }

        .tentang-stat-box {
            background: rgba(255,248,230,0.08);
            border-radius: 14px;
            padding: 18px 10px;
        }

        .tentang-stat-box .t-number {
            font-family: 'Crimson Pro', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 4px;
        }

        .tentang-stat-box .t-label {
            font-size: 0.75rem;
            color: rgba(255,248,230,0.6);
        }

        .tentang-right .section-tag {
            display: inline-block;
            margin-bottom: 18px;
        }

        .tentang-right h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
            margin-bottom: 24px;
        }

        .tentang-right p {
            font-size: 1rem;
            color: #666;
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .tentang-right p:last-of-type {
            margin-bottom: 35px;
        }

        .btn-tentang {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--wood-dark);
            color: white;
            padding: 15px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-tentang:hover {
            background: var(--wood-medium);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(93,64,55,0.3);
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }
            .hero-left h1 { font-size: 2.8rem; }
            .hero-buttons { justify-content: center; }
            .hero-stats { justify-content: center; }
            .hero-logo { width: 280px; height: 280px; }
            .hero-logo img { width: 200px; }
            .keunggulan-grid { grid-template-columns: 1fr; gap: 25px; }
            .footer-content { grid-template-columns: 1fr 1fr; gap: 40px; }
            .footer-brand { grid-column: 1 / -1; }
            .tentang-container { grid-template-columns: 1fr; gap: 50px; }
            .tentang-right h2 { font-size: 2.2rem; }
        }

        @media (max-width: 768px) {
            .books-display {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }
            .book-item:nth-child(4),
            .book-item:nth-child(5) { display: none; }
            .bookshelf-wrapper { padding: 35px 20px; border-radius: 16px; }
            .header-buttons { display: none; }
            .mobile-user-btn { display: flex; }

            /* Hero mobile: mirip referensi */
            .hero { padding: 40px 24px 55px; }
            .hero-container {
                grid-template-columns: 1fr;
                gap: 0;
                text-align: left;
            }
            .hero-right { display: none; }

            /* Badge: 1 baris, font lebih kecil */
            .hero-badge {
                font-size: 0.75rem;
                padding: 6px 12px;
                white-space: nowrap;
            }

            .hero-left h1 { font-size: 2.1rem; }
            .hero-left p { font-size: 0.95rem; margin-bottom: 28px; }

            /* Tombol: jejer kiri-kanan (flex-row), sama lebar */
            .hero-buttons {
                flex-direction: row;
                gap: 10px;
                margin-bottom: 36px;
            }
            .btn-hero-primary,
            .btn-hero-secondary {
                flex: 1;
                justify-content: center;
                padding: 13px 10px;
                font-size: 0.9rem;
            }

            /* Stats: kecil, tetap 3 kolom jejer */
            .hero-stats { justify-content: flex-start; gap: 0; }
            .hero-stat-item {
                padding-right: 20px;
                margin-right: 20px;
            }
            .hero-stat-item .stat-number { font-size: 1.5rem; }
            .hero-stat-item .stat-label { font-size: 0.72rem; }

            .section-title { font-size: 2rem; }
            .footer-content { grid-template-columns: 1fr; gap: 30px; }
            .tentang { padding: 70px 20px; }
            .tentang-card { padding: 35px 25px; }
            .tentang-right h2 { font-size: 1.9rem; }
        }

        @media (max-width: 480px) {
            .books-display {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .book-item:nth-child(3),
            .book-item:nth-child(4),
            .book-item:nth-child(5) { display: none; }
            .bookshelf-wrapper { padding: 25px 12px; border-radius: 14px; }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <img src="{{ asset('img/ui/logotext.png') }}" alt="Digishelf" height="55" width="auto">
            </div>
            <div class="header-buttons">
                <a href="/login" class="btn-login">Login</a>
                <a href="/register" class="btn-daftar">Daftar</a>
            </div>
            <a href="/login" class="mobile-user-btn">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-left">
                <span class="hero-badge"><i class="fas fa-shield-alt"></i> Platform Perpustakaan Resmi & Terpercaya</span>
                <h1>Perpustakaan Digital Terpercaya</h1>
                <p>Kelola koleksi buku dengan mudah, pantau peminjaman real-time, dan akses katalog digital kapan saja, di mana saja dengan sistem yang aman dan transparan.</p>
                <div class="hero-buttons">
                    <a href="/register" class="btn-hero-primary"><i class="fas fa-book-open"></i> Mulai Sekarang</a>
                    <a href="#tentang" class="btn-hero-secondary">Tentang Kami &rarr;</a>
                </div>
                <!-- STATS DARI DATABASE -->
                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <div class="stat-number">{{ \App\Models\Book::count() }}+</div>
                        <div class="stat-label">Koleksi Buku</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="stat-number">{{ \App\Models\User::where('role', 'user')->count() }}+</div>
                        <div class="stat-label">Anggota Aktif</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="stat-number">{{ \App\Models\Borrowing::count() }}+</div>
                        <div class="stat-label">Riwayat Peminjaman</div>
                    </div>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-logo">
                    <img src="{{ asset('img/ui/logo.png') }}" alt="Digishelf Logo">
                </div>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN -->
    <section class="keunggulan">
        <div class="keunggulan-container">
            <div class="section-header">
                <span class="section-tag">Keunggulan Platform</span>
                <h2 class="section-title">Mengapa Memilih Digishelf?</h2>
            </div>
            
            <div class="keunggulan-grid">
                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Terpercaya & Aman</h3>
                    <p>Sistem keamanan tingkat tinggi dengan enkripsi data dan backup otomatis untuk melindungi informasi perpustakaan Anda</p>
                </div>

                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>UI Menyerupai Rak</h3>
                    <p>Tampilan antarmuka yang intuitif dengan desain visual menyerupai rak buku asli untuk pengalaman yang lebih natural</p>
                </div>

                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3>Fleksibel & Lengkap</h3>
                    <p>Tersedia banyak informasi detail seperti judul, penulis, cover, deskripsi, ISBN, penerbit, dan tahun terbit untuk setiap buku</p>
                </div>
            </div>
        </div>
    </section>

    <!-- BUKU TERBARU -->
    <section class="buku-terbaru">
        <div class="buku-container">
            <div class="section-header">
                <span class="section-tag">Koleksi Terbaru</span>
                <h2 class="section-title">Buku Terbaru</h2>
            </div>
            
            <div class="bookshelf-wrapper">
                <div class="books-display">
                    @php
                    $latestBooks = \App\Models\Book::latest()->take(5)->get();
                    $colors = [
                        'linear-gradient(135deg, #A1887F, #8D6E63)',
                        'linear-gradient(135deg, #7986CB, #5C6BC0)',
                        'linear-gradient(135deg, #81C784, #66BB6A)',
                        'linear-gradient(135deg, #FFB74D, #FFA726)',
                        'linear-gradient(135deg, #E57373, #EF5350)',
                    ];
                    @endphp
                    
                    @if($latestBooks->count() > 0)
                        @foreach($latestBooks as $index => $book)
                        <div class="book-item">
                            <div class="book-cover-display" style="background: {{ $colors[$index % 5] }};">
                                @if($book->cover_image)
                                    <img src="{{ asset('img/covers/' . $book->cover_image) }}" alt="{{ $book->title }}">
                                @else
                                    <i class="fas fa-book"></i>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <!-- Dummy books if no data -->
                        @for($i = 0; $i < 5; $i++)
                        <div class="book-item">
                            <div class="book-cover-display" style="background: {{ $colors[$i] }};">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        @endfor
                    @endif
                </div>
                <div class="shelf-line"></div>
            </div>
        </div>
    </section>

    <!-- TENTANG -->
    <section class="tentang" id="tentang">
        <div class="tentang-container">
            <div class="tentang-left">
                <div class="tentang-card">
                    <div class="tentang-card-logo">
                        <img src="{{ asset('img/ui/logo.png') }}" alt="Digishelf Logo">
                    </div>
                    <h3>Digishelf</h3>
                    <p class="tentang-card-sub">Platform Perpustakaan Digital Terpercaya</p>
                    <div class="tentang-card-stats">
                        <div class="tentang-stat-box">
                            <div class="t-number">{{ \App\Models\Book::count() }}+</div>
                            <div class="t-label">Koleksi Buku</div>
                        </div>
                        <div class="tentang-stat-box">
                            <div class="t-number">{{ \App\Models\User::where('role', 'user')->count() }}+</div>
                            <div class="t-label">Anggota</div>
                        </div>
                        <div class="tentang-stat-box">
                            <div class="t-number">{{ \App\Models\Borrowing::count() }}+</div>
                            <div class="t-label">Peminjaman</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tentang-right">
                <span class="section-tag">Tentang Kami</span>
                <h2>Hadir untuk Mengelola Perpustakaan Modern</h2>
                <p>Digishelf adalah platform perpustakaan digital yang dikelola secara amanah dan transparan. Kami hadir untuk memudahkan pengelolaan koleksi buku, peminjaman, dan administrasi perpustakaan secara digital.</p>
                <p>Setiap data yang masuk dikelola dengan penuh tanggung jawab, dan laporan aktivitas perpustakaan dapat diakses secara berkala untuk menjaga kepercayaan seluruh anggota.</p>
                <a href="/register" class="btn-tentang">Bergabung Sekarang &rarr;</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="{{ asset('img/ui/logotext.png') }}" alt="Digishelf">
                    <div class="footer-brand-text">
                        <p>Platform perpustakaan digital terpercaya untuk mengelola koleksi buku dengan mudah dan transparan. Aman, cepat, dan berdampak nyata bagi pengelolaan perpustakaan modern.</p>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="/">Beranda</a></li>
                        <li><a href="/login">Login</a></li>
                        <li><a href="/register">Daftar</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div class="footer-section footer-contact">
                    <h4>Hubungi Kami</h4>
                    <p><i class="fas fa-envelope"></i> info@digishelf.id</p>
                    <p><i class="fas fa-phone"></i> +62 812-3456-7890</p>
                    <p><i class="fas fa-map-marker-alt"></i> Gedung PKM Lt. 2</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Digishelf. Platform Perpustakaan Digital Terpercaya.</p>
            </div>
        </div>
    </footer>

</body>
</html>