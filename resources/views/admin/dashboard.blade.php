@extends('layouts.app')

@section('title', 'Dashboard Admin - Digishelf')

@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Kelola perpustakaan digital Anda')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}" class="active">
            <i class="fas fa-th-large"></i><span>Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.books.index') }}" >
            <i class="fas fa-book"></i><span>Kelola Buku</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.books.create') }}" >
            <i class="fas fa-plus-circle"></i><span>Tambah Buku</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.borrowings.index') }}" >
            <i class="fas fa-exchange-alt"></i><span>Kelola Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.verify-qr.index') }}" >
            <i class="fas fa-barcode"></i><span>Verifikasi Kode</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.borrowings.history') }}" >
            <i class="fas fa-history"></i><span>Riwayat Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}" >
            <i class="fas fa-users"></i><span>Kelola Pengguna</span>
        </a>
    </li>
    <li class="logout-section">
        <form action="{{ url('/logout') }}" method="POST" class="logout-form">@csrf
            <button type="submit"><i class="fas fa-sign-out-alt"></i><span>Logout</span></button>
        </form>
    </li>
@endsection

@section('content')
<style>
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 22px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border-left: 4px solid var(--wood-medium);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .stat-icon i { font-size: 24px; color: white; }

    .stat-value {
        font-size: 1.9rem;
        font-weight: 700;
        color: var(--wood-dark);
        margin-bottom: 4px;
        line-height: 1;
    }

    .stat-label { color: #666; font-size: 0.85rem; }

    /* Section */
    .section {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .section-title {
        font-family: 'Crimson Pro', serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--cream);
    }

    /* Borrowing Items */
    .borrowing-list { display: grid; gap: 12px; }

    .borrowing-item {
        background: var(--cream);
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        gap: 12px;
    }

    .borrowing-item:hover {
        transform: translateX(4px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .borrowing-info h4 {
        color: var(--text-dark);
        font-size: 0.95rem;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .borrowing-info p {
        color: #666;
        font-size: 0.8rem;
        margin: 0;
    }

    .borrower-name { color: var(--wood-medium); font-weight: 500; }

    .borrowing-status { text-align: right; flex-shrink: 0; }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .status-badge.active  { background: #E8F5E9; color: #2E7D32; }
    .status-badge.due-soon { background: #FFF3E0; color: #E65100; }
    .status-badge.overdue  { background: #FFEBEE; color: #C62828; }

    .due-date { color: #666; font-size: 0.75rem; }

    /* Bookshelf — sama persis dengan welcome.blade */
    .bookshelf-container {
        background: linear-gradient(to bottom, #8B6F47 0%, #7A5F3D 50%, #6F5539 100%);
        border-radius: 20px;
        padding: 50px 30px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
        position: relative;
    }

    .bookshelf-container::before {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(90deg,transparent,transparent 2px,rgba(0,0,0,0.05) 2px,rgba(0,0,0,0.05) 4px);
        pointer-events: none;
        border-radius: 20px;
    }

    /* Grid 6 kolom: 5 buku + 1 tombol tambah */
    .books-display {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
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
    }

    .book-item:hover {
        transform: translateY(-12px) rotate(2deg);
        box-shadow: 5px 12px 25px rgba(0,0,0,0.4);
    }

    .book-cover-display {
        width: 100%;
        aspect-ratio: 2 / 3;
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

    /* Tombol Tambah — ikut grid, aspect-ratio sama */
    .add-book-card {
        aspect-ratio: 2 / 3;
        background: transparent;
        border: 3px dashed rgba(255,255,255,0.5);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
    }

    .add-book-card:hover {
        border-color: rgba(255,255,255,0.85);
        background: rgba(255,255,255,0.1);
        transform: translateY(-5px);
    }

    .add-book-card i { font-size: 40px; color: rgba(255,255,255,0.6); margin-bottom: 8px; }
    .add-book-card span { color: rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 500; }

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
        bottom: -6px; left: 0; right: 0;
        height: 8px;
        background: rgba(0,0,0,0.2);
        filter: blur(6px);
    }

    .empty-state { text-align: center; padding: 40px 20px; color: #999; }
    .empty-state i { font-size: 48px; margin-bottom: 15px; opacity: 0.5; }

    /* ===========================
       RESPONSIVE
    =========================== */
    @media (max-width: 1024px) {
        .stats-grid { gap: 15px; }
        .stat-card { padding: 18px; }
        .stat-value { font-size: 1.6rem; }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .section { padding: 18px; }

        .borrowing-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .borrowing-status { text-align: left; }

        /* Tablet: 3 buku + tombol tambah tampil, buku ke-4 & 5 disembunyikan */
        .books-display {
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .book-item:nth-child(4),
        .book-item:nth-child(5) { display: none; }

        .bookshelf-container { padding: 35px 20px; border-radius: 16px; }
        .book-cover-display i { font-size: 34px; }
        .add-book-card i { font-size: 30px; }
        .add-book-card span { font-size: 0.75rem; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat-card { padding: 14px; }
        .stat-icon { width: 42px; height: 42px; margin-bottom: 10px; }
        .stat-icon i { font-size: 18px; }
        .stat-value { font-size: 1.4rem; }
        .stat-label { font-size: 0.78rem; }

        /* HP kecil: 2 buku + tombol tambah, buku ke-3/4/5 disembunyikan */
        .books-display {
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .book-item:nth-child(3),
        .book-item:nth-child(4),
        .book-item:nth-child(5) { display: none; }

        .bookshelf-container { padding: 25px 12px; border-radius: 14px; }
        .book-cover-display i { font-size: 28px; }
        .add-book-card i { font-size: 24px; }
        .add-book-card span { font-size: 0.7rem; }
    }
</style>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-book"></i></div>
        <div class="stat-value">{{ $stats['total_books'] }}</div>
        <div class="stat-label">Total Buku</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
        <div class="stat-value">{{ $stats['active_borrowings'] }}</div>
        <div class="stat-label">Sedang Dipinjam</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-value">{{ $stats['pending_borrowings'] }}</div>
        <div class="stat-label">Menunggu Verifikasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
        <div class="stat-label">Total Pengguna</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-value">{{ $stats['activity_rate'] }}%</div>
        <div class="stat-label">Tingkat Aktivitas</div>
    </div>
</div>

<!-- Active Borrowings -->
<div class="section">
    <h2 class="section-title">Peminjaman Aktif & Menunggu</h2>

    @if($activeBorrowings->count() > 0)
        <div class="borrowing-list">
            @foreach($activeBorrowings as $req)
                <div class="borrowing-item">
                    <div class="borrowing-info">
                        <h4 style="font-family:'Courier New',monospace;font-size:0.88rem;">{{ $req->qr_code }}</h4>
                        <p>Peminjam: <span class="borrower-name">{{ $req->user->name }}</span></p>
                        <p>Buku: {{ $req->items->map(fn($i)=>$i->book->title)->join(', ') }}</p>
                        <p>Ambil: {{ $req->pickup_date->format('d M Y') }}</p>
                    </div>
                    <div class="borrowing-status">
                        @if($req->status === 'pending')
                            <span class="status-badge due-soon">Menunggu</span>
                        @else
                            <span class="status-badge active">Dipinjam</span>
                        @endif
                        <p class="due-date">Kembali: {{ $req->return_date->format('d M Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <p>Tidak ada peminjaman aktif saat ini</p>
        </div>
    @endif
</div>

<!-- Recent Books -->
<div class="section">
    <h2 class="section-title">Koleksi Buku Terbaru</h2>

    <div class="bookshelf-container">
        @php
            $colors = [
                'linear-gradient(135deg, #A1887F, #8D6E63)',
                'linear-gradient(135deg, #7986CB, #5C6BC0)',
                'linear-gradient(135deg, #81C784, #66BB6A)',
                'linear-gradient(135deg, #FFB74D, #FFA726)',
                'linear-gradient(135deg, #E57373, #EF5350)',
            ];
        @endphp

        <div class="books-display">
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
            @endif

            <a href="{{ route('admin.books.create') }}" class="add-book-card">
                <i class="fas fa-plus"></i>
                <span>Tambah Buku</span>
            </a>
        </div>
        <div class="shelf-line"></div>
    </div>
</div>
@endsection