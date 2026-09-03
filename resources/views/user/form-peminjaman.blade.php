@extends('layouts.app-navbar')
@section('title', 'Form Peminjaman - Digishelf')

@section('content')
<style>
    :root { --wood-dark:#5D4037; --wood-medium:#8D6E63; --wood-light:#D7CCC8; --cream:#FFF8E1; --text-dark:#3E2723; }

    /* ── Page layout ── */
    .form-pinjam-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        align-items: start;
    }

    /* ── Kartu umum ── */
    .fpcard {
        background: white; border-radius: 16px; padding: 24px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08); margin-bottom: 20px;
        min-width: 0; overflow: hidden;
    }
    .fpcard-title {
        font-family: 'Crimson Pro', serif;
        font-size: 1.25rem; font-weight: 700; color: var(--text-dark);
        margin-bottom: 16px; padding-bottom: 10px;
        border-bottom: 2px solid var(--cream);
        display: flex; align-items: center; gap: 9px;
    }
    .fpcard-title i { color: var(--wood-medium); }

    /* ── Bookshelf (identik books-grid) ── */
    .bookshelf-wrapper {
        background: linear-gradient(180deg, #B8956A 0%, #9A7B5A 100%);
        border-radius: 20px; padding: 30px 28px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: relative;
    }
    .bookshelf-wrapper::before {
        content: ''; position: absolute; inset: 0;
        background: repeating-linear-gradient(90deg,transparent,transparent 2px,rgba(0,0,0,0.03) 2px,rgba(0,0,0,0.03) 4px);
        border-radius: 20px; pointer-events: none;
    }

    .shelf-row { position: relative; margin-bottom: 50px; }
    .shelf-row:last-child { margin-bottom: 0; }
    .shelf-board {
        position: absolute; bottom: -25px; left: -20px; right: -20px;
        height: 15px;
        background: linear-gradient(180deg, #6D4C41 0%, #5D4037 100%);
        border-radius: 3px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1), inset 0 -2px 5px rgba(0,0,0,0.3);
    }
    .shelf-board::before {
        content: ''; position: absolute; bottom: -8px; left: 0; right: 0;
        height: 8px; background: linear-gradient(180deg,transparent,rgba(0,0,0,0.15));
        border-radius: 0 0 3px 3px;
    }

    .books-grid { display: grid; gap: 20px; position: relative; z-index: 1; min-height: 180px; }
    .books-grid-5 { grid-template-columns: repeat(5, 1fr); }
    .books-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .books-grid-2 { grid-template-columns: repeat(2, 1fr); }

    .shelf-laptop { display: block; }
    .shelf-tablet { display: none; }
    .shelf-mobile { display: none; }

    /* Book item */
    .fp-book-item {
        background: white; border-radius: 8px;
        overflow: visible; box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        transition: all 0.3s; cursor: pointer;
        aspect-ratio: 2/3; position: relative;
    }
    .fp-book-item:hover { transform: translateY(-10px) rotate(2deg); box-shadow: 0 12px 25px rgba(0,0,0,0.35); }
    .fp-book-item.selected { outline: 4px solid var(--wood-dark); box-shadow: 0 0 0 4px var(--wood-dark); }
    .fp-book-item.selected:hover { transform: translateY(-10px) rotate(2deg); }
    .fp-book-item.unavailable { opacity: 0.4; cursor: not-allowed; }
    .fp-book-item.unavailable:hover { transform: none; box-shadow: 0 6px 15px rgba(0,0,0,0.25); }

    .fp-cover {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; border-radius: 8px;
    }
    .fp-cover img { width: 100%; height: 100%; object-fit: cover; }
    .fp-cover i { font-size: 50px; color: rgba(255,255,255,0.4); }

    /* Check overlay */
    .fp-check {
        position: absolute; top: 6px; right: 6px;
        width: 28px; height: 28px;
        background: var(--wood-dark); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.2s; z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        border: 2px solid white;
    }
    .fp-check i { font-size: 12px; color: white; font-weight: 700; }
    .fp-book-item.selected .fp-check { opacity: 1; }

    /* Search in shelf */
    .shelf-search {
        text-align: center; margin-bottom: 16px; position: relative; z-index: 2;
    }
    .shelf-search input {
        padding: 9px 40px 9px 18px; border: none; border-radius: 30px;
        font-size: 0.88rem; font-family: 'Poppins', sans-serif;
        background: rgba(255,255,255,0.9);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15); width: 100%; max-width: 400px;
    }
    .shelf-search input:focus { outline: none; background: white; }

    /* ── Keranjang ── */
    .cart-counter {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--cream); border-radius: 8px; padding: 8px 14px;
        margin-bottom: 14px; font-size: 0.84rem;
    }
    .cart-counter strong { color: var(--wood-dark); }

    .cart-empty { text-align: center; padding: 24px 16px; color: #bbb; }
    .cart-empty i { font-size: 34px; margin-bottom: 8px; display: block; }
    .cart-empty p { font-size: 0.82rem; }

    .cart-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 0; border-bottom: 1px solid #F5F5F5;
        min-width: 0; overflow: hidden; /* prevent cart item from stretching */
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-thumb {
        width: 36px; height: 54px; border-radius: 4px; overflow: hidden;
        flex-shrink: 0; box-shadow: 1px 1px 5px rgba(0,0,0,0.18);
        display: flex; align-items: center; justify-content: center;
    }
    .cart-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .cart-thumb i { font-size: 16px; color: rgba(255,255,255,0.6); }
    .cart-info {
        flex: 1;
        min-width: 0;      /* CRITICAL: biarkan flex item bisa menyusut */
        overflow: hidden;
        width: 0;          /* Paksa tidak melebar keluar container */
    }
    .cart-info-title {
        font-size: 0.8rem; font-weight: 600; color: var(--text-dark);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        display: block; max-width: 100%;
    }
    .cart-info-author {
        font-size: 0.71rem; color: #888;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        display: block; max-width: 100%;
    }
    .cart-remove {
        background: #FFEBEE; color: #C62828; border: none;
        width: 26px; height: 26px; border-radius: 50%;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        font-size: 11px; flex-shrink: 0; transition: background 0.2s;
    }
    .cart-remove:hover { background: #C62828; color: white; }

    /* ── Form tanggal ── */
    .fp-form-group { margin-bottom: 14px; }
    .fp-form-group label {
        display: block; font-size: 0.84rem; font-weight: 600;
        color: var(--text-dark); margin-bottom: 5px;
    }
    .fp-form-group label.req::after { content: ' *'; color: #E53935; }
    .fp-form-control {
        width: 100%; padding: 10px 13px; border: 2px solid #E0E0E0;
        border-radius: 9px; font-size: 0.88rem;
        font-family: 'Poppins', sans-serif; transition: border-color 0.2s; background: white;
    }
    .fp-form-control:focus { outline: none; border-color: var(--wood-medium); }

    /* Info box */
    .info-box {
        background: #FFF8E1; border: 1px solid #FFE082; border-radius: 10px;
        padding: 11px 14px; font-size: 0.8rem; color: #795548;
        margin-bottom: 14px; line-height: 1.6;
    }
    .info-box i { color: #F9A825; margin-right: 5px; }

    /* Submit */
    .btn-submit {
        width: 100%; padding: 12px;
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white; border: none; border-radius: 10px;
        font-size: 0.92rem; font-weight: 600; cursor: pointer;
        transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(93,64,55,0.3); }
    .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

    /* Error */
    .error-box {
        background: #FFEBEE; color: #C62828; border-radius: 9px;
        padding: 11px 14px; font-size: 0.83rem; margin-bottom: 14px;
    }

    /* Responsive — identik dengan books-grid breakpoints */
    @media (max-width: 1024px) {
        .form-pinjam-layout { grid-template-columns: 1fr 280px; }
    }

    /* Tablet: shelf-tablet aktif (3 kolom) */
    @media (max-width: 900px) {
        .form-pinjam-layout { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .shelf-laptop { display: none; }
        .shelf-tablet { display: block; }
        .shelf-mobile { display: none; }
        .bookshelf-wrapper { padding: 25px 15px; }
        .shelf-row { margin-bottom: 40px; }
        .books-grid { gap: 14px; }
    }

    /* HP kecil: shelf-mobile aktif (2 kolom) — sama dengan books-grid */
    @media (max-width: 480px) {
        .shelf-laptop { display: none; }
        .shelf-tablet { display: none; }
        .shelf-mobile { display: block; }
        .bookshelf-wrapper { padding: 15px 10px; }
        .shelf-row { margin-bottom: 35px; }
        .books-grid { gap: 10px; }
        /* Hint & search lebih compact */
        .shelf-search input { font-size: 0.82rem; }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <i class="fas fa-exclamation-circle"></i>
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
@endif

@if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;border-radius:9px;padding:11px 14px;margin-bottom:14px;font-size:0.86rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<form action="{{ route('borrow.store') }}" method="POST" id="borrowForm"
      onsubmit="sessionStorage.removeItem('fp_cart');sessionStorage.removeItem('fp_pickup');sessionStorage.removeItem('fp_return');">
@csrf

<div class="form-pinjam-layout">

    {{-- ── KIRI: Bookshelf katalog buku ── --}}
    <div>
        <div class="fpcard">
            <div class="fpcard-title"><i class="fas fa-book-open"></i> Pilih Buku</div>

            {{-- Hint --}}
            <div style="text-align:center;margin-bottom:14px;">
                <span style="display:inline-flex;align-items:center;gap:7px;background:var(--cream);border:1px solid #FFE082;padding:6px 16px;border-radius:20px;font-size:0.8rem;font-weight:500;color:var(--wood-dark);">
                    <i class="fas fa-hand-pointer"></i> Klik cover buku untuk menambahkan ke keranjang
                </span>
            </div>

            {{-- Search --}}
            <div class="shelf-search">
                <input type="text" id="searchBook" placeholder="Cari judul atau penulis…">
            </div>

            @php
            $colors = [
                'linear-gradient(135deg,#A1887F,#8D6E63)',
                'linear-gradient(135deg,#7986CB,#5C6BC0)',
                'linear-gradient(135deg,#81C784,#66BB6A)',
                'linear-gradient(135deg,#FFB74D,#FFA726)',
                'linear-gradient(135deg,#E57373,#EF5350)',
                'linear-gradient(135deg,#9575CD,#7E57C2)',
            ];
            $booksArr = $books->items(); // items() untuk LengthAwarePaginator
            $cnt = count($booksArr);
            $books5 = $cnt > 0 ? array_chunk(array_pad(array_values($booksArr), (int)(ceil($cnt/5)*5), null), 5) : [[]];
            $books3 = $cnt > 0 ? array_chunk(array_pad(array_values($booksArr), (int)(ceil($cnt/3)*3), null), 3) : [[]];
            $books2 = $cnt > 0 ? array_chunk(array_pad(array_values($booksArr), (int)(ceil($cnt/2)*2), null), 2) : [[]];
            @endphp

            <div class="bookshelf-wrapper">

                @if(count($booksArr) > 0)

                    {{-- LAPTOP: 5 per baris --}}
                    <div class="shelf-laptop">
                        @foreach($books5 as $row)
                            <div class="shelf-row" data-shelf>
                                <div class="books-grid books-grid-5">
                                    @foreach($row as $idx => $book)
                                        @if($book)
                                            @php
                                                $unavail = in_array($book->id, $borrowedBookIds);
                                                $presel  = (string)$book->id === (string)$preselectedId;
                                            @endphp
                                            <div class="fp-book-item {{ $unavail ? 'unavailable' : '' }} {{ $presel ? 'selected' : '' }}"
                                                 data-id="{{ $book->id }}"
                                                 data-title="{{ addslashes($book->title) }}"
                                                 data-author="{{ addslashes($book->author) }}"
                                                 data-cover="{{ $book->cover_image ? asset('img/covers/'.$book->cover_image) : '' }}"
                                                 data-color="{{ $colors[$idx % 6] }}"
                                                 data-unavail="{{ $unavail ? '1' : '0' }}"
                                                 data-search="{{ strtolower($book->title . ' ' . $book->author) }}"
                                                 onclick="toggleBook(this)">
                                                <div class="fp-cover" style="background:{{ $colors[$idx % 6] }};">
                                                    @if($book->cover_image)
                                                        <img src="{{ asset('img/covers/'.$book->cover_image) }}" alt="{{ $book->title }}">
                                                    @else
                                                        <i class="fas fa-book"></i>
                                                    @endif
                                                </div>
                                                <div class="fp-check"><i class="fas fa-check"></i></div>
                                            </div>
                                        @else
                                            <div style="visibility:hidden;aspect-ratio:2/3;"></div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="shelf-board"></div>
                            </div>
                        @endforeach
                    </div>

                    {{-- TABLET: 3 per baris --}}
                    <div class="shelf-tablet">
                        @foreach($books3 as $row)
                            <div class="shelf-row" data-shelf>
                                <div class="books-grid books-grid-3">
                                    @foreach($row as $idx => $book)
                                        @if($book)
                                            @php $unavail = in_array($book->id, $borrowedBookIds); $presel = (string)$book->id === (string)$preselectedId; @endphp
                                            <div class="fp-book-item {{ $unavail ? 'unavailable' : '' }} {{ $presel ? 'selected' : '' }}"
                                                 data-id="{{ $book->id }}"
                                                 data-title="{{ addslashes($book->title) }}"
                                                 data-author="{{ addslashes($book->author) }}"
                                                 data-cover="{{ $book->cover_image ? asset('img/covers/'.$book->cover_image) : '' }}"
                                                 data-color="{{ $colors[$idx % 6] }}"
                                                 data-unavail="{{ $unavail ? '1' : '0' }}"
                                                 data-search="{{ strtolower($book->title . ' ' . $book->author) }}"
                                                 onclick="toggleBook(this)">
                                                <div class="fp-cover" style="background:{{ $colors[$idx % 6] }};">
                                                    @if($book->cover_image) <img src="{{ asset('img/covers/'.$book->cover_image) }}" alt=""> @else <i class="fas fa-book"></i> @endif
                                                </div>
                                                <div class="fp-check"><i class="fas fa-check"></i></div>
                                            </div>
                                        @else
                                            <div style="visibility:hidden;aspect-ratio:2/3;"></div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="shelf-board"></div>
                            </div>
                        @endforeach
                    </div>

                    {{-- MOBILE: 2 per baris --}}
                    <div class="shelf-mobile">
                        @foreach($books2 as $row)
                            <div class="shelf-row" data-shelf>
                                <div class="books-grid books-grid-2">
                                    @foreach($row as $idx => $book)
                                        @if($book)
                                            @php $unavail = in_array($book->id, $borrowedBookIds); $presel = (string)$book->id === (string)$preselectedId; @endphp
                                            <div class="fp-book-item {{ $unavail ? 'unavailable' : '' }} {{ $presel ? 'selected' : '' }}"
                                                 data-id="{{ $book->id }}"
                                                 data-title="{{ addslashes($book->title) }}"
                                                 data-author="{{ addslashes($book->author) }}"
                                                 data-cover="{{ $book->cover_image ? asset('img/covers/'.$book->cover_image) : '' }}"
                                                 data-color="{{ $colors[$idx % 6] }}"
                                                 data-unavail="{{ $unavail ? '1' : '0' }}"
                                                 data-search="{{ strtolower($book->title . ' ' . $book->author) }}"
                                                 onclick="toggleBook(this)">
                                                <div class="fp-cover" style="background:{{ $colors[$idx % 6] }};">
                                                    @if($book->cover_image) <img src="{{ asset('img/covers/'.$book->cover_image) }}" alt=""> @else <i class="fas fa-book"></i> @endif
                                                </div>
                                                <div class="fp-check"><i class="fas fa-check"></i></div>
                                            </div>
                                        @else
                                            <div style="visibility:hidden;aspect-ratio:2/3;"></div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="shelf-board"></div>
                            </div>
                        @endforeach
                    </div>

                @else
                    <div style="text-align:center;padding:50px;color:rgba(255,255,255,0.7);">
                        <i class="fas fa-book" style="font-size:50px;opacity:0.5;margin-bottom:12px;display:block;"></i>
                        <p>Belum ada buku tersedia</p>
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($books->hasPages())
            <div style="display:flex;justify-content:center;gap:6px;flex-wrap:wrap;margin-top:20px;">
                @if($books->onFirstPage())
                    <span style="min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#F1F5F9;color:#aaa;font-size:0.84rem;">&lt;</span>
                @else
                    <a href="{{ $books->previousPageUrl() }}" data-page-link style="min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#F1F5F9;color:#64748B;text-decoration:none;font-size:0.84rem;">&lt;</a>
                @endif

                @foreach(range(1, $books->lastPage()) as $page)
                    @if($page == $books->currentPage())
                        <span style="min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:var(--wood-dark);color:white;font-size:0.84rem;">{{ $page }}</span>
                    @else
                        <a href="{{ $books->url($page) }}" data-page-link style="min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#F1F5F9;color:#64748B;text-decoration:none;font-size:0.84rem;">{{ $page }}</a>
                    @endif
                @endforeach

                @if($books->hasMorePages())
                    <a href="{{ $books->nextPageUrl() }}" data-page-link style="min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#F1F5F9;color:#64748B;text-decoration:none;font-size:0.84rem;">&gt;</a>
                @else
                    <span style="min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#F1F5F9;color:#aaa;font-size:0.84rem;">&gt;</span>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ── KANAN: Keranjang + Jadwal ── --}}
    <div>
        {{-- Keranjang --}}
        <div class="fpcard">
            <div class="fpcard-title"><i class="fas fa-shopping-basket"></i> Keranjang Pinjam</div>

            <div class="cart-counter">
                <span>Buku dipilih</span>
                <strong id="cartCount">0 / {{ $remaining }}</strong>
            </div>

            <div id="cartEmpty" class="cart-empty">
                <i class="fas fa-inbox"></i>
                <p>Klik cover buku di rak<br>untuk menambahkan.</p>
            </div>

            <div id="cartItems"></div>
            <div id="hiddenInputs"></div>
        </div>

        {{-- Jadwal --}}
        <div class="fpcard">
            <div class="fpcard-title"><i class="fas fa-calendar-alt"></i> Jadwal Peminjaman</div>

            <div class="fp-form-group">
                <label for="pickup_date" class="req">Tanggal Pengambilan</label>
                <input type="date" class="fp-form-control" id="pickup_date" name="pickup_date"
                       value="{{ old('pickup_date') }}" min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="fp-form-group">
                <label for="return_date" class="req">Tanggal Pengembalian</label>
                <input type="date" class="fp-form-control" id="return_date" name="return_date"
                       value="{{ old('return_date') }}" required>
            </div>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                Setelah submit Anda mendapat <strong>QR Code</strong>.
                Tunjukkan ke petugas saat mengambil buku.<br><br>
                <i class="fas fa-calendar-check"></i>
                Tanggal pengembalian hanya bisa dipilih maksimal <strong>4 hari</strong> setelah tanggal pengambilan.<br><br>
                <i class="fas fa-clock"></i>
                <strong>Auto-batal</strong> jika tidak diverifikasi dalam <strong>24 jam</strong>.
            </div>

            <button type="submit" class="btn-submit" id="submitBtn" disabled>
                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
            </button>
        </div>
    </div>

</div>
</form>

{{-- Data buku untuk JS --}}
<script>
const MAX_BORROW = {{ $remaining }};
const PRESELECTED_ID = '{{ $preselectedId }}';
const CART_KEY = 'fp_cart'; // sessionStorage key
let selected = new Map(); // id → {title, author, cover, color}

// ── Simpan cart ke sessionStorage ────────────────────────────
function saveCart() {
    const obj = {};
    selected.forEach((book, id) => { obj[id] = book; });
    sessionStorage.setItem(CART_KEY, JSON.stringify(obj));
}

// ── Restore cart dari sessionStorage ─────────────────────────
function restoreCart() {
    const raw = sessionStorage.getItem(CART_KEY);
    if (!raw) return;
    try {
        const obj = JSON.parse(raw);
        Object.entries(obj).forEach(([id, book]) => {
            selected.set(id, book);
            // tandai tile jika ada di halaman ini
            document.querySelectorAll(`.fp-book-item[data-id="${id}"]`)
                    .forEach(t => t.classList.add('selected'));
        });
    } catch(e) { sessionStorage.removeItem(CART_KEY); }
}

// ── Intercept link pagination — simpan cart sebelum navigasi ─
function initPaginationSave() {
    document.querySelectorAll('a[data-page-link]').forEach(link => {
        link.addEventListener('click', (e) => {
            // Simpan tanggal ke sessionStorage juga
            const pickup = document.getElementById('pickup_date').value;
            const ret    = document.getElementById('return_date').value;
            if (pickup) sessionStorage.setItem('fp_pickup', pickup);
            if (ret)    sessionStorage.setItem('fp_return', ret);
            saveCart(); // simpan keranjang
            // biarkan navigasi berjalan normal
        });
    });
}

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Restore tanggal
    const pickup = document.getElementById('pickup_date');
    const ret    = document.getElementById('return_date');
    if (!pickup.value) {
        const saved = sessionStorage.getItem('fp_pickup');
        pickup.value = saved || new Date().toISOString().split('T')[0];
    }
    if (!ret.value && sessionStorage.getItem('fp_return')) {
        ret.value = sessionStorage.getItem('fp_return');
    }
    updateReturnMin();
    pickup.addEventListener('change', () => { updateReturnMin(); saveCart(); });
    ret.addEventListener('change', () => { enforceReturnRange(); saveCart(); });

    // Restore cart dari session (ganti halaman pagination)
    restoreCart();

    // Pre-select buku dari URL (hanya jika belum ada di cart)
    if (PRESELECTED_ID && !selected.has(PRESELECTED_ID)) {
        const tile = document.querySelector(`.fp-book-item[data-id="${PRESELECTED_ID}"]`);
        if (tile && tile.dataset.unavail !== '1') {
            doSelect(tile);
            saveCart();
        }
    }

    renderCart();
    initPaginationSave();
});

function updateReturnMin() {
    const pickup = document.getElementById('pickup_date').value;
    const ret = document.getElementById('return_date');
    if (pickup) {
        const minDate = addDays(pickup, 1);
        const maxDate = addDays(pickup, 4);
        ret.min = minDate;
        ret.max = maxDate;
        if (!ret.value || ret.value < minDate || ret.value > maxDate) {
            ret.value = minDate;
        }
    }
}

function enforceReturnRange() {
    const pickup = document.getElementById('pickup_date').value;
    const ret = document.getElementById('return_date');
    if (!pickup || !ret.value) return;

    const minDate = addDays(pickup, 1);
    const maxDate = addDays(pickup, 4);
    if (ret.value < minDate) ret.value = minDate;
    if (ret.value > maxDate) ret.value = maxDate;
}

function addDays(dateString, days) {
    const date = new Date(dateString + 'T00:00:00');
    date.setDate(date.getDate() + days);
    return date.toISOString().split('T')[0];
}

// ── Toggle book ───────────────────────────────────────────────
function toggleBook(tile) {
    if (tile.dataset.unavail === '1') return;
    const id = tile.dataset.id;
    if (selected.has(id)) {
        doDeselect(id);
    } else {
        if (selected.size >= MAX_BORROW) {
            alert('Maksimal ' + MAX_BORROW + ' buku dalam satu peminjaman.');
            return;
        }
        doSelect(tile);
    }
    saveCart();
    renderCart();
}

function doSelect(tile) {
    const id = tile.dataset.id;
    selected.set(id, {
        title:  tile.dataset.title,
        author: tile.dataset.author,
        cover:  tile.dataset.cover,
        color:  tile.dataset.color,
    });
    // Tandai semua tile dengan id ini (di 3 shelf)
    document.querySelectorAll(`.fp-book-item[data-id="${id}"]`).forEach(t => t.classList.add('selected'));
}

function doDeselect(id) {
    selected.delete(id);
    document.querySelectorAll(`.fp-book-item[data-id="${id}"]`).forEach(t => t.classList.remove('selected'));
}

function removeBook(id) { doDeselect(id); saveCart(); renderCart(); }

// ── Render keranjang ──────────────────────────────────────────
function renderCart() {
    const cartEmpty   = document.getElementById('cartEmpty');
    const cartItems   = document.getElementById('cartItems');
    const cartCount   = document.getElementById('cartCount');
    const hidden      = document.getElementById('hiddenInputs');
    const submitBtn   = document.getElementById('submitBtn');

    cartCount.textContent = selected.size + ' / ' + MAX_BORROW;
    hidden.innerHTML = '';

    if (selected.size === 0) {
        cartEmpty.style.display = 'block';
        cartItems.innerHTML = '';
        submitBtn.disabled = true;
        return;
    }

    cartEmpty.style.display = 'none';
    submitBtn.disabled = false;

    let html = '';
    selected.forEach((book, id) => {
        hidden.innerHTML += `<input type="hidden" name="book_ids[]" value="${id}">`;
        const coverHtml = book.cover
            ? `<img src="${book.cover}" alt="${book.title}">`
            : `<div style="width:100%;height:100%;background:${book.color};display:flex;align-items:center;justify-content:center;"><i class="fas fa-book" style="color:rgba(255,255,255,0.6);font-size:16px;"></i></div>`;
        html += `
            <div class="cart-item">
                <div class="cart-thumb">${coverHtml}</div>
                <div class="cart-info">
                    <div class="cart-info-title">${book.title}</div>
                    <div class="cart-info-author">${book.author}</div>
                </div>
                <button type="button" class="cart-remove" onclick="removeBook('${id}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
    });
    cartItems.innerHTML = html;
}

// ── Search ────────────────────────────────────────────────────
document.getElementById('searchBook').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    // Sembunyikan/tampilkan per shelf-row
    document.querySelectorAll('[data-shelf]').forEach(shelfRow => {
        let visibleInRow = 0;
        shelfRow.querySelectorAll('.fp-book-item').forEach(tile => {
            const match = !q || tile.dataset.search.includes(q);
            tile.style.display = match ? '' : 'none';
            if (match) visibleInRow++;
        });
        // Sembunyikan shelf row jika tidak ada buku yang cocok
        shelfRow.style.display = visibleInRow === 0 ? 'none' : '';
    });
});
</script>
@endsection
