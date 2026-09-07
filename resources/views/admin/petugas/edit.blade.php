@extends('layouts.app')

@section('title', 'Edit Petugas - Digishelf')

@section('page-title', 'Edit Petugas')
@section('page-subtitle', 'Update data petugas perpustakaan')

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
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
    }

    .form-group input {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #E0E0E0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--wood-medium);
        box-shadow: 0 0 0 3px rgba(139, 111, 71, 0.1);
    }

    .form-group small {
        display: block;
        margin-top: 6px;
        color: #999;
        font-size: 0.8rem;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
    }

    .btn {
        padding: 11px 22px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-secondary {
        background: #E0E0E0;
        color: #666;
    }

    .btn-secondary:hover {
        background: #BDBDBD;
    }

    .error-message {
        background: #FFEBEE;
        color: #C62828;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-top: 6px;
    }

    @media (max-width: 768px) {
        .section { padding: 20px; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; text-align: center; }
    }
</style>

@if($errors->any())
    <div style="background:#FFEBEE;color:#C62828;padding:13px 16px;border-radius:10px;margin-bottom:16px;font-size:0.9rem;">
        <ul style="margin:0;padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="section">
    <form action="{{ route('admin.petugas.update', $petuga) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name', $petuga->name) }}" required>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $petuga->email) }}" required>
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" id="password" name="password">
            <small>Kosongkan jika tidak ingin mengubah password</small>
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection
