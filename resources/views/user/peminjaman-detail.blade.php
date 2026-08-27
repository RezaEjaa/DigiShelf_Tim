@extends('layouts.app-navbar')
@section('title', 'Detail Peminjaman - Digishelf')

@section('content')
<style>
    :root { --wood-dark:#5D4037; --wood-medium:#8D6E63; --wood-light:#D7CCC8; --cream:#FFF8E1; --text-dark:#3E2723; }

    /* ── Back button ── */
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        color: var(--wood-medium); font-size: 0.88rem; font-weight: 600;
        text-decoration: none; margin-bottom: 20px; padding: 8px 0;
        transition: color 0.2s;
    }
    .btn-back:hover { color: var(--wood-dark); }

    /* ── Status banner ── */
    .status-banner {
        border-radius: 12px; padding: 13px 18px;
        display: flex; align-items: center; gap: 11px;
        margin-bottom: 22px; font-weight: 600; font-size: 0.93rem;
    }
    .status-banner.pending   { background:#FFF3E0; color:#E65100; }
    .status-banner.active    { background:#E8F5E9; color:#2E7D32; }
    .status-banner.returned  { background:#EDE7F6; color:#4527A0; }
    .status-banner.cancelled { background:#FFEBEE; color:#C62828; }
    .status-banner i { font-size: 1.15rem; }

    /* ── Layout grid ── */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 22px;
        align-items: start;
    }

    /* ── Card ── */
    .card {
        background: white; border-radius: 15px; padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08); margin-bottom: 18px;
    }
    .card-title {
        font-family: 'Crimson Pro', serif;
        font-size: 1.1rem; font-weight: 700; color: var(--text-dark);
        margin-bottom: 14px; padding-bottom: 10px;
        border-bottom: 2px solid var(--cream);
        display: flex; align-items: center; gap: 8px;
    }
    .card-title i { color: var(--wood-medium); }

    /* ── Info rows ── */
    .info-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 9px 0; border-bottom: 1px solid #F5F5F5;
        font-size: 0.86rem; gap: 14px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .lbl { color: #888; flex-shrink: 0; }
    .info-row .val { font-weight: 600; color: var(--text-dark); text-align: right; word-break: break-word; }

    /* ── Book rows ── */
    .book-row {
        display: flex; align-items: center; gap: 13px;
        padding: 11px 0; border-bottom: 1px solid #F5F5F5;
    }
    .book-row:last-child { border-bottom: none; }
    .book-thumb {
        width: 44px; height: 66px; border-radius: 5px;
        overflow: hidden; flex-shrink: 0;
        box-shadow: 2px 2px 7px rgba(0,0,0,0.2);
        display: flex; align-items: center; justify-content: center;
    }
    .book-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .book-thumb i   { font-size: 18px; color: rgba(255,255,255,0.6); }
    .book-meta { flex: 1; min-width: 0; }
    .book-meta-title  { font-size: 0.87rem; font-weight: 600; color: var(--text-dark); margin-bottom: 3px; }
    .book-meta-author { font-size: 0.76rem; color: #888; }
    .book-meta-pub    { font-size: 0.71rem; color: #aaa; margin-top: 2px; }

    /* ── QR Box ── */
    .qr-box { text-align: center; }

    .qr-display {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 16px; background: white;
        border: 3px solid var(--wood-dark); border-radius: 14px;
        margin-bottom: 12px;
        box-shadow: 0 5px 18px rgba(93,64,55,0.15);
    }

    /* QR container: qrcodejs renders BOTH canvas + img
       — sembunyikan img, tampilkan canvas saja agar tidak double */
    #qrContainer { line-height: 0; }
    #qrContainer canvas {
        display: block !important;
        width: 190px !important;
        height: 190px !important;
    }
    #qrContainer img {
        display: none !important;  /* hide duplicate img */
    }

    /* Download button */
    .btn-download-qr {
        display: inline-flex; align-items: center; gap: 7px;
        width: 100%; justify-content: center;
        margin-top: 12px; padding: 10px 16px;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white; border: none; border-radius: 9px;
        font-size: 0.86rem; font-weight: 600; cursor: pointer;
        text-decoration: none; transition: all 0.2s;
    }
    .btn-download-qr:hover { opacity: 0.9; transform: translateY(-1px); }

    .qr-code-str {
        font-family: 'Courier New', monospace;
        font-size: 0.95rem; font-weight: 700;
        color: var(--wood-dark); letter-spacing: 2px; margin-bottom: 10px;
    }

    .qr-instruction {
        background: var(--cream); border-radius: 10px; padding: 12px 14px;
        font-size: 0.8rem; color: #5D4037; line-height: 1.7;
        text-align: left; margin-bottom: 12px;
    }
    .qr-instruction i { color: var(--wood-medium); margin-right: 5px; }

    .countdown-box {
        background: #FFF3E0; border: 1px solid #FFE082; border-radius: 10px;
        padding: 12px 14px; text-align: center;
        font-size: 0.82rem; color: #E65100; font-weight: 600;
    }
    .countdown-timer {
        font-size: 1.35rem; font-weight: 700;
        font-family: monospace; letter-spacing: 2px; margin-top: 4px;
    }

    .cancelled-box {
        background: #FFEBEE; border-radius: 10px; padding: 20px;
        text-align: center; color: #C62828; font-size: 0.85rem;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .detail-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .card { padding: 16px 14px; }
        .status-banner { font-size: 0.85rem; padding: 11px 14px; }
        .info-row { font-size: 0.82rem; }
        #qrContainer canvas {
            width: 160px !important;
            height: 160px !important;
        }
        #qrContainer img { display: none !important; }
        .qr-code-str { font-size: 0.82rem; letter-spacing: 1px; }
    }
</style>

@php
$req    = $borrowingRequest;
$colors = [
    'linear-gradient(135deg,#A1887F,#8D6E63)',
    'linear-gradient(135deg,#7986CB,#5C6BC0)',
    'linear-gradient(135deg,#81C784,#66BB6A)',
    'linear-gradient(135deg,#FFB74D,#FFA726)',
    'linear-gradient(135deg,#E57373,#EF5350)',
];
@endphp

@if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;border-radius:9px;padding:11px 14px;margin-bottom:14px;font-size:0.86rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Back --}}
<a href="{{ route('user.borrowings') }}" class="btn-back">
    <i class="fas fa-arrow-left"></i> Kembali ke Peminjaman
