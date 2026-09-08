@extends('layouts.app')

@section('title', 'Dashboard Petugas - Digishelf')

@section('page-title', 'Dashboard Petugas')
@section('page-subtitle', 'Kelola peminjaman dan verifikasi buku')

@section('sidebar-menu')
    <li>
        <a href="{{ route('petugas.dashboard') }}" class="active">
            <i class="fas fa-th-large"></i><span>Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('petugas.borrowings.index') }}" >
            <i class="fas fa-exchange-alt"></i><span>Kelola Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('petugas.verify-qr.index') }}" >
            <i class="fas fa-barcode"></i><span>Verifikasi Kode</span>
        </a>
    </li>
    <li>
        <a href="{{ route('petugas.borrowings.history') }}" >
            <i class="fas fa-history"></i><span>Riwayat Peminjaman</span>
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
    .status-badge.pending { background: #FFF3E0; color: #E65100; }

    .due-date { color: #666; font-size: 0.75rem; }

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
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat-card { padding: 14px; }
        .stat-icon { width: 42px; height: 42px; margin-bottom: 10px; }
        .stat-icon i { font-size: 18px; }
        .stat-value { font-size: 1.4rem; }
        .stat-label { font-size: 0.78rem; }
    }
</style>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-value">{{ $stats['pending_verifications'] }}</div>
        <div class="stat-label">Menunggu Verifikasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
        <div class="stat-value">{{ $stats['active_borrowings'] }}</div>
        <div class="stat-label">Sedang Dipinjam</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value">{{ $stats['my_processed_today'] }}</div>
        <div class="stat-label">Diproses Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-tasks"></i></div>
        <div class="stat-value">{{ $stats['my_total_processed'] }}</div>
        <div class="stat-label">Total Transaksi Saya</div>
    </div>
</div>

<!-- Pending Verifications -->
<div class="section">
    <h2 class="section-title">Menunggu Verifikasi</h2>

    @if($pendingRequests->count() > 0)
        <div class="borrowing-list">
            @foreach($pendingRequests as $req)
                <div class="borrowing-item">
                    <div class="borrowing-info">
                        <h4 style="font-family:'Courier New',monospace;font-size:0.88rem;">{{ $req->qr_code }}</h4>
                        <p>Peminjam: <span class="borrower-name">{{ $req->user->name }}</span></p>
                        <p>Buku: {{ $req->items->map(fn($i)=>$i->book->title)->join(', ') }}</p>
                        <p>Ambil: {{ $req->pickup_date->format('d M Y') }}</p>
                    </div>
                    <div class="borrowing-status">
                        <span class="status-badge pending">Menunggu</span>
                        <p class="due-date">Kembali: {{ $req->return_date->format('d M Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-check"></i>
            <p>Tidak ada peminjaman yang menunggu verifikasi</p>
        </div>
    @endif
</div>

<!-- Active Borrowings -->
<div class="section">
    <h2 class="section-title">Peminjaman Aktif Terbaru</h2>

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
                        <span class="status-badge active">Dipinjam</span>
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
@endsection
