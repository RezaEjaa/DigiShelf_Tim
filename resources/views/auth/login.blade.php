<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Digishelf</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --wood-dark: #5D4037;
            --wood-medium: #8D6E63;
            --cream: #F5F1E8;
            --cream-dark: #E8DCC8;
            --accent: #D4A574;
            --text-dark: #3E2723;
            --bg-dark: #2E2018;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: url("{{ asset('img/ui/background.jpg') }}") center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(30, 16, 8, 0.55);
            pointer-events: none;
            z-index: 0;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 560px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }

        /* LEFT PANEL */
        .auth-left {
            background: linear-gradient(160deg, #4A3728 0%, #3A2A1E 60%, #2E2018 100%);
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(212,165,116,0.07);
            pointer-events: none;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -40px; right: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(212,165,116,0.05);
            pointer-events: none;
        }

        .left-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .left-logo img {
            height: 70px;
            width: auto;
        }

        .left-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 0 30px;
        }

        .left-title {
            font-family: 'Crimson Pro', serif;
            font-size: 2.1rem;
            font-weight: 700;
            color: #FFF8EE;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .left-sub {
            font-size: 0.88rem;
            color: rgba(255,248,230,0.55);
            margin-bottom: 36px;
            line-height: 1.6;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,248,230,0.8);
            font-size: 0.88rem;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            background: rgba(212,165,116,0.12);
            border: 1px solid rgba(212,165,116,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--accent);
            font-size: 0.8rem;
        }

        .left-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,248,230,0.5);
            text-decoration: none;
            font-size: 0.82rem;
            transition: color 0.2s;
        }

        .left-back:hover { color: rgba(255,248,230,0.85); }

        /* RIGHT PANEL */
        .auth-right {
            background: white;
            padding: 50px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
            max-height: 90vh;
        }

        .auth-right::-webkit-scrollbar { width: 6px; }
        .auth-right::-webkit-scrollbar-track { background: var(--cream); }
        .auth-right::-webkit-scrollbar-thumb { background: var(--cream-dark); border-radius: 3px; }

        .right-heading h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .right-heading p {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            border: 1.5px solid #E8E0D8;
            border-radius: 12px;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #FAFAF9;
        }

        .input-wrap:focus-within {
            border-color: var(--wood-medium);
            box-shadow: 0 0 0 3px rgba(141,110,99,0.1);
            background: white;
        }

        .input-icon {
            padding: 0 14px;
            color: #CCC;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .input-wrap input {
            flex: 1;
            padding: 13px 14px 13px 0;
            border: none;
            background: transparent;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            color: var(--text-dark);
            outline: none;
        }

        .input-wrap input::placeholder { color: #CCC; }

        .toggle-pass {
            padding: 0 14px;
            background: none;
            border: none;
            color: #BBB;
            cursor: pointer;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .toggle-pass:hover { color: var(--wood-medium); }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--wood-dark);
            cursor: pointer;
        }

        .remember-row label {
            font-size: 0.82rem;
            color: #888;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            background: var(--wood-dark);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            margin-bottom: 18px;
        }

        .btn-submit:hover {
            background: var(--wood-medium);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(93,64,55,0.35);
        }

        .auth-switch {
            text-align: center;
            font-size: 0.83rem;
            color: #999;
            margin-bottom: 20px;
        }

        .auth-switch a {
            color: var(--wood-dark);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-switch a:hover { text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #EEE;
        }

        .divider span {
            font-size: 0.75rem;
            color: #BBB;
            white-space: nowrap;
        }

        .btn-google {
            width: 100%;
            background: white;
            border: 1.5px solid #E8E0D8;
            border-radius: 12px;
            padding: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-google:hover {
            background: #FAFAF9;
            border-color: #CCC;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .google-icon {
            width: 18px;
            height: 18px;
        }

        .alert-danger {
            background: #FFF0F0;
            color: #C62828;
            border-left: 3px solid #C62828;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.83rem;
            margin-bottom: 18px;
        }

        .mobile-logo {
            display: none;
            justify-content: center;
            margin-bottom: 20px;
        }

        .mobile-logo img {
            height: 50px;
        }

        .mobile-back {
            display: none;
        }

        @media (min-width: 769px) {
            .left-back {
                display: inline-flex;
            }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body {
                background-image: url("{{ asset('img/ui/mobile-back.jpg') }}");
                padding: 20px 16px;
                align-items: center;
            }
            .auth-wrapper {
                grid-template-columns: 1fr;
                border-radius: 20px;
                min-height: auto;
                max-width: 460px;
                margin: auto;
            }
            .auth-left { display: none; }
            .auth-right {
                padding: 44px 28px 40px;
                max-height: none;
                border-radius: 20px;
            }
            .mobile-back { display: flex !important; }
            .left-back {
                display: none;
            }
        
            .mobile-back {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }
        
            .auth-right {
                display: flex;
                flex-direction: column;
            }

            .mobile-logo {
                display: flex;
            }
        }

        .mobile-back {
            display: none;
            align-items: center;
            gap: 8px;
            color: var(--wood-medium);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 28px;
        }

        @media (max-width: 425px) {

        .auth-wrapper {
            max-width: 92%;
            border-radius: 16px;
        }

        .auth-right {
            padding: 28px 20px;
        }

        .right-heading h2 {
            font-size: 1.5rem;
        }

        .right-heading p {
            font-size: 0.75rem;
        }

        .input-wrap input {
            font-size: 0.8rem;
        }

        .btn-submit {
            font-size: 0.85rem;
            padding: 12px;
        }
    }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <!-- LEFT -->
        <div class="auth-left">
            <div class="left-logo">
                <img src="{{ asset('img/ui/logotext.png') }}" alt="Digishelf">
            </div>

            <div class="left-body">
                <h1 class="left-title">Selamat Datang Kembali</h1>
                <p class="left-sub">Masuk untuk melanjutkan pengelolaan perpustakaan digital Anda</p>

                <ul class="feature-list">
                    <li>
                        <span class="feature-icon"><i class="fas fa-book"></i></span>
                        Akses katalog buku digital
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-chart-line"></i></span>
                        Pantau peminjaman real-time
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-bookmark"></i></span>
                        Kelola koleksi dengan mudah
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-file-alt"></i></span>
                        Riwayat peminjaman tercatat
                    </li>
                </ul>
            </div>

            <a href="/" class="left-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        <!-- RIGHT -->
        <div class="auth-right">
            <div class="mobile-logo">
                <img src="{{ asset('img/ui/logotext.png') }}" alt="Digishelf">
            </div>
            <div class="right-heading">
                <h2>Masuk ke Akun</h2>
                <p>Kelola dan pantau perpustakaan Anda</p>
            </div>

            @if ($errors->any())
                <div class="alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="off" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="loginPass" placeholder="••••••••" autocomplete="new-password" required>
                        <button type="button" class="toggle-pass" onclick="togglePass('loginPass','iconLogin')">
                            <i class="fas fa-eye" id="iconLogin"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                </button>
            </form>

            <div class="auth-switch">
                Belum punya akun? <a href="/register">Daftar di sini</a>
            </div>

            <div class="divider"><span>atau masuk dengan</span></div>

            <a href="{{ route('auth.google') }}" class="btn-google">
                <svg class="google-icon" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Lanjutkan dengan Google
            </a>

            <a href="/" class="mobile-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>