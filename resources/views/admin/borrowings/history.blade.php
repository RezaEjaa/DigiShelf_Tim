@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Digishelf')
@section('page-title', 'Riwayat Peminjaman')
@section('page-subtitle', 'Histori peminjaman yang telah selesai atau dibatalkan')

@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
    <li><a href="{{ route('admin.books.index') }}"><i class="fas fa-book"></i><span>Kelola Buku</span></a></li>
    <li><a href="{{ route('admin.books.create') }}"><i class="fas fa-plus-circle"></i><span>Tambah Buku</span></a></li>
    <li><a href="{{ route('admin.borrowings.index') }}"><i class="fas fa-exchange-alt"></i><span>Kelola Peminjaman</span></a></li>
    <li><a href="{{ route('admin.verify-qr.index') }}"><i class="fas fa-barcode"></i><span>Verifikasi Kode</span></a></li>
    <li><a href="{{ route('admin.borrowings.history') }}" class="active"><i class="fas fa-history"></i><span>Riwayat Peminjaman</span></a></li>
    <li><a href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i><span>Kelola Pengguna</span></a></li>
    <li class="logout-section">
        <form action="{{ url('/logout') }}" method="POST" class="logout-form">@csrf
            <button type="submit"><i class="fas fa-sign-out-alt"></i><span>Logout</span></button>
        </form>
    </li>
@endsection

@section('content')
<style>
    .section { background:white; border-radius:15px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
    .section-title { font-size:1.05rem; color:var(--text-dark); font-weight:600; margin-bottom:22px; }

    .table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .borrow-table { width:100%; border-collapse:collapse; min-width:620px; }
    .borrow-table th { background:var(--cream); padding:12px 14px; text-align:left; font-size:0.85rem; font-weight:600; color:var(--text-dark); border-bottom:2px solid #E0E0E0; white-space:nowrap; }
    .borrow-table td { padding:12px 14px; border-bottom:1px solid #F5F5F5; font-size:0.84rem; vertical-align:middle; }
    .borrow-table tr:hover { background:#FAFAFA; }

    .badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:600; white-space:nowrap; }
    .badge-returned  { background:#E8F5E9; color:#2E7D32; }
    .badge-cancelled { background:#FFEBEE; color:#C62828; }

    .qr-code { font-family:'Courier New',monospace; font-weight:700; color:var(--wood-dark); font-size:0.88rem; }
    .books-cell { max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .pagination-wrapper { margin-top:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
    .pagination-info { color:#94A3B8; font-size:0.84rem; }
    .pagination-controls { display:flex; gap:6px; flex-wrap:wrap; }
    .pagination-controls a, .pagination-controls span { min-width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:7px; text-decoration:none; font-size:0.84rem; }
    .pagination-controls a { background:#F1F5F9; color:#64748B; }
    .pagination-controls a:hover { background:var(--wood-medium); color:white; }
    .pagination-controls .active { background:var(--wood-dark); color:white; }

    @media (max-width:768px) { .section { padding:16px; } .borrow-table th, .borrow-table td { padding:10px; font-size:0.8rem; } }
    @media (max-width:480px) { .section { padding:12px; } }
</style>

<div class="section">
    <h2 class="section-title">Riwayat Peminjaman ({{ $requests->total() }})</h2>

    @if($requests->count() > 0)
        <div class="table-responsive">
            <table class="borrow-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Ambil</th>
                        <th>Tgl Kembali</th>
                        <th>Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr>
                            <td class="qr-code">{{ $req->qr_code }}</td>
                            <td>{{ $req->user->name }}</td>
                            <td class="books-cell" title="{{ $req->items->map(fn($i)=>$i->book->title)->join(', ') }}">
                                {{ $req->items->map(fn($i)=>$i->book->title)->join(', ') }}
                            </td>
                            <td>{{ $req->pickup_date->format('d M Y') }}</td>
                            <td>{{ $req->return_date->format('d M Y') }}</td>
                            <td>
                                @if($req->returned_at)
                                    {{ $req->returned_at->format('d M Y') }}
                                @elseif($req->isCancelled())
                                    <span style="color:#bbb;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $req->status }}">
                                    {{ $req->statusLabel() }}
                                </span>
                                @if($req->isReturned() && $req->returned_at && $req->returned_at->gt($req->return_date))
                                    <span class="badge" style="background:#FFEBEE;color:#C62828;margin-top:3px;">Terlambat</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">Page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}</div>
                <div class="pagination-controls">
                    @if($requests->onFirstPage()) <span>&lt;</span> @else <a href="{{ $requests->previousPageUrl() }}">&lt;</a> @endif
                    @foreach(range(1, $requests->lastPage()) as $page)
                        @if($page == $requests->currentPage()) <span class="active">{{ $page }}</span>
                        @else <a href="{{ $requests->url($page) }}">{{ $page }}</a> @endif
                    @endforeach
                    @if($requests->hasMorePages()) <a href="{{ $requests->nextPageUrl() }}">&gt;</a> @else <span>&gt;</span> @endif
                </div>
            </div>
        @endif
    @else
        <div style="text-align:center;padding:60px;color:#999;">
            <i class="fas fa-history" style="font-size:56px;opacity:0.3;"></i>
            <h3 style="margin-top:16px;font-size:1rem;">Belum ada riwayat peminjaman</h3>
        </div>
    @endif
</div>
@endsection