@extends('layouts.app')

@section('title', 'Kelola Pengguna - Digishelf')

@section('page-title', 'Kelola Pengguna')
@section('page-subtitle', 'Manajemen anggota perpustakaan')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}" >
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
        <a href="{{ route('admin.users.index') }}" class="active">
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
    .section {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .section-header h2 { font-size: 1.1rem; color: var(--text-dark); }

    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 500px;
    }

    .users-table th {
        background: var(--cream);
        padding: 13px 14px;
        text-align: left;
        font-weight: 600;
        color: var(--text-dark);
        border-bottom: 2px solid #E0E0E0;
        font-size: 0.88rem;
        white-space: nowrap;
    }

    .users-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #F5F5F5;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .users-table tr:hover { background: #FAFAFA; }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-action {
        padding: 5px 11px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 0.8rem;
        white-space: nowrap;
    }
    .btn-delete { background: #FFEBEE; color: #C62828; }
    .btn-delete:hover { background: #C62828; color: white; }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pagination-info { color: #94A3B8; font-size: 0.85rem; }
    .pagination-controls { display: flex; gap: 6px; flex-wrap: wrap; }

    .pagination-controls a,
    .pagination-controls span {
        min-width: 33px;
        height: 33px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        text-decoration: none;
        font-size: 0.85rem;
    }
    .pagination-controls a { background: #F1F5F9; color: #64748B; }
    .pagination-controls a:hover { background: var(--wood-medium); color: white; }
    .pagination-controls .active { background: var(--wood-dark); color: white; }

    /* ===========================
       RESPONSIVE
    =========================== */
    @media (max-width: 768px) {
        .section { padding: 16px; }
        .users-table th,
        .users-table td { padding: 10px 10px; font-size: 0.82rem; }
    }

    @media (max-width: 480px) {
        .section { padding: 12px; }
        /* Hide less important columns on small phones */
        .col-registered { display: none; }
    }
</style>

@if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;padding:13px 16px;border-radius:10px;margin-bottom:16px;font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif

<div class="section">
    <div class="section-header">
        <h2>Daftar Pengguna ({{ $users->total() }})</h2>
    </div>

    @if($users->count() > 0)
        <div class="table-responsive">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th class="col-registered">Terdaftar</th>
                        <th>Pinjaman</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td class="col-registered">{{ $user->created_at->format('d M Y') }}</td>
                            <td>{{ $user->borrowings->count() }}</td>
                            <td>
                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </div>
                <div class="pagination-controls">
                    @if($users->onFirstPage())
                        <span>&lt;</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}">&lt;</a>
                    @endif

                    @foreach(range(1, $users->lastPage()) as $page)
                        @if($page == $users->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $users->url($page) }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}">&gt;</a>
                    @else
                        <span>&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div style="text-align:center;padding:60px 20px;color:#999;">
            <i class="fas fa-users" style="font-size:56px;opacity:0.3;"></i>
            <h3 style="margin-top:16px;font-size:1rem;">Belum ada pengguna terdaftar</h3>
        </div>
    @endif
</div>
@endsection