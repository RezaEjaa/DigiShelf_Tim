<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digishelf')</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ═══════════ HEADER ═══════════ */
        .header {
            background: rgba(30, 20, 16, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .header-brand img {
            height: 42px;
            width: auto;
            display: block;
        }

        /* Nav */
        .header-nav {
            display: flex;
            align-items: center;
            gap: 2px;
            list-style: none;
            flex: 1;
            justify-content: center;
        }

        .header-nav a {
            padding: 8px 15px;
            color: rgba(255,248,230,0.62);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .header-nav a:hover {
            color: rgba(255,248,230,0.95);
            background: rgba(255,255,255,0.07);
        }

        .header-nav a.active {
            color: var(--accent);
            background: rgba(212,165,116,0.1);
            font-weight: 600;
        }

        /* User section - avatar clickable untuk logout */
        .header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .user-dropdown { position: relative; }

        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 50px;
            transition: background 0.2s;
        }

        .user-dropdown-btn:hover { background: rgba(255,255,255,0.07); }

        .header-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Crimson Pro', serif;
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
            overflow: hidden;
        }

        .header-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .header-chevron {
            font-size: 0.65rem;
            color: rgba(255,248,230,0.45);
            transition: transform 0.2s;
        }

        .user-dropdown.open .header-chevron { transform: rotate(180deg); }

        .header-username {
            font-size: 0.875rem;
            font-weight: 600;
            color: rgba(255,248,230,0.9);
            white-space: nowrap;
        }

        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.18);
            min-width: 150px;
            overflow: hidden;
            z-index: 100;
        }

        .user-dropdown-menu.show { display: block; }

        .user-dropdown-menu button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            color: #C62828;
            transition: background 0.2s;
        }

        .user-dropdown-menu button:hover { background: #FFF5F5; }

        /* Mobile */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: rgba(255,248,230,0.8);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
        }

        .mobile-nav {
            display: none;
            background: #1a110d;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 10px 16px 18px;
        }

        .mobile-nav.show { display: block; }

        .mobile-nav a {
            display: block;
            padding: 12px 14px;
            color: rgba(255,248,230,0.68);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 9px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .mobile-nav a:hover,
        .mobile-nav a.active {
            background: rgba(255,255,255,0.07);
            color: var(--accent);
        }

        .mobile-nav-logout {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .mobile-nav-logout button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            background: none;
            border: none;
            color: #EF9A9A;
            font-size: 0.9rem;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            border-radius: 9px;
            transition: background 0.2s;
        }

        .mobile-nav-logout button:hover { background: rgba(255,255,255,0.05); }

        /* ═══════════ CONTENT ═══════════ */
        .main-content {
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 35px 30px;
        }

        /* ═══════════ FOOTER ═══════════ */
        .footer {
            background: #1E1410;
            color: rgba(255,255,255,0.7);
            padding: 50px 30px 25px;
            margin-top: auto;
        }

        .footer-container { max-width: 1400px; margin: 0 auto; }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 50px;
            margin-bottom: 35px;
        }

        .footer-brand img { height: 40px; width: auto; margin-bottom: 15px; }

        .footer-brand p {
            font-size: 0.875rem;
            line-height: 1.7;
            color: rgba(255,255,255,0.5);
        }

        .footer-section h4 {
            color: white;
            font-size: 0.925rem;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }

        .footer-links a {
            color: rgba(255,255,255,0.52);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--accent); }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 22px;
            text-align: center;
            color: rgba(255,255,255,0.38);
            font-size: 0.82rem;
        }

        /* ═══════════ RESPONSIVE ═══════════ */
        @media (max-width: 992px) {
            .header-nav { display: none; }
            .mobile-toggle { display: block; }
            .header-username { display: none; }
            .chevron-icon { display: none; }
        }

        @media (max-width: 768px) {
            .header-container { padding: 0 20px; }
            .main-content { padding: 20px 16px; }
            .footer-content { grid-template-columns: 1fr; gap: 28px; }
            .footer { padding: 38px 20px 20px; }
        }

        @media (max-width: 360px) {
            .main-content { padding: 12px 8px; }
            .header-container { padding: 0 12px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- HEADER -->
    <header class="header">
        <div class="header-container">

            <a href="{{ route('user.dashboard') }}" class="header-brand">
                <img src="{{ asset('img/ui/logotext.png') }}" alt="Digishelf">
            </a>

            <!-- Desktop Nav -->
            <ul class="header-nav">
                <li><a href="{{ route('user.dashboard') }}"   class="{{ request()->routeIs('user.dashboard')  ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('user.books') }}"       class="{{ request()->routeIs('user.books')       ? 'active' : '' }}">Koleksi Buku</a></li>
                <li><a href="{{ route('user.borrowings') }}"  class="{{ request()->routeIs('user.borrowings')  ? 'active' : '' }}">Peminjaman</a></li>
                <li><a href="{{ route('user.history') }}"     class="{{ request()->routeIs('user.history')     ? 'active' : '' }}">Riwayat</a></li>
                <li><a href="{{ route('user.favorites') }}"   class="{{ request()->routeIs('user.favorites')   ? 'active' : '' }}">Favorit</a></li>
                <li><a href="{{ route('user.account') }}"     class="{{ request()->routeIs('user.account')     ? 'active' : '' }}">Akun</a></li>
            </ul>

            <div class="header-user">
                <!-- Dropdown logout -->
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-dropdown-btn" onclick="toggleUserDropdown()">
                        <div class="header-avatar">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset('img/profile_photos/' . Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <span class="header-username">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down header-chevron"></i>
                    </button>
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </div>
                </div>

                <!-- Hamburger (mobile) -->
                <button class="mobile-toggle" id="mobileToggle" onclick="toggleMobileNav()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer: nav saja, TANPA logout -->
        <div class="mobile-nav" id="mobileNav">
            <a href="{{ route('user.dashboard') }}"  class="{{ request()->routeIs('user.dashboard')  ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('user.books') }}"      class="{{ request()->routeIs('user.books')       ? 'active' : '' }}">Koleksi Buku</a>
            <a href="{{ route('user.borrowings') }}" class="{{ request()->routeIs('user.borrowings')  ? 'active' : '' }}">Peminjaman</a>
            <a href="{{ route('user.history') }}"    class="{{ request()->routeIs('user.history')     ? 'active' : '' }}">Riwayat</a>
            <a href="{{ route('user.favorites') }}"  class="{{ request()->routeIs('user.favorites')   ? 'active' : '' }}">Favorit</a>
            <a href="{{ route('user.account') }}"    class="{{ request()->routeIs('user.account')     ? 'active' : '' }}">Akun</a>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="{{ asset('img/ui/logotext.png') }}" alt="Digishelf">
                    <p>Platform perpustakaan digital yang mudah, transparan, dan terpercaya untuk mengelola koleksi buku Anda.</p>
                </div>
                <div class="footer-section">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('user.books') }}">Koleksi Buku</a></li>
                        <li><a href="{{ route('user.borrowings') }}">Peminjaman</a></li>
                        <li><a href="{{ route('user.history') }}">Riwayat</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Akun</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('user.account') }}">Profil Saya</a></li>
                        <li><a href="{{ route('user.favorites') }}">Favorit</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Bantuan</h4>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Digishelf. Platform Perpustakaan Digital Terpercaya.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const menu = document.getElementById('userDropdownMenu');
            dropdown.classList.toggle('open');
            menu.classList.toggle('show');
        }

        function toggleMobileNav() {
            document.getElementById('mobileNav').classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
                document.getElementById('userDropdownMenu').classList.remove('show');
            }
            const mobileNav = document.getElementById('mobileNav');
            const mobileToggle = document.getElementById('mobileToggle');
            if (mobileNav && mobileToggle && !mobileNav.contains(e.target) && !mobileToggle.contains(e.target)) {
                mobileNav.classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>