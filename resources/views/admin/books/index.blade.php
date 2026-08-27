@extends('layouts.app')

@section('title', 'Kelola Buku - Digishelf')

@section('page-title', 'Kelola Buku')
@section('page-subtitle', 'Manajemen koleksi buku perpustakaan')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.books.index') }}" class="active">
            <i class="fas fa-book"></i>
            <span>Kelola Buku</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.books.create') }}">
            <i class="fas fa-plus-circle"></i>
            <span>Tambah Buku</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.borrowings.index') }}">
            <i class="fas fa-exchange-alt"></i>
            <span>Kelola Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.verify-qr.index') }}">
            <i class="fas fa-barcode"></i>
            <span>Verifikasi Kode</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.borrowings.history') }}">
            <i class="fas fa-history"></i>
            <span>Riwayat Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i>
            <span>Kelola Pengguna</span>
        </a>
    </li>
    <li class="logout-section">
        <form action="{{ url('/logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </li>
@endsection

@section('content')
<style>
    .alert {
        padding: 13px 18px;
        border-radius: 10px;
        margin-bottom: 18px;
    }
    .alert-success {
        background: #E8F5E9;
        color: #2E7D32;
        border-left: 4px solid #2E7D32;
    }
    .alert-error {
        background: #FFEBEE;
        color: #C62828;
        border-left: 4px solid #C62828;
    }
</style>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

{{-- Books Grid Component — already has its own responsive styles --}}
@include('components.books-grid', ['books' => $books, 'showSearch' => true])
@endsection