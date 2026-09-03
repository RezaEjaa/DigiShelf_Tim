@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Digishelf')
@section('page-title', 'Kelola Peminjaman')
@section('page-subtitle', 'Peminjaman aktif dan menunggu verifikasi')

@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
    <li><a href="{{ route('admin.books.index') }}"><i class="fas fa-book"></i><span>Kelola Buku</span></a></li>
    <li><a href="{{ route('admin.books.create') }}"><i class="fas fa-plus-circle"></i><span>Tambah Buku</span></a></li>
    <li><a href="{{ route('admin.borrowings.index') }}" class="active"><i class="fas fa-exchange-alt"></i><span>Kelola Peminjaman</span></a></li>
    <li><a href="{{ route('admin.verify-qr.index') }}"><i class="fas fa-barcode"></i><span>Verifikasi Kode</span></a></li>
    <li><a href="{{ route('admin.borrowings.history') }}"><i class="fas fa-history"></i><span>Riwayat Peminjaman</span></a></li>
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
    .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; flex-wrap:wrap; gap:10px; }
    .section-header h2 { font-size:1.05rem; color:var(--text-dark); }

    .btn-qr {
        display:inline-flex; align-items:center; gap:8px;
        background:linear-gradient(135deg,var(--wood-medium),var(--wood-dark));
        color:white; padding:10px 18px; border-radius:9px;
        font-size:0.85rem; font-weight:600; text-decoration:none;
        transition:all 0.3s;
    }
    .btn-qr:hover { transform:translateY(-1px); box-shadow:0 5px 14px rgba(93,64,55,0.3); }

    .table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .borrow-table { width:100%; border-collapse:collapse; min-width:640px; }
    .borrow-table th { background:var(--cream); padding:12px 14px; text-align:left; font-size:0.85rem; font-weight:600; color:var(--text-dark); border-bottom:2px solid #E0E0E0; white-space:nowrap; }
    .borrow-table td { padding:12px 14px; border-bottom:1px solid #F5F5F5; font-size:0.84rem; vertical-align:middle; }
    .borrow-table tr:hover { background:#FAFAFA; }

    .badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:600; white-space:nowrap; }
    .badge-pending   { background:#FFF3E0; color:#E65100; }
    .badge-active    { background:#E8F5E9; color:#2E7D32; }

    .qr-code { font-family:'Courier New',monospace; font-weight:700; color:var(--wood-dark); font-size:0.88rem; }

    .btn-action { padding:5px 11px; border-radius:6px; border:none; cursor:pointer; font-size:0.78rem; white-space:nowrap; margin-right:4px; }
    .btn-verify { background:#E8F5E9; color:#2E7D32; }
    .btn-verify:hover { background:#2E7D32; color:white; }
    .btn-ret { background:#EDE7F6; color:#4527A0; }
    .btn-ret:hover { background:#4527A0; color:white; }

    .books-cell { max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .pagination-wrapper { margin-top:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
    .pagination-info { color:#94A3B8; font-size:0.84rem; }
    .pagination-controls { display:flex; gap:6px; flex-wrap:wrap; }
    .pagination-controls a, .pagination-controls span { min-width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:7px; text-decoration:none; font-size:0.84rem; }
    .pagination-controls a { background:#F1F5F9; color:#64748B; }
    .pagination-controls a:hover { background:var(--wood-medium); color:white; }
    .pagination-controls .active { background:var(--wood-dark); color:white; }

    @media (max-width:768px) { .section { padding:16px; } }
</style>

@if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;padding:13px 16px;border-radius:10px;margin-bottom:16px;font-size:0.88rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="section">
    <div class="section-header">
        <h2>Peminjaman Aktif & Menunggu ({{ $requests->total() }})</h2>
        <a href="{{ route('admin.verify-qr.index') }}" class="btn-qr">
            <i class="fas fa-barcode"></i> Verifikasi Kode
        </a>
    </div>

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
                        <th>Status</th>
                        <th>Aksi</th>
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
                            <td><span class="badge badge-{{ $req->status }}">{{ $req->statusLabel() }}</span></td>
                            <td>
                                @if($req->isPending())
                                    <form action="{{ route('admin.verify-qr.confirm') }}" method="POST" style="display:inline;" data-confirm="Konfirmasi peminjaman dan serahkan buku ke user?">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $req->id }}">
                                        <button type="submit" class="btn-action btn-verify">
                                            <i class="fas fa-check"></i> Verifikasi
                                        </button>
                                    </form>
                                @elseif($req->isActive())
                                    <form action="{{ route('admin.verify-qr.return') }}" method="POST" style="display:inline;" data-confirm="Konfirmasi buku telah dikembalikan?">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $req->id }}">
                                        <button type="submit" class="btn-action btn-ret">
                                            <i class="fas fa-undo"></i> Kembalikan
                                        </button>
                                    </form>
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
            <i class="fas fa-book-open" style="font-size:56px;opacity:0.3;"></i>
            <h3 style="margin-top:16px;font-size:1rem;">Tidak ada peminjaman aktif</h3>
        </div>
    @endif
</div>
@endsection