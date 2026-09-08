@extends('layouts.app')

@section('title', 'Kelola Petugas - Digishelf')

@section('page-title', 'Kelola Petugas')
@section('page-subtitle', 'Manajemen petugas perpustakaan')

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
        <a href="{{ route('admin.users.index') }}" >
            <i class="fas fa-users"></i><span>Kelola Pengguna</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.petugas.index') }}" class="active">
            <i class="fas fa-user-tie"></i><span>Kelola Petugas</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.borrowings.history') }}" >
            <i class="fas fa-history"></i><span>Riwayat Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.backup.index') }}">
            <i class="fas fa-database"></i><span>Backup Database</span>
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

    .btn-primary {
        padding: 9px 18px;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    .petugas-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .petugas-table th {
        background: var(--cream);
        padding: 13px 14px;
        text-align: left;
        font-weight: 600;
        color: var(--text-dark);
        border-bottom: 2px solid #E0E0E0;
        font-size: 0.88rem;
        white-space: nowrap;
    }

    .petugas-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #F5F5F5;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .petugas-table tr:hover { background: #FAFAFA; }

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
        text-decoration: none;
        display: inline-block;
        margin-right: 4px;
    }
    .btn-edit { background: #E3F2FD; color: #1976D2; }
    .btn-edit:hover { background: #1976D2; color: white; }
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
        .petugas-table th,
        .petugas-table td { padding: 10px 10px; font-size: 0.82rem; }
    }

    @media (max-width: 480px) {
        .section { padding: 12px; }
        .col-registered { display: none; }
    }
</style>

@if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;padding:13px 16px;border-radius:10px;margin-bottom:16px;font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background:#FFEBEE;color:#C62828;padding:13px 16px;border-radius:10px;margin-bottom:16px;font-size:0.9rem;">
        {{ session('error') }}
    </div>
@endif

<div class="section">
    <div class="section-header">
        <h2>Daftar Petugas ({{ $petugas->total() }})</h2>
        <a href="{{ route('admin.petugas.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Petugas
        </a>
    </div>

    @if($petugas->count() > 0)
        <div class="table-responsive">
            <table class="petugas-table">
                <thead>
                    <tr>
                        <th>Petugas</th>
                        <th>Email</th>
                        <th class="col-registered">Terdaftar</th>
                        <th>Transaksi Diproses</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($petugas as $p)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $p->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $p->email }}</td>
                            <td class="col-registered">{{ $p->created_at->format('d M Y') }}</td>
                            <td>{{ $p->processed_count ?? 0 }}</td>
                            <td>
                                <a href="{{ route('admin.petugas.edit', $p) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.petugas.destroy', $p) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus petugas ini?')">
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

        @if($petugas->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Page {{ $petugas->currentPage() }} of {{ $petugas->lastPage() }}
                </div>
                <div class="pagination-controls">
                    @if($petugas->onFirstPage())
                        <span>&lt;</span>
                    @else
                        <a href="{{ $petugas->previousPageUrl() }}">&lt;</a>
                    @endif

                    @foreach(range(1, $petugas->lastPage()) as $page)
                        @if($page == $petugas->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $petugas->url($page) }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($petugas->hasMorePages())
                        <a href="{{ $petugas->nextPageUrl() }}">&gt;</a>
                    @else
                        <span>&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div style="text-align:center;padding:60px 20px;color:#999;">
            <i class="fas fa-user-tie" style="font-size:56px;opacity:0.3;"></i>
            <h3 style="margin-top:16px;font-size:1rem;">Belum ada petugas terdaftar</h3>
        </div>
    @endif
</div>
@endsection
