@extends('layouts.app')

@section('title', 'Verifikasi Kode - Digishelf')
@section('page-title', 'Verifikasi Kode')
@section('page-subtitle', 'Input kode peminjaman dari user')

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
        <a href="{{ route('admin.verify-qr.index') }}" class="active">
            <i class="fas fa-barcode"></i><span>Verifikasi Kode</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.borrowings.history') }}" >
            <i class="fas fa-history"></i><span>Riwayat Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}" >
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
    :root { --wood-dark:#5D4037; --wood-medium:#8D6E63; --wood-light:#D7CCC8; --cream:#FFF8E1; --text-dark:#3E2723; }

    /* ── Layout ── */
    .top-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    .card {
        background: white; border-radius: 16px; padding: 26px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    }
    .card-title {
        font-family: 'Crimson Pro', serif;
        font-size: 1.2rem; font-weight: 700; color: var(--text-dark);
        margin-bottom: 18px; padding-bottom: 11px;
        border-bottom: 2px solid var(--cream);
        display: flex; align-items: center; gap: 9px;
    }
    .card-title i { color: var(--wood-medium); }

    /* ── Input kode ── */
    .code-input-wrapper { display: flex; gap: 10px; }

    .code-input {
        flex: 1; padding: 14px 18px;
        border: 2px solid #E0E0E0; border-radius: 10px;
        font-size: 1rem; font-family: 'Courier New', monospace;
        font-weight: 700; letter-spacing: 2px;
        text-transform: uppercase; transition: border-color 0.2s;
        background: #FAFAFA;
    }
    .code-input:focus {
        outline: none; border-color: var(--wood-medium);
        background: white;
        box-shadow: 0 0 0 3px rgba(141,110,99,0.12);
    }

    .btn-cek {
        padding: 14px 24px;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white; border: none; border-radius: 10px;
        font-size: 0.92rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        transition: all 0.3s; white-space: nowrap;
    }
    .btn-cek:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(93,64,55,0.3); }

    /* Hint prefix */
    .input-hint {
        margin-top: 10px; font-size: 0.8rem; color: #aaa;
        font-family: 'Courier New', monospace;
    }
    .input-hint span { color: var(--wood-medium); font-weight: 700; }

    /* ── Alert ── */
    .alert { border-radius: 10px; padding: 13px 17px; font-size: 0.88rem; margin-bottom: 20px; }
    .alert-success { background:#E8F5E9; color:#2E7D32; }
    .alert-error   { background:#FFEBEE; color:#C62828; }
    .alert-info    { background:#E3F2FD; color:#1565C0; }

    /* ── Result card ── */
    .result-card {
        background: white; border-radius: 16px; padding: 26px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        margin-bottom: 28px; border-left: 5px solid var(--wood-medium);
    }
    .result-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 18px; flex-wrap: wrap; gap: 12px;
    }
    .result-code {
        font-family: 'Courier New', monospace;
        font-size: 1.2rem; font-weight: 700; color: var(--wood-dark);
    }

    .badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-pending   { background:#FFF3E0; color:#E65100; }
    .badge-active    { background:#E8F5E9; color:#2E7D32; }
    .badge-cancelled { background:#FFEBEE; color:#C62828; }
    .badge-returned  { background:#EDE7F6; color:#4527A0; }

    .result-info-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
        margin-bottom: 18px;
    }
    .result-info-item { font-size: 0.86rem; }
    .result-info-item .lbl { color: #888; margin-bottom: 3px; }
    .result-info-item .val { font-weight: 600; color: var(--text-dark); }

    /* Buku list */
    .book-row {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 0; border-bottom: 1px solid #F5F5F5;
    }
    .book-row:last-child { border-bottom: none; }
    .b-thumb {
        width: 42px; height: 63px; border-radius: 5px;
        overflow: hidden; flex-shrink: 0;
        box-shadow: 1px 1px 5px rgba(0,0,0,0.15);
        display: flex; align-items: center; justify-content: center;
    }
    .b-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .b-thumb i   { font-size: 18px; color: rgba(255,255,255,0.6); }
    .b-info { flex: 1; min-width: 0; }
    .b-title  { font-size: 0.86rem; font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .b-author { font-size: 0.75rem; color: #888; }

    /* Action buttons */
    .action-row { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
    .btn-confirm {
        padding: 12px 26px;
        background: linear-gradient(135deg, #66BB6A, #388E3C);
        color: white; border: none; border-radius: 10px;
        font-size: 0.9rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 8px; transition: all 0.3s;
    }
    .btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(56,142,60,0.3); }

    .btn-return {
        padding: 12px 26px;
        background: linear-gradient(135deg, #7986CB, #3949AB);
        color: white; border: none; border-radius: 10px;
        font-size: 0.9rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 8px; transition: all 0.3s;
    }
    .btn-return:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(57,73,171,0.3); }

    /* ── Responsive ── */
    @media (max-width: 1024px) { .top-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
        .result-info-grid { grid-template-columns: 1fr; }
        .action-row { flex-direction: column; }
        .btn-confirm, .btn-return { width: 100%; justify-content: center; }
        .code-input-wrapper { flex-direction: column; }
        .btn-cek { width: 100%; justify-content: center; }
    }
</style>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
@endif
@if(session('kode_error'))
    <div class="alert alert-error"><i class="fas fa-barcode"></i> {{ session('kode_error') }}</div>
@endif
@if(session('kode_info'))
    <div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ session('kode_info') }}</div>
@endif

{{-- ── Input Kode + Panduan ── --}}
<div class="top-grid">

    {{-- Input kode --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-keyboard"></i> Input Kode Peminjaman
        </div>

        <form action="{{ route('admin.verify-qr.verify') }}" method="POST">
            @csrf
            <div class="code-input-wrapper">
                <input type="text"
                       name="qr_code"
                       class="code-input"
                       placeholder="DIGI-XXXXXXXX"
                       value="{{ old('qr_code') }}"
                       autocomplete="off"
                       autofocus
                       spellcheck="false">
                <button type="submit" class="btn-cek">
                    <i class="fas fa-search"></i> Cek
                </button>
            </div>
            <div class="input-hint">
                Format kode: <span>DIGI-XXXXXXXX</span> (ada di halaman detail peminjaman user)
            </div>
        </form>
    </div>

    {{-- Panduan --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-book-open"></i> Panduan Verifikasi
        </div>
        <div style="font-size:0.86rem;color:#555;line-height:1.9;">
            <p style="font-weight:700;color:var(--text-dark);margin-bottom:8px;">Verifikasi Peminjaman (Serah Buku):</p>
            <ol style="padding-left:20px;margin-bottom:16px;">
                <li>User membuka halaman <strong>Detail Peminjaman</strong> di aplikasi</li>
                <li>User menunjukkan <strong>Kode DIGI-XXXXXXXX</strong> kepada petugas</li>
                <li>Petugas ketik kode di kolom kiri → klik <strong>Cek</strong></li>
                <li>Periksa data peminjaman yang muncul</li>
                <li>Klik <strong style="color:#388E3C;">Konfirmasi & Serahkan Buku</strong></li>
            </ol>

            <p style="font-weight:700;color:var(--text-dark);margin-bottom:8px;">Pengembalian Buku:</p>
            <ol style="padding-left:20px;">
                <li>User menunjukkan kode yang sama saat mengembalikan</li>
                <li>Ketik kode → klik <strong>Cek</strong></li>
                <li>Klik <strong style="color:#3949AB;">Konfirmasi Pengembalian</strong></li>
                <li>Stok buku otomatis bertambah</li>
            </ol>
        </div>
    </div>
</div>

{{-- ── Hasil setelah input kode ── --}}
@if(session('kode_result'))
    @php
        $r = session('kode_result');
        $colors = [
            'linear-gradient(135deg,#A1887F,#8D6E63)',
            'linear-gradient(135deg,#7986CB,#5C6BC0)',
            'linear-gradient(135deg,#81C784,#66BB6A)',
            'linear-gradient(135deg,#FFB74D,#FFA726)',
            'linear-gradient(135deg,#E57373,#EF5350)',
        ];
    @endphp

    <div class="result-card">
        <div class="result-header">
            <div>
                <div class="result-code">
                    <i class="fas fa-barcode"></i> {{ $r->qr_code }}
                </div>
                <div style="font-size:0.82rem;color:#888;margin-top:4px;">
                    Ditemukan — {{ $r->items->count() }} buku
                </div>
            </div>
            <span class="badge badge-{{ $r->status }}">{{ $r->statusLabel() }}</span>
        </div>

        {{-- Info Peminjaman --}}
        <div class="result-info-grid">
            <div class="result-info-item">
                <div class="lbl">Peminjam</div>
                <div class="val">{{ $r->user->name }}</div>
            </div>
            <div class="result-info-item">
                <div class="lbl">Email</div>
                <div class="val">{{ $r->user->email }}</div>
            </div>
            <div class="result-info-item">
                <div class="lbl">Tanggal Pengambilan</div>
                <div class="val">{{ $r->pickup_date->format('d M Y') }}</div>
            </div>
            <div class="result-info-item">
                <div class="lbl">Tanggal Pengembalian</div>
                <div class="val">{{ $r->return_date->format('d M Y') }}</div>
            </div>
            @if($r->expires_at && $r->isPending())
            <div class="result-info-item">
                <div class="lbl">Batas Verifikasi</div>
                <div class="val" style="color:#E65100;">{{ $r->expires_at->format('d M Y H:i') }}</div>
            </div>
            @endif
        </div>

        {{-- Daftar buku --}}
        <div style="font-weight:600;font-size:0.86rem;color:var(--text-dark);margin-bottom:8px;">
            <i class="fas fa-books"></i> Buku yang dipinjam:
        </div>
        @foreach($r->items as $ci => $item)
            <div class="book-row">
                <div class="b-thumb" style="background:{{ $colors[$ci % 5] }};">
                    @if($item->book->cover_image)
                        <img src="{{ asset('img/covers/'.$item->book->cover_image) }}" alt="">
                    @else
                        <i class="fas fa-book"></i>
                    @endif
                </div>
                <div class="b-info">
                    <div class="b-title">{{ $item->book->title }}</div>
                    <div class="b-author">{{ $item->book->author }}</div>
                </div>
            </div>
        @endforeach

        {{-- Tombol aksi --}}
        @if(session('kode_action') === 'confirm' && $r->isPending())
            <div class="action-row">
                <form action="{{ route('admin.verify-qr.confirm') }}" method="POST" data-confirm="Konfirmasi peminjaman dan serahkan buku ke user?">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $r->id }}">
                    <button type="submit" class="btn-confirm"
                            >
                        <i class="fas fa-check-circle"></i> Konfirmasi & Serahkan Buku
                    </button>
                </form>
            </div>

        @elseif($r->isActive())
            <div class="action-row">
                <form action="{{ route('admin.verify-qr.return') }}" method="POST" data-confirm="Konfirmasi buku telah dikembalikan?">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $r->id }}">
                    <button type="submit" class="btn-return"
                            >
                        <i class="fas fa-undo"></i> Konfirmasi Pengembalian
                    </button>
                </form>
            </div>

        @elseif($r->isCancelled())
            <div style="background:#FFEBEE;border-radius:10px;padding:14px;margin-top:16px;font-size:0.86rem;color:#C62828;">
                <i class="fas fa-times-circle"></i> Peminjaman ini telah dibatalkan (kadaluarsa atau dibatalkan sistem).
            </div>

        @elseif($r->isReturned())
            <div style="background:#EDE7F6;border-radius:10px;padding:14px;margin-top:16px;font-size:0.86rem;color:#4527A0;">
                <i class="fas fa-check-circle"></i> Buku sudah dikembalikan pada {{ $r->returned_at?->format('d M Y H:i') }}.
            </div>
        @endif
    </div>
@endif

<script>
// Auto-uppercase input
const inp = document.querySelector('.code-input');
if (inp) {
    inp.addEventListener('input', function() {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
    inp.focus();
}
</script>
@endsection