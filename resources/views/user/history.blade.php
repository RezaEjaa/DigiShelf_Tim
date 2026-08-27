@extends('layouts.app-navbar')
@section('title', 'Riwayat Peminjaman - Digishelf')

@section('content')
<style>
    :root { --wood-dark:#5D4037; --wood-medium:#8D6E63; --wood-light:#D7CCC8; --cream:#FFF8E1; --text-dark:#3E2723; }

    /* Filter tabs */
    .filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-tab {
        padding: 8px 18px; border-radius: 20px; font-size: 0.84rem;
        font-weight: 600; cursor: pointer; border: 2px solid #E0E0E0;
        background: white; color: #666; text-decoration: none;
        transition: all 0.2s;
    }
    .filter-tab:hover { border-color: var(--wood-medium); color: var(--wood-medium); }
    .filter-tab.active { background: var(--wood-dark); color: white; border-color: var(--wood-dark); }

    /* History card */
    .history-card {
        background: white; border-radius: 14px; padding: 20px 22px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.07); margin-bottom: 14px;
        border-left: 5px solid #E0E0E0; transition: all 0.25s;
    }
    .history-card:hover { box-shadow: 0 5px 18px rgba(0,0,0,0.11); }
    .history-card.returned  { border-left-color: #66BB6A; }
    .history-card.cancelled { border-left-color: #EF5350; }

    .history-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 12px; gap: 10px; flex-wrap: wrap;
    }
    .history-qr {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem; font-weight: 700; color: var(--wood-dark);
    }
    .history-date { font-size: 0.75rem; color: #aaa; margin-top: 2px; }

    /* Mini book covers */
    .mini-covers { display: flex; margin-bottom: 10px; }
    .mini-cover {
        width: 38px; height: 57px; border-radius: 4px;
        overflow: hidden; margin-right: -8px; border: 2px solid white;
        flex-shrink: 0; display: flex; align-items: center; justify-content: center;
        box-shadow: 1px 1px 5px rgba(0,0,0,0.15);
    }
    .mini-cover img { width: 100%; height: 100%; object-fit: cover; }
    .mini-cover i { font-size: 14px; color: rgba(255,255,255,0.6); }
    .mini-cover-more {
        width: 38px; height: 57px; border-radius: 4px;
        background: rgba(0,0,0,0.12); margin-right: -8px; border: 2px solid white;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.68rem; font-weight: 700; color: white;
        box-shadow: 1px 1px 5px rgba(0,0,0,0.15);
    }

    .history-books { font-size: 0.81rem; color: #555; margin-bottom: 8px; padding-left: 4px; }
    .history-meta { display: flex; gap: 14px; flex-wrap: wrap; font-size: 0.76rem; color: #999; }
    .history-meta span i { margin-right: 4px; color: var(--wood-light); }

    /* Badge */
    .badge { display: inline-block; padding: 4px 13px; border-radius: 20px;
        font-size: 0.74rem; font-weight: 600; white-space: nowrap; }
    .badge-returned  { background: #E8F5E9; color: #2E7D32; }
    .badge-cancelled { background: #FFEBEE; color: #C62828; }
    .badge-late      { background: #FFF3E0; color: #E65100; }
    .badge-group { display: flex; flex-direction: column; gap: 4px; align-items: flex-end; }

    /* Empty */
    .empty-state {
        text-align: center; padding: 70px 20px;
        background: white; border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06); color: #aaa;
    }
    .empty-state i { font-size: 58px; margin-bottom: 14px; opacity: 0.35; display: block; }
    .empty-state h3 { font-size: 1rem; color: #666; margin-bottom: 6px; }

    /* Pagination */
    .pagination-wrapper { margin-top: 20px; display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 10px; }
    .pagination-info { color: #94A3B8; font-size: 0.83rem; }
    .pagination-controls { display: flex; gap: 6px; flex-wrap: wrap; }
    .pagination-controls a, .pagination-controls span {
        min-width: 32px; height: 32px; display: flex; align-items: center;
        justify-content: center; border-radius: 7px; text-decoration: none; font-size: 0.83rem;
    }
    .pagination-controls a { background: #F1F5F9; color: #64748B; }
    .pagination-controls a:hover { background: var(--wood-medium); color: white; }
    .pagination-controls .active { background: var(--wood-dark); color: white; }

    @media (max-width: 768px) {
        .history-header { flex-direction: column; }
        .badge-group { align-items: flex-start; }
    }
    @media (max-width: 480px) {
        .history-card { padding: 16px; }
        .history-meta { flex-direction: column; gap: 4px; }
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
$filterStatus = request('status', 'all');
@endphp

{{-- Filter tabs --}}
<div class="filter-tabs">
    <a href="{{ route('user.history') }}" class="filter-tab {{ $filterStatus === 'all' ? 'active' : '' }}">
        Semua ({{ $totalAll }})
    </a>
    <a href="{{ route('user.history', ['status' => 'returned']) }}"
       class="filter-tab {{ $filterStatus === 'returned' ? 'active' : '' }}">
        <i class="fas fa-check-circle" style="color:#66BB6A;margin-right:4px;"></i> Selesai ({{ $totalReturned }})
    </a>
    <a href="{{ route('user.history', ['status' => 'cancelled']) }}"
       class="filter-tab {{ $filterStatus === 'cancelled' ? 'active' : '' }}">
        <i class="fas fa-times-circle" style="color:#EF5350;margin-right:4px;"></i> Dibatalkan ({{ $totalCancelled }})
    </a>
</div>

@if($requests->isEmpty())
    <div class="empty-state">
        <i class="fas fa-history"></i>
        <h3>Belum ada riwayat peminjaman</h3>
        <p style="font-size:0.85rem;">Riwayat muncul setelah peminjaman selesai atau dibatalkan.</p>
    </div>
@else
    @foreach($requests as $req)
        @php $books = $req->items->map(fn($i) => $i->book); @endphp
        <div class="history-card {{ $req->status }}">
            <div class="history-header">
                <div>
                    <div class="history-qr"><i class="fas fa-qrcode"></i> {{ $req->qr_code }}</div>
                    <div class="history-date">{{ $req->created_at->format('d M Y H:i') }}</div>
                </div>
                <div class="badge-group">
                    <span class="badge badge-{{ $req->status }}">{{ $req->statusLabel() }}</span>
                    @if($req->isReturned() && $req->returned_at && $req->returned_at->gt($req->return_date))
                        <span class="badge badge-late">Terlambat</span>
                    @endif
                </div>
            </div>

            {{-- Mini covers --}}
            <div class="mini-covers">
                @foreach($books->take(4) as $ci => $book)
                    <div class="mini-cover" style="background:{{ $colors[$ci % 5] }}; z-index:{{ 10 - $ci }}">
                        @if($book->cover_image)
                            <img src="{{ asset('img/covers/'.$book->cover_image) }}" alt="">
                        @else
                            <i class="fas fa-book"></i>
                        @endif
                    </div>
                @endforeach
                @if($books->count() > 4)
                    <div class="mini-cover-more">+{{ $books->count() - 4 }}</div>
                @endif
            </div>

            <div class="history-books">{{ $books->pluck('title')->join(' · ') }}</div>

            <div class="history-meta">
                <span><i class="fas fa-calendar-check"></i> Ambil: {{ $req->pickup_date->format('d M Y') }}</span>
                <span><i class="fas fa-calendar-times"></i> Kembali: {{ $req->return_date->format('d M Y') }}</span>
                @if($req->isReturned() && $req->returned_at)
                    <span><i class="fas fa-undo"></i> Dikembalikan: {{ $req->returned_at->format('d M Y') }}</span>
                @endif
                @if($req->isCancelled())
                    <span style="color:#EF5350;">
                        <i class="fas fa-times-circle"></i> Auto-batal (24 jam terlewat)
                    </span>
                @endif
                <span><i class="fas fa-book"></i> {{ $books->count() }} buku</span>
            </div>
        </div>
    @endforeach

    {{-- Pagination --}}
    @if($requests->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}
            </div>
            <div class="pagination-controls">
                @if($requests->onFirstPage()) <span>&lt;</span>
                @else <a href="{{ $requests->previousPageUrl() }}">&lt;</a> @endif

                @foreach(range(1, $requests->lastPage()) as $page)
                    @if($page == $requests->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $requests->url($page) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($requests->hasMorePages()) <a href="{{ $requests->nextPageUrl() }}">&gt;</a>
                @else <span>&gt;</span> @endif
            </div>
        </div>
    @endif
@endif
@endsection