</a>

{{-- Status --}}
<div class="status-banner {{ $req->status }}">
    @if($req->isPending())    <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi Petugas
    @elseif($req->isActive()) <i class="fas fa-check-circle"></i> Sedang Dipinjam
    @elseif($req->isReturned()) <i class="fas fa-undo"></i> Sudah Dikembalikan
    @else <i class="fas fa-times-circle"></i> Dibatalkan
    @endif
</div>

<div class="detail-grid">

    {{-- KIRI --}}
    <div>
        <div class="card">
            <div class="card-title"><i class="fas fa-info-circle"></i> Informasi Peminjaman</div>

            <div class="info-row">
                <span class="lbl">Kode QR</span>
                <span class="val" style="font-family:'Courier New',monospace;color:var(--wood-dark);">{{ $req->qr_code }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">Peminjam</span>
                <span class="val">{{ $req->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">Tgl Pengambilan</span>
                <span class="val">{{ $req->pickup_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">Tgl Pengembalian</span>
                <span class="val">{{ $req->return_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">Status</span>
                <span class="val">{{ $req->statusLabel() }}</span>
            </div>
            @if($req->verified_at)
            <div class="info-row">
                <span class="lbl">Diverifikasi</span>
                <span class="val">{{ $req->verified_at->format('d M Y H:i') }}</span>
            </div>
            @endif
            @if($req->returned_at)
            <div class="info-row">
                <span class="lbl">Dikembalikan</span>
                <span class="val">{{ $req->returned_at->format('d M Y H:i') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="lbl">Jumlah Buku</span>
                <span class="val">{{ $req->items->count() }} buku</span>
            </div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fas fa-book-open"></i> Daftar Buku Dipinjam</div>
            @foreach($req->items as $ci => $item)
                @php $book = $item->book; @endphp
                <div class="book-row">
                    <div class="book-thumb" style="background:{{ $colors[$ci % 5] }};">
                        @if($book->cover_image)
                            <img src="{{ asset('img/covers/'.$book->cover_image) }}" alt="{{ $book->title }}">
                        @else
                            <i class="fas fa-book"></i>
                        @endif
                    </div>
                    <div class="book-meta">
                        <div class="book-meta-title">{{ $book->title }}</div>
                        <div class="book-meta-author">{{ $book->author }}</div>
                        @if($book->publisher)
                            <div class="book-meta-pub">{{ $book->publisher }}@if($book->publication_year) ({{ $book->publication_year }})@endif</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- KANAN: QR --}}
    <div>
        <div class="card">
            <div class="card-title"><i class="fas fa-qrcode"></i> QR Code</div>

            @if($req->isCancelled())
                <div class="cancelled-box">
                    <i class="fas fa-times-circle" style="font-size:2rem;margin-bottom:10px;display:block;"></i>
                    <p>Peminjaman ini telah dibatalkan.<br>QR Code sudah tidak berlaku.</p>
                </div>

            @else
                <div class="qr-box">
                    {{-- qrcodejs akan INSERT <img> atau <canvas> ke dalam div ini --}}
                    <div class="qr-display">
                        <div id="qrContainer"></div>
                    </div>
                    <div class="qr-code-str">{{ $req->qr_code }}</div>
                    <button class="btn-download-qr" onclick="downloadQR()">
                        <i class="fas fa-download"></i> Download QR Code
                    </button>
                </div>

                @if(!$req->isReturned())
                    <div class="qr-instruction">
                        <p><i class="fas fa-map-marker-alt"></i>
                            <strong>Ambil buku di perpustakaan</strong> dan tunjukkan QR Code ini kepada petugas.</p>
                        <p style="margin-top:7px;">
                            <i class="fas fa-info-circle"></i>
                            Petugas akan memindai QR Code dan menyerahkan buku kepada Anda.</p>
                    </div>
                @else
                    <div style="background:#EDE7F6;border-radius:9px;padding:11px;font-size:0.81rem;color:#4527A0;margin-top:10px;text-align:center;">
                        <i class="fas fa-check-circle"></i> Buku sudah dikembalikan. Terima kasih!
                    </div>
                @endif

                @if($req->isPending() && $req->expires_at)
                    <div class="countdown-box" style="margin-top:12px;">
                        <div><i class="fas fa-exclamation-triangle"></i> Auto-batal jika tidak diambil dalam:</div>
                        <div class="countdown-timer" id="countdown">--:--:--</div>
                        <div style="font-size:0.73rem;margin-top:4px;opacity:0.8;">
                            Batas: {{ $req->expires_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @elseif($req->isActive())
                    <div style="background:#E8F5E9;border-radius:9px;padding:11px;font-size:0.81rem;color:#2E7D32;text-align:center;margin-top:12px;">
                        <i class="fas fa-check-circle"></i> Buku sudah diverifikasi petugas.<br>
                        <strong>Kembalikan sebelum {{ $req->return_date->format('d M Y') }}</strong>
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>

@if(!$req->isCancelled())
<script>
// ── QR Data: unik per peminjaman (pakai qr_code yg sudah dibuat unique di DB) ──
// Isi QR: URL halaman scan yang menampilkan kode DIGI-XXXXXXXX.
const QR_TEXT = @json(route('borrow.scan', $req->qr_code));
const QR_SIZE = window.innerWidth <= 600 ? 160 : 190;

function buildQR() {
    const wrap = document.getElementById('qrContainer');
    if (!wrap || typeof QRCode === 'undefined') return;

    wrap.innerHTML = ''; // bersihkan container

    // qrcodejs membuat CANVAS + IMG di dalam wrap
    // Kita tampilkan canvas saja, img di-hide via CSS
    new QRCode(wrap, {
        text: QR_TEXT,
        width: QR_SIZE,
        height: QR_SIZE,
        colorDark: '#3E2723',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });

    // Paksa canvas visible, sembunyikan img duplikat
    setTimeout(() => {
        const cv  = wrap.querySelector('canvas');
        const img = wrap.querySelector('img');
        if (cv)  cv.style.cssText  = `display:block!important;width:${QR_SIZE}px!important;height:${QR_SIZE}px!important;`;
        if (img) img.style.cssText = 'display:none!important;';
    }, 250);
}

// Download QR sebagai PNG HD (4x resolution)
function downloadQR() {
    const wrap = document.getElementById('qrContainer');
    const cv   = wrap ? wrap.querySelector('canvas') : null;

    if (cv) {
        // Render ulang ke canvas HD (4x)
        const HD = QR_SIZE * 4;
        const hdCanvas = document.createElement('canvas');
        hdCanvas.width  = HD;
        hdCanvas.height = HD;
        const ctx = hdCanvas.getContext('2d');

        // Background putih
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, HD, HD);
        // Scale up canvas QR yang ada
        ctx.imageSmoothingEnabled = false;
        ctx.drawImage(cv, 0, 0, HD, HD);

        // Download
        const link = document.createElement('a');
        link.download = 'QR-{{ $req->qr_code }}.png';
        link.href = hdCanvas.toDataURL('image/png', 1.0);
        link.click();
    } else {
        // Fallback: download via Google Charts (HD 600px)
        const enc  = encodeURIComponent(QR_TEXT);
        const link = document.createElement('a');
        link.href     = `https://chart.googleapis.com/chart?chs=600x600&cht=qr&chl=${enc}&choe=UTF-8`;
        link.download = 'QR-{{ $req->qr_code }}.png';
        link.target   = '_blank';
        link.click();
    }
}

// Muat library lalu render
if (typeof QRCode !== 'undefined') {
    buildQR();
} else {
    const scr = document.createElement('script');
    scr.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
    scr.onload = buildQR;
    scr.onerror = function() {
        // Fallback Google Charts
        const wrap = document.getElementById('qrContainer');
        if (wrap) {
            const enc = encodeURIComponent(QR_TEXT);
            wrap.innerHTML = `<img src="https://chart.googleapis.com/chart?chs=${QR_SIZE}x${QR_SIZE}&cht=qr&chl=${enc}&choe=UTF-8"
                style="display:block;width:${QR_SIZE}px;height:${QR_SIZE}px;" alt="QR">`;
        }
    };
    document.body.appendChild(scr);
}

@if($req->isPending() && $req->expires_at)
// Countdown
(function() {
    const exp = new Date("{{ $req->expires_at->toIso8601String() }}");
    function tick() {
        const el = document.getElementById('countdown');
        if (!el) return;
        const d = exp - Date.now();
        if (d <= 0) { el.textContent = 'KADALUARSA'; el.style.color='#C62828'; return; }
        const h = String(Math.floor(d/3600000)).padStart(2,'0');
        const m = String(Math.floor((d%3600000)/60000)).padStart(2,'0');
        const s = String(Math.floor((d%60000)/1000)).padStart(2,'0');
        el.textContent = `${h}:${m}:${s}`;
        setTimeout(tick, 1000);
    }
    tick();
})();
@endif
</script>
@endif

@endsection
