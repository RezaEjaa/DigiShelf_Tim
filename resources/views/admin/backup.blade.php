@extends('layouts.app')
@section('title', 'Backup Database - Digishelf')

@section('page-title', 'Backup Database')
@section('page-subtitle', 'Backup database berjalan otomatis setiap hari')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-th-large"></i><span>Dashboard</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.books.index') }}">
            <i class="fas fa-book"></i><span>Kelola Buku</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.books.create') }}">
            <i class="fas fa-plus-circle"></i><span>Tambah Buku</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.borrowings.index') }}">
            <i class="fas fa-exchange-alt"></i><span>Kelola Peminjaman</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.verify-qr.index') }}">
            <i class="fas fa-barcode"></i><span>Verifikasi Kode</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.borrowings.history') }}">
            <i class="fas fa-history"></i><span>Riwayat Peminjaman</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i><span>Kelola Pengguna</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.backup.index') }}" class="active">
            <i class="fas fa-database"></i><span>Backup Database</span>
        </a>
    </li>

    <li class="logout-section">
        <form action="{{ url('/logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </button>
        </form>
    </li>
@endsection

@section('content')

<style>
    .backup-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .backup-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .backup-header h2 {
        margin: 0 0 8px;
        font-size: 28px;
    }

    .backup-header p {
        margin: 0;
        opacity: .9;
    }

    .backup-status {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 22px;
        background: rgba(255,255,255,.15);
        padding: 15px 18px;
        border-radius: 14px;
    }

    .status-dot {
        width: 14px;
        height: 14px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 5px rgba(34,197,94,.2);
        flex-shrink: 0;
    }

    .status-text strong {
        display: block;
        font-size: 16px;
    }

    .status-text span {
        font-size: 13px;
        opacity: .85;
    }

    .backup-card {
        background: #fff;
        border-radius: 18px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,.07);
    }

    .backup-card h3 {
        margin: 0 0 8px;
        font-size: 20px;
        color: #222;
    }

    .backup-card-description {
        color: #777;
        margin-bottom: 20px;
    }

    .schedule-box {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .schedule-item {
        padding: 18px;
        background: #f7f8fc;
        border-radius: 14px;
    }

    .schedule-item i {
        font-size: 22px;
        margin-bottom: 10px;
        color: #667eea;
    }

    .schedule-item small {
        display: block;
        color: #888;
        margin-bottom: 5px;
    }

    .schedule-item strong {
        font-size: 16px;
        color: #333;
    }

    .backup-files {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .backup-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 16px;
        border: 1px solid #eee;
        border-radius: 14px;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
    }

    .file-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #667eea;
        border-radius: 12px;
        flex-shrink: 0;
    }

    .file-name {
        font-weight: 600;
        color: #333;
        word-break: break-all;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 14px;
        background: #667eea;
        color: white;
        text-decoration: none;
        border-radius: 9px;
        font-size: 13px;
        white-space: nowrap;
    }

    .download-btn:hover {
        color: white;
        opacity: .9;
    }

    .empty-backup {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }

    .empty-backup i {
        font-size: 45px;
        margin-bottom: 15px;
        color: #bbb;
    }

    .info-box {
        padding: 18px;
        border-radius: 14px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .info-box i {
        margin-right: 7px;
    }

    @media (max-width: 850px) {
        .schedule-box {
            grid-template-columns: 1fr;
        }

        .backup-header {
            padding: 25px;
        }
    }

    @media (max-width: 600px) {
        .backup-header {
            border-radius: 15px;
            padding: 20px;
        }

        .backup-header h2 {
            font-size: 23px;
        }

        .backup-card {
            padding: 18px;
            border-radius: 15px;
        }

        .backup-file {
            align-items: flex-start;
            flex-direction: column;
        }

        .download-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="backup-wrapper">

    <div class="backup-header">
        <h2>
            <i class="fas fa-database"></i>
            Backup Database
        </h2>

        <p>
            Sistem akan membuat backup database secara otomatis.
        </p>

        <div class="backup-status">
            <div class="status-dot"></div>

            <div class="status-text">
                <strong>Backup Otomatis Aktif</strong>
                <span>Backup dijadwalkan setiap hari pada pukul 00:00</span>
            </div>
        </div>
    </div>

    <div class="backup-card">
        <h3>Jadwal Backup</h3>

        <div class="backup-card-description">
            Tidak diperlukan tindakan manual dari administrator.
        </div>

        <div class="schedule-box">

            <div class="schedule-item">
                <i class="fas fa-clock"></i>
                <small>Frekuensi</small>
                <strong>Setiap Hari</strong>
            </div>

            <div class="schedule-item">
                <i class="fas fa-calendar-day"></i>
                <small>Waktu</small>
                <strong>00:00</strong>
            </div>

            <div class="schedule-item">
                <i class="fas fa-server"></i>
                <small>Penyimpanan</small>
                <strong>Server Lokal</strong>
            </div>

        </div>
    </div>
    <div class="backup-card">

        <h3>File Backup</h3>

        <div class="backup-card-description">
            File backup yang sudah dibuat oleh sistem.
        </div>
@if($backups->count() > 0)

    <div class="backup-files">

        @foreach($backups as $backup)

            <div class="backup-file">

                <div class="file-info">

                    <div class="file-icon">
                        <i class="fas fa-file-archive"></i>
                    </div>

                    <div>
                        <div class="file-name">
                            {{ basename($backup->path()) }}
                        </div>

                       <small style="color:#888;">
    Backup dibuat {{ $backup->date()->format('d/m/Y H:i:s') }}
</small>                    </div>

                </div>

                <a href="{{ route('admin.backup.download', basename($backup->path())) }}"
                   class="download-btn">
                    <i class="fas fa-download"></i>
                    Download
                </a>

            </div>

        @endforeach

    </div>

@else

    <div class="empty-backup">
        <i class="fas fa-database"></i>
        <p>Belum ada file backup.</p>
        <small>
            Backup akan dibuat otomatis setiap hari pukul 00:00.
        </small>
    </div>

@endif

    </div>

    <div class="info-box">
        <i class="fas fa-shield-alt"></i>
        <strong>Backup otomatis aktif.</strong>
        Database akan dicadangkan secara otomatis setiap hari tanpa perlu menekan tombol.
    </div>

</div>

@endsection
