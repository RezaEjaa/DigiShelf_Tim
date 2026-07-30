<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digishelf')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --wood-dark: #5D4037;
            --wood-medium: #8D6E63;
            --wood-light: #A1887F;
            --cream: #F5F1E8;
            --text-dark: #3E2723;
            --sidebar-width: 260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--cream);
            overflow-x: hidden;
        }

        /* ===========================
           SIDEBAR OVERLAY (mobile)
        =========================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 999;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }

        /* ===========================
           SIDEBAR
        =========================== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #6D4C41 0%, #4E342E 100%);
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1),
                        width 0.3s ease;
            overflow: hidden;
        }

        /* Desktop collapsed */
        .sidebar.collapsed { width: 80px; }

        /* Logo */
        .sidebar-header {
            padding: 22px 20px;
            background: rgba(0,0,0,0.12);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
            min-height: 80px;
        }

        .sidebar-logo { width: 52px; height: 52px; flex-shrink: 0; }
        .sidebar-logo img { width: 100%; height: 100%; object-fit: contain; }

        .sidebar-title {
            font-family: 'Crimson Pro', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.2s;
        }
        .sidebar.collapsed .sidebar-title { opacity: 0; pointer-events: none; }

        /* Menu */
        .sidebar-menu {
            padding: 15px 0;
            list-style: none;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar-menu::-webkit-scrollbar { width: 3px; }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }

        .sidebar-menu li { margin-bottom: 3px; }

        .sidebar-menu a,
        .sidebar-menu button {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 22px;
            color: rgba(255,255,255,0.78);
            text-decoration: none;
            transition: background 0.2s, color 0.2s, padding-left 0.2s;
            font-size: 0.92rem;
            position: relative;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active,
        .sidebar-menu button:hover {
            background: rgba(255,255,255,0.12);
            color: white;
        }
        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: #D4A574;
            border-radius: 0 2px 2px 0;
        }
        .sidebar-menu a i,
        .sidebar-menu button i {
            font-size: 1.05rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar-menu a span,
        .sidebar-menu button span {
            opacity: 1;
            transition: opacity 0.2s;
            overflow: hidden;
        }

        /* Collapsed state: center icons, hide text */
        .sidebar.collapsed .sidebar-menu a,
        .sidebar.collapsed .sidebar-menu button {
            justify-content: center;
            padding: 13px 0;
            gap: 0;
        }
        .sidebar.collapsed .sidebar-menu a span,
        .sidebar.collapsed .sidebar-menu button span {
            opacity: 0;
            width: 0;
        }

        /* Logout */
        .logout-section { border-top: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
        .logout-form { margin: 0; }

        /* Collapse toggle (desktop only) */
        .sidebar-toggle {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            z-index: 10;
        }
        .sidebar-toggle:hover { background: rgba(255,255,255,0.22); }
        .sidebar-toggle i { transition: transform 0.3s; }
        .sidebar.collapsed .sidebar-toggle i { transform: rotate(180deg); }

        /* ===========================
           MAIN CONTENT
        =========================== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .sidebar.collapsed ~ .main-content { margin-left: 80px; }

        /* ===========================
           TOP BAR
        =========================== */
        .topbar {
            background: white;
            padding: 15px 28px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 990;
            gap: 12px;
        }

        /* Burger — hidden on desktop */
        .burger-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            min-width: 40px;
            background: var(--wood-dark);
            color: white;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .burger-btn:hover { background: var(--wood-medium); }

        .page-header { flex: 1; min-width: 0; }
        .page-header h1 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.7rem;
            color: var(--text-dark);
            margin-bottom: 2px;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .page-header p {
            color: #777;
            font-size: 0.82rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-info-top {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .user-avatar-top {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }
        .user-details { text-align: right; }
        .user-name { font-weight: 600; color: var(--text-dark); font-size: 0.88rem; white-space: nowrap; }
        .user-role { font-size: 0.75rem; color: #888; text-transform: capitalize; }

        /* Content */
        .content-area { padding: 28px; }

        /* ===========================
           TABLET (769px – 1024px)
        =========================== */
        @media (max-width: 1024px) and (min-width: 769px) {
            /* Auto-collapse on tablet */
            .sidebar:not(.expanded) { width: 80px; }
            .sidebar:not(.expanded) .sidebar-title { opacity: 0; pointer-events: none; }
            .sidebar:not(.expanded) .sidebar-menu a,
            .sidebar:not(.expanded) .sidebar-menu button {
                justify-content: center;
                padding: 13px 0;
                gap: 0;
            }
            .sidebar:not(.expanded) .sidebar-menu a span,
            .sidebar:not(.expanded) .sidebar-menu button span { opacity: 0; width: 0; }
            .sidebar:not(.expanded) .sidebar-toggle i { transform: rotate(180deg); }
            .sidebar:not(.expanded) ~ .main-content { margin-left: 80px; }

            .sidebar.expanded { width: var(--sidebar-width); }
            .sidebar.expanded ~ .main-content { margin-left: var(--sidebar-width); }

            /* Reset collapsed class behavior on tablet */
            .sidebar.collapsed { width: 80px; }
            .sidebar.collapsed ~ .main-content { margin-left: 80px; }

            .content-area { padding: 20px; }
            .burger-btn { display: none; }
        }

        /* ===========================
           MOBILE (≤768px)
        =========================== */
        @media (max-width: 768px) {
            /* Sidebar slides in from left */
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }
            .sidebar.show { transform: translateX(0); }

            /* Always full sidebar on mobile when shown */
            .sidebar .sidebar-title { opacity: 1 !important; pointer-events: auto !important; }
            .sidebar .sidebar-menu a,
            .sidebar .sidebar-menu button {
                justify-content: flex-start !important;
                padding: 13px 22px !important;
                gap: 14px !important;
            }
            .sidebar .sidebar-menu a span,
            .sidebar .sidebar-menu button span {
                opacity: 1 !important;
                width: auto !important;
            }

            /* Hide desktop collapse toggle on mobile */
            .sidebar-toggle { display: none !important; }

            /* Burger visible */
            .burger-btn { display: flex; }

            /* Main fills full width */
            .main-content { margin-left: 0 !important; }

            .topbar { padding: 12px 14px; }
            .page-header h1 { font-size: 1.25rem; }
            .page-header p { display: none; }
            .user-details { display: none; }

            .content-area { padding: 14px; }
        }

        /* ===========================
           SMALL MOBILE (≤480px)
        =========================== */
        @media (max-width: 480px) {
            .content-area { padding: 10px; }
            .topbar { padding: 10px 12px; }
            .page-header h1 { font-size: 1.1rem; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="{{ asset('img/ui/logo.png') }}" alt="Digishelf">
            </div>
            <span class="sidebar-title">Digishelf</span>
        </div>

        <ul class="sidebar-menu">
            @yield('sidebar-menu')
        </ul>

        <!-- Collapse toggle (desktop/tablet) -->
        <button class="sidebar-toggle" onclick="toggleCollapse()" aria-label="Toggle sidebar">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">

        <div class="topbar">
            <!-- Burger (mobile only) -->
            <button class="burger-btn" onclick="openSidebar()" aria-label="Buka menu">
                <i class="fas fa-bars"></i>
            </button>

            <div class="page-header">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p>@yield('page-subtitle', 'Sistem Manajemen Perpustakaan Digital')</p>
            </div>

            <div class="user-info-top">
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->role }}</div>
                </div>
                <div class="user-avatar-top">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script>
        const sidebar   = document.getElementById('sidebar');
        const overlay   = document.getElementById('sidebarOverlay');
        const MOBILE_BP = 768;
        const TABLET_BP = 1024;

        function isMobile()  { return window.innerWidth <= MOBILE_BP; }
        function isTablet()  { return window.innerWidth > MOBILE_BP && window.innerWidth <= TABLET_BP; }
        function isDesktop() { return window.innerWidth > TABLET_BP; }

        /* Mobile: open */
        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        /* Mobile: close */
        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        /* Desktop/Tablet: collapse toggle */
        function toggleCollapse() {
            if (isMobile()) return;

            if (isDesktop()) {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarState',
                    sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
            } else {
                // Tablet: toggle expanded (default is mini)
                sidebar.classList.toggle('expanded');
                localStorage.setItem('sidebarState',
                    sidebar.classList.contains('expanded') ? 'expanded' : 'collapsed');
            }
        }

        /* Restore state on load */
        document.addEventListener('DOMContentLoaded', function () {
            const saved = localStorage.getItem('sidebarState');
            if (isDesktop()) {
                if (saved === 'collapsed') sidebar.classList.add('collapsed');
            } else if (isTablet()) {
                if (saved === 'expanded') sidebar.classList.add('expanded');
            }
        });

        /* Clean up on resize */
        window.addEventListener('resize', function () {
            if (!isMobile()) {
                closeSidebar();
            }
        });

        /* ESC closes */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });
    </script>

    @stack('scripts')
</body>
</html>