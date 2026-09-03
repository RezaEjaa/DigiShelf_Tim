@extends('layouts.app-navbar')
@section('title', 'Peminjaman - Digishelf')

@section('content')
<style>
    :root { --wood-dark:#5D4037; --wood-medium:#8D6E63; --wood-light:#D7CCC8; --cream:#FFF8E1; --text-dark:#3E2723; }

    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .page-header h2 {
        font-family: 'Crimson Pro', serif;
        font-size: 1.6rem; font-weight: 700; color: var(--text-dark);
    }
    .page-header p { color: #777; font-size: 0.88rem; margin-top: 2px; }

    .btn-new {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white; padding: 11px 22px; border-radius: 10px;
        font-size: 0.9rem; font-weight: 600; text-decoration: none;
        transition: all 0.3s; white-space: nowrap;
    }
    .btn-new:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(93,64,55,0.3); }

    /* ── Kartu peminjaman ── */
    .borrow-card {
        background: white; border-radius: 15px; padding: 20px 22px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 16px;
        cursor: pointer; transition: all 0.25s;
        border-left: 5px solid #E0E0E0;
        display: flex; gap: 18px; align-items: flex-start;
        text-decoration: none; color: inherit;
    }
    .borrow-card:hover { transform: translateX(5px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    .borrow-card.pending { border-left-color: #FFA726; }
    .borrow-card.active  { border-left-color: #66BB6A; }

    /* Mini book covers */
    .mini-covers { display: flex; flex-shrink: 0; }
    .mini-cover {
        width: 42px; height: 63px; border-radius: 5px;
        overflow: hidden; box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
        margin-right: -10px; border: 2px solid white; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .mini-cover img { width: 100%; height: 100%; object-fit: cover; }
    .mini-cover i { font-size: 16px; color: rgba(255,255,255,0.6); }
    .mini-cover-more {
        width: 42px; height: 63px; border-radius: 5px;
        background: rgba(0,0,0,0.15); display: flex;
        align-items: center; justify-content: center;
        font-size: 0.72rem; font-weight: 700; color: white;
        margin-right: -10px; border: 2px solid white;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
    }

    .borrow-info { flex: 1; min-width: 0; }
    .borrow-qr {
        font-family: 'Courier New', monospace;
        font-size: 0.92rem; font-weight: 700; color: var(--wood-dark);
        margin-bottom: 5px;
    }
    .borrow-books { font-size: 0.82rem; color: #555; margin-bottom: 7px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .borrow-dates { display: flex; gap: 14px; flex-wrap: wrap; font-size: 0.76rem; color: #888; }
    .borrow-dates span i { margin-right: 4px; color: var(--wood-light); }

    .borrow-status { flex-shrink: 0; text-align: right; }

    /* Badge */
    .badge { display: inline-block; padding: 5px 13px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600; white-space: nowrap; }
    .badge-pending   { background: #FFF3E0; color: #E65100; }
    .badge-active    { background: #E8F5E9; color: #2E7D32; }

    .countdown { font-size: 0.72rem; color: #E65100; margin-top: 5px; font-weight: 500; }

    /* Empty */
    .empty-state { text-align: center; padding: 70px 20px;
        background: white; border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06); color: #aaa; }
    .empty-state i { font-size: 60px; margin-bottom: 16px; opacity: 0.35; display: block; }
    .empty-state h3 { font-size: 1.05rem; margin-bottom: 8px; color: #666; }
    .empty-state p  { font-size: 0.86rem; margin-bottom: 20px; }

    @media (max-width: 768px) {
        .borrow-card { flex-direction: column; gap: 14px; }
        .borrow-status { text-align: left; }
        .page-header { flex-direction: column; align-items: flex-start; }
    }
    @media (max-width: 480px) {
        .borrow-dates { flex-direction: column; gap: 4px; }
    }
</style>

@php
$colors = [
    'linear-gradient(135deg,#A1887F,#8D6E63)',
    'linear-gradient(135deg,#7986CB,#5C6BC0)',
    'linear-gradient(135deg,#81C784,#66BB6A)',
    'linear-gradient(135deg,#FFB74D,#FFA726)',
    'linear-gradient(135deg,#E57373,#EF5350)',
];
@endphp

<div class="page-header">
    <div>
        <h2>Peminjaman Saya</h2>
        <p>{{ $requests->count() }} aktif / menunggu verifikasi</p>
    </div>
    <a href="{{ route('borrow.form') }}" class="btn-new">
        <i class="fas fa-plus"></i> Pinjam Buku Baru
    </a>
</div>

@if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;border-radius:9px;padding:13px 16px;margin-bottom:16px;font-size:0.88rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if($requests->isEmpty())
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <h3>Tidak ada peminjaman aktif</h3>
        <p>Anda belum memiliki peminjaman yang sedang berjalan atau menunggu verifikasi.</p>
    </div>
@else
    @foreach($requests as $req)
        @php $books = $req->items->map(fn($i) => $i->book); @endphp
        <a href="{{ route('user.borrowings.detail', $req->id) }}" class="borrow-card {{ $req->status }}">

            {{-- Mini cover stack --}}
            <div class="mini-covers">
                @foreach($books->take(3) as $ci => $book)
                    <div class="mini-cover" style="background:{{ $colors[$ci % 5] }}; z-index:{{ 10 - $ci }}">
                        @if($book->cover_image)
                            <img src="{{ asset('img/covers/'.$book->cover_image) }}" alt="">
                        @else
                            <i class="fas fa-book"></i>
                        @endif
                    </div>
                @endforeach
                @if($books->count() > 3)
                    <div class="mini-cover-more">+{{ $books->count() - 3 }}</div>
                @endif
            </div>

            {{-- Info --}}
            <div class="borrow-info">
                <div class="borrow-qr"><i class="fas fa-qrcode"></i> {{ $req->qr_code }}</div>
                <div class="borrow-books">{{ $books->pluck('title')->join(', ') }}</div>
                <div class="borrow-dates">
                    <span><i class="fas fa-calendar-check"></i> Ambil: {{ $req->pickup_date->format('d M Y') }}</span>
                    <span><i class="fas fa-calendar-times"></i> Kembali: {{ $req->return_date->format('d M Y') }}</span>
                </div>
            </div>

            {{-- Status --}}
            <div class="borrow-status">
                <span class="badge badge-{{ $req->status }}">{{ $req->statusLabel() }}</span>
                @if($req->isPending() && $req->expires_at)
                    <div class="countdown" data-expires="{{ $req->expires_at->toIso8601String() }}">
                        <i class="fas fa-clock"></i> Menghitung…
                    </div>
                @endif
            </div>
        </a>
    @endforeach
@endif

<script>
document.querySelectorAll('.countdown[data-expires]').forEach(el => {
    const expires = new Date(el.dataset.expires);
    function tick() {
        const diff = expires - Date.now();
        if (diff <= 0) {
            el.innerHTML = '⚠ Kadaluarsa';
            el.style.color = '#C62828';
            return;
        }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.innerHTML = `<i class="fas fa-clock"></i> ${h}j ${m}m ${s}d`;
        setTimeout(tick, 1000);
    }
    tick();
});
</script>
@endsection