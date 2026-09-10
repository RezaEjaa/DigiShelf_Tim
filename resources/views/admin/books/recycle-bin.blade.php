@extends('layouts.app')

@section('title', 'Recycle Bin - Digishelf')
@section('page-title', 'Recycle Bin')
@section('page-subtitle', 'Kelola buku yang telah dihapus')

@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
    <li><a href="{{ route('admin.books.index') }}"><i class="fas fa-book"></i><span>Kelola Buku</span></a></li>
    <li><a href="{{ route('admin.books.create') }}"><i class="fas fa-plus-circle"></i><span>Tambah Buku</span></a></li>
    <li><a href="{{ route('admin.books.recycle-bin') }}" class="active"><i class="fas fa-trash-alt"></i><span>Recycle Bin</span></a></li>
    <li><a href="{{ route('admin.borrowings.index') }}"><i class="fas fa-exchange-alt"></i><span>Kelola Peminjaman</span></a></li>
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
    .recycle-section { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .recycle-table { width: 100%; border-collapse: collapse; min-width: 680px; }
    .recycle-table th { background: var(--cream); padding: 13px 14px; text-align: left; color: var(--text-dark); font-size: 0.84rem; border-bottom: 2px solid #E0E0E0; white-space: nowrap; }
    .recycle-table td { padding: 13px 14px; border-bottom: 1px solid #F0F0F0; font-size: 0.84rem; vertical-align: middle; }
    .recycle-table tr:hover { background: #FAFAFA; }
    .book-thumb { width: 46px; height: 62px; border-radius: 5px; object-fit: cover; background: linear-gradient(135deg, #A1887F, #8D6E63); vertical-align: middle; }
    .book-title { color: var(--text-dark); font-weight: 600; }
    .book-author { color: #777; font-size: 0.78rem; margin-top: 3px; }
    .deleted-at { color: #777; white-space: nowrap; }
    .action-group { display: flex; gap: 8px; flex-wrap: wrap; }
    .action-btn { border: none; border-radius: 7px; padding: 8px 11px; color: white; cursor: pointer; font-family: 'Poppins', sans-serif; font-size: 0.78rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .restore-btn { background: #6D8B54; }
    .restore-btn:hover { background: #55713F; }
    .delete-btn { background: #C62828; }
    .delete-btn:hover { background: #A51F1F; }
    .empty-state { text-align: center; padding: 55px 20px; color: #777; }
    .empty-state i { display: block; color: var(--wood-medium); font-size: 3rem; margin-bottom: 14px; }
    .table-responsive { overflow-x: auto; }
    .pagination-wrapper { margin-top: 20px; }
    .alert { padding: 13px 18px; border-radius: 10px; margin-bottom: 18px; }
    .alert-success { background: #E8F5E9; color: #2E7D32; border-left: 4px solid #2E7D32; }
</style>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="recycle-section">
    @if($books->count())
        <div class="table-responsive">
            <table class="recycle-table">
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>ISBN</th>
                        <th>Dihapus Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                        <tr>
                            <td>
                                @if($book->cover_image)
                                    <img class="book-thumb" src="{{ asset('img/covers/' . $book->cover_image) }}" alt="{{ $book->title }}">
                                @else
                                    <span class="book-thumb" style="display:inline-flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.6)"><i class="fas fa-book"></i></span>
                                @endif
                                <span style="display:inline-block;margin-left:10px;vertical-align:middle">
                                    <span class="book-title">{{ $book->title }}</span>
                                    <span class="book-author">{{ $book->author }}</span>
                                </span>
                            </td>
                            <td>{{ $book->isbn ?: '-' }}</td>
                            <td class="deleted-at">{{ $book->deleted_at->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="action-group">
                                    <form action="{{ route('admin.books.restore', $book->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-btn restore-btn"><i class="fas fa-undo"></i> Pulihkan</button>
                                    </form>
                                    <form action="{{ route('admin.books.force-delete', $book->id) }}" method="POST" onsubmit="return confirm('Buku ini akan dihapus permanen dari database. Lanjutkan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn"><i class="fas fa-trash"></i> Hapus Permanen</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $books->links() }}</div>
    @else
        <div class="empty-state">
            <i class="fas fa-trash-alt"></i>
            <p>Recycle Bin masih kosong.</p>
        </div>
    @endif
</div>
@endsection
