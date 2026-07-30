@extends('layouts.app-navbar')
@section('title', 'Dashboard - Digishelf')

@section('content')
<style>
    /* ── HERO ── */
    .dash-hero {
        background: linear-gradient(150deg, #4A3728 0%, #3A2A1E 50%, #2E2018 100%);
        border-radius: 20px;
        padding: 50px 40px;
        margin-bottom: 35px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 30px;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .dash-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23CFBB99' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(212,165,116,0.15);
        border: 1px solid rgba(212,165,116,0.3);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 500;
        color: #D4A574;
        margin-bottom: 16px;
    }

    .hero-left h1 {
        font-family: 'Crimson Pro', serif;
        font-size: 2.4rem;
        font-weight: 700;
        color: #FFF8EE;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .hero-left p {
        font-size: 0.95rem;
        color: rgba(255,248,230,0.62);
        line-height: 1.7;
    }

    .hero-right {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 16px;
        padding: 28px 32px;
        text-align: center;
        min-width: 240px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .hero-logo {
        width: 64px;
        height: 64px;
    }
    .hero-logo img { width: 100%; height: 100%; object-fit: contain; }

    .hero-stats {
        display: flex;
        gap: 24px;
        width: 100%;
        justify-content: center;
    }

    .hero-stat-box {
        background: rgba(255,255,255,0.07);
        border-radius: 12px;
        padding: 14px 20px;
        flex: 1;
    }

    .hero-stat-num {
        font-family: 'Crimson Pro', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: #D4A574;
        line-height: 1;
        margin-bottom: 4px;
    }

    .hero-stat-label {
        font-size: 0.72rem;
        color: rgba(255,248,230,0.55);
    }

    /* ── SECTION ── */
    .section {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        margin-bottom: 30px;
    }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-head h2 {
        font-family: 'Crimson Pro', serif;
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .view-all {
        font-size: 0.88rem;
        color: var(--wood-medium);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }
    .view-all:hover { color: var(--wood-dark); }

    /* ── BOOKSHELF (sama seperti welcome) ── */
    .bookshelf-wrapper {
        background: linear-gradient(to bottom, #8B6F47 0%, #7A5F3D 50%, #6F5539 100%);
        border-radius: 20px;
        padding: 40px 25px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
        position: relative;
    }

    .bookshelf-wrapper::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: repeating-linear-gradient(90deg,transparent,transparent 2px,rgba(0,0,0,0.05) 2px,rgba(0,0,0,0.05) 4px);
        pointer-events: none;
        border-radius: 20px;
    }

    .books-display {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 15px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .book-item-dash {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 3px 3px 10px rgba(0,0,0,0.3);
        transition: all 0.3s;
        cursor: pointer;
        aspect-ratio: 2/3;
    }

    .book-item-dash:hover {
        transform: translateY(-10px) rotate(2deg);
        box-shadow: 5px 10px 20px rgba(0,0,0,0.4);
    }

    .book-cover-dash {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .book-cover-dash img {
        width: 100%; height: 100%;
        object-fit: cover;
    }

    .book-cover-dash i { font-size: 40px; color: rgba(255,255,255,0.4); }

    .shelf-line {
        height: 12px;
        background: linear-gradient(to bottom, #6F5539 0%, #5C4A31 50%, #6F5539 100%);
        border-radius: 4px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    /* ── BORROWING LIST ── */
    .borrowing-list { display: grid; gap: 14px; }

    .borrowing-item {
        background: var(--cream);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .borrowing-item:hover {
        transform: translateX(4px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    .borrowing-info h4 {
        font-size: 0.98rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .borrowing-info p {
        color: #777;
        font-size: 0.82rem;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .status-badge.active   { background: #E8F5E9; color: #2E7D32; }
    .status-badge.overdue  { background: #FFEBEE; color: #C62828; }
    .status-badge.due-soon { background: #FFF3E0; color: #E65100; }

    .due-date { font-size: 0.78rem; color: #999; }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #aaa;
    }
    .empty-state i { font-size: 52px; margin-bottom: 15px; opacity: 0.4; display: block; }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
        .books-display { grid-template-columns: repeat(4, 1fr); }
        .book-item-dash:nth-child(n+4) { display: none; }
        .dash-hero { grid-template-columns: 1fr; }
        .hero-right { min-width: auto; }
    }

    @media (max-width: 768px) {
        .dash-hero { padding: 36px 24px; }
        .hero-left h1 { font-size: 1.8rem; }
        .books-display { grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .book-item-dash:nth-child(n+4) { display: none; }
        .bookshelf-wrapper { padding: 28px 15px; }
        .borrowing-item { flex-direction: column; align-items: flex-start; gap: 12px; }
        .section { padding: 22px 18px; }
    }

    @media (max-width: 480px) {
        .books-display { grid-template-columns: repeat(2, 1fr); }
        .book-item-dash:nth-child(n+3) { display: none; }
        .hero-stats { gap: 12px; }
    }
</style>

{{-- HERO --}}
<div class="dash-hero">
    <div class="hero-left">
        <div class="hero-badge"><i class="fas fa-heart"></i> Halo, {{ Auth::user()->name }}!</div>
        <h1>Selamat Datang di<br>Perpustakaan Digital</h1>
        <p>Temukan koleksi buku terbaik, pantau peminjaman,<br>dan kelola favorit Anda dengan mudah.</p>
    </div>
    <div class="hero-right">
        <div class="hero-logo">
            <img src="{{ asset('img/ui/logo.png') }}" alt="Digishelf">
        </div>
        <div class="hero-stats">
            <div class="hero-stat-box">
                <div class="hero-stat-num">{{ $stats['total_books'] }}</div>
                <div class="hero-stat-label">Total Buku</div>
            </div>
            <div class="hero-stat-box">
                <div class="hero-stat-num">{{ $stats['active_borrowings'] }}</div>
                <div class="hero-stat-label">Peminjaman Anda</div>
            </div>
        </div>
    </div>
</div>

{{-- BUKU TERBARU --}}
<div class="section">
    <div class="section-head">
        <h2>Buku Terbaru</h2>
        <a href="{{ route('user.books') }}" class="view-all">Lihat Semua →</a>
    </div>

    <div class="bookshelf-wrapper">
        @php
            $colors = [
                'linear-gradient(135deg, #A1887F, #8D6E63)',
                'linear-gradient(135deg, #7986CB, #5C6BC0)',
                'linear-gradient(135deg, #81C784, #66BB6A)',
                'linear-gradient(135deg, #FFB74D, #FFA726)',
                'linear-gradient(135deg, #E57373, #EF5350)',
                'linear-gradient(135deg, #9575CD, #7E57C2)',
            ];
        @endphp
        <div class="books-display">
            @forelse($recommendedBooks as $index => $book)
                <div class="book-item-dash" onclick="window.location='{{ route('user.books') }}'">
                    <div class="book-cover-dash" style="background: {{ $colors[$index % 6] }};">
                        @if($book->cover_image)
                            <img src="{{ asset('img/covers/' . $book->cover_image) }}" alt="{{ $book->title }}">
                        @else
                            <i class="fas fa-book"></i>
                        @endif
                    </div>
                </div>
            @empty
                <p style="color:rgba(255,255,255,0.7); grid-column: 1/-1; text-align:center; padding:30px 0;">Belum ada buku</p>
            @endforelse
        </div>
        <div class="shelf-line"></div>
    </div>
</div>

{{-- SEDANG DIPINJAM --}}
<div class="section">
    <div class="section-head">
        <h2>Peminjaman Saya</h2>
        <a href="{{ route('user.borrowings') }}" class="view-all">Lihat Semua →</a>
    </div>

    @if($activeBorrowings->count() > 0)
        <div class="borrowing-list">
            @foreach($activeBorrowings as $req)
                @php $books = $req->items->map(fn($i) => $i->book); @endphp
                <a href="{{ route('user.borrowings.detail', $req->id) }}"
                   style="text-decoration:none; color:inherit; display:block;">
                    <div class="borrowing-item">
                        <div class="borrowing-info">
                            {{-- Judul buku (bisa lebih dari satu) --}}
                            <h4>
                                @if($books->count() === 1)
                                    {{ $books->first()->title }}
                                @else
                                    {{ $books->first()->title }}
                                    <span style="font-size:0.78rem;font-weight:500;color:var(--wood-medium);">
                                        +{{ $books->count() - 1 }} buku lainnya
                                    </span>
                                @endif
                            </h4>
                            <p>
                                <i class="fas fa-qrcode"></i>
                                <span style="font-family:'Courier New',monospace;font-size:0.78rem;">{{ $req->qr_code }}</span>
                            </p>
                            <p><i class="fas fa-calendar-check"></i> Ambil: {{ $req->pickup_date->format('d M Y') }}</p>
                        </div>
                        <div style="text-align:right; flex-shrink:0;">
                            @if($req->status === 'pending')
                                <span class="status-badge due-soon">Menunggu Verifikasi</span>
                                @if($req->expires_at)
                                    <div class="due-date">
                                        Batas: {{ $req->expires_at->format('d M H:i') }}
                                    </div>
                                @endif
                            @else
                                <span class="status-badge active">Sedang Dipinjam</span>
                                <div class="due-date">Kembali: {{ $req->return_date->format('d M Y') }}</div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <p>Belum ada peminjaman aktif</p>
        </div>
    @endif
</div>
@endsection