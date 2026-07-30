{{-- Component: Book Grid - Cover Only with Modal --}}
@props(['books', 'showSearch' => true])

<style>
    /* Search Section */
    .search-section {
        margin-bottom: 30px;
    }

    .search-box {
        position: relative;
        max-width: 100%;
    }

    .search-box input {
        width: 100%;
        padding: 15px 60px 15px 20px;
        border: none;
        border-radius: 50px;
        font-size: 0.95rem;
        font-family: 'Poppins', sans-serif;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .search-box input:focus {
        outline: none;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .search-box button {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        border: none;
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .search-box button:hover {
        transform: translateY(-50%) scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    /* Bookshelf Container */
    .bookshelf-wrapper {
        background: linear-gradient(180deg, #B8956A 0%, #9A7B5A 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: relative;
    }

    .bookshelf-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: repeating-linear-gradient(
            90deg,
            transparent,
            transparent 2px,
            rgba(0,0,0,0.03) 2px,
            rgba(0,0,0,0.03) 4px
        );
        border-radius: 20px;
        pointer-events: none;
    }

    .shelf-row {
        position: relative;
        margin-bottom: 50px;
    }

    .shelf-row:last-child {
        margin-bottom: 0;
    }

    .shelf-board {
        position: absolute;
        bottom: -25px;
        left: -20px;
        right: -20px;
        height: 15px;
        background: linear-gradient(180deg, #6D4C41 0%, #5D4037 100%);
        border-radius: 3px;
        box-shadow: 
            0 4px 8px rgba(0,0,0,0.3),
            inset 0 1px 0 rgba(255,255,255,0.1),
            inset 0 -2px 5px rgba(0,0,0,0.3);
    }

    .shelf-board::before {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        right: 0;
        height: 8px;
        background: linear-gradient(180deg, transparent, rgba(0,0,0,0.15));
        border-radius: 0 0 3px 3px;
    }

    .books-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        position: relative;
        z-index: 1;
        min-height: 220px;
    }

    .book-item {
        background: white;
        border-radius: 8px;
        overflow: visible;
        box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        transition: all 0.3s;
        cursor: pointer;
        aspect-ratio: 2/3;
        position: relative;
    }

    .book-item:hover {
        transform: translateY(-10px) rotate(2deg);
        box-shadow: 0 12px 25px rgba(0,0,0,0.35);
    }

    /* Fav button - overlay pojok kanan atas */
    .fav-btn {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 28px;
        height: 28px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        font-size: 0.72rem;
        color: #ccc;
        /* PENTING: transition HANYA untuk transform, bukan color */
        transition: transform 0.15s;
    }

    /* Hover: HANYA scale, warna TIDAK berubah */
    .fav-btn:hover {
        transform: scale(1.2);
    }

    /* Active (sudah difavoritkan): merah */
    .fav-btn.active {
        color: #E53935;
        background: white;
    }

    .book-cover-only {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #A1887F, #8D6E63);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
    }

    .book-cover-only img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .book-cover-only i {
        font-size: 60px;
        color: rgba(255,255,255,0.4);
    }

    /* IMPROVED MODAL STYLES */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.75);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        padding: 20px;
        backdrop-filter: blur(4px);
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: linear-gradient(135deg, #FFF8E1 0%, #FFFFFF 100%);
        border-radius: 24px;
        max-width: 700px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        transition: all 0.3s;
        z-index: 10;
    }

    .modal-close:hover {
        background: #E53935;
        color: white;
        transform: rotate(90deg) scale(1.1);
    }

    .modal-close i {
        font-size: 18px;
    }

    .modal-body {
        padding: 32px;
    }

    .modal-header {
        display: flex;
        align-items: flex-start;
        gap: 24px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(141, 110, 99, 0.1);
    }

    .modal-cover-small {
        flex-shrink: 0;
        width: 120px;
        height: 180px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        background: linear-gradient(135deg, #A1887F, #8D6E63);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-cover-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-cover-small i {
        font-size: 48px;
        color: rgba(255,255,255,0.3);
    }

    .modal-title-section {
        flex: 1;
    }

    .modal-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem;
        color: var(--wood-dark);
        margin: 0 0 8px 0;
        line-height: 1.3;
        font-weight: 700;
    }

    .modal-author {
        font-style: italic;
        color: var(--wood-medium);
        font-size: 1.05rem;
        margin: 0;
        font-weight: 500;
    }

    .modal-details-grid {
        display: grid;
        gap: 14px;
        margin-bottom: 20px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 16px;
        padding: 10px 0;
    }

    .detail-label {
        font-weight: 600;
        color: var(--wood-dark);
        font-size: 0.9rem;
    }

    .detail-value {
        color: var(--text-gray);
        font-size: 0.9rem;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .stock-badge.available {
        background: rgba(129, 199, 132, 0.15);
        color: #2E7D32;
    }

    .stock-badge.unavailable {
        background: rgba(229, 115, 115, 0.15);
        color: #C62828;
    }

    .stock-badge i {
        font-size: 6px;
    }

    .modal-description {
        margin-top: 16px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        border-left: 4px solid var(--wood-medium);
    }

    .modal-description h4 {
        margin: 0 0 10px 0;
        color: var(--wood-dark);
        font-size: 1rem;
        font-weight: 600;
    }

    .modal-description p {
        margin: 0;
        line-height: 1.7;
        color: var(--text-gray);
        font-size: 0.9rem;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 2px solid rgba(141, 110, 99, 0.1);
    }

    .modal-action {
        flex: 1;
        padding: 13px 20px;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-borrow {
        background: linear-gradient(135deg, #66BB6A, #43A047);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 187, 106, 0.3);
    }

    .btn-borrow:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(102, 187, 106, 0.4);
    }

    .btn-borrow:disabled {
        background: #BDBDBD;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-edit {
        background: linear-gradient(135deg, #FFA726, #FB8C00);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 167, 38, 0.3);
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(255, 167, 38, 0.4);
    }

    .btn-delete {
        background: linear-gradient(135deg, #EF5350, #E53935);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 83, 80, 0.3);
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(239, 83, 80, 0.4);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: rgba(255,255,255,0.7);
    }

    .empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin: 0 0 10px 0;
        color: white;
    }

    .empty-state p {
        margin: 0;
        font-size: 1rem;
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .pagination-info {
        color: white;
        font-weight: 500;
        background: rgba(0,0,0,0.2);
        padding: 10px 20px;
        border-radius: 8px;
    }

    .pagination-controls {
        display: flex;
        gap: 8px;
    }

    .pagination-controls a,
    .pagination-controls span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: white;
        color: var(--wood-dark);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .pagination-controls a:hover {
        background: var(--wood-medium);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .pagination-controls span.active {
        background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .pagination-controls span.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    /* Shelf sets: show/hide per breakpoint */
    .shelf-laptop { display: block; }
    .shelf-tablet { display: none; }
    .shelf-mobile { display: none; }

    .books-grid-5 { grid-template-columns: repeat(5, 1fr); }
    .books-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .books-grid-2 { grid-template-columns: repeat(2, 1fr); }

    /* Tablet ≤768px — sama dengan favorites */
    @media (max-width: 768px) {
        .shelf-laptop { display: none; }
        .shelf-tablet { display: block; }
        .shelf-mobile { display: none; }
        .pagination-wrapper { flex-direction: column; gap: 15px; }
        .bookshelf-wrapper { padding: 25px 15px; }
        .shelf-row { margin-bottom: 40px; }
        .modal-header { flex-direction: column; align-items: center; text-align: center; }
        .modal-cover-small { width: 140px; height: 210px; }
        .detail-row { grid-template-columns: 1fr; gap: 4px; }
        .modal-actions { flex-direction: column; }
    }

    /* HP ≤480px — sama dengan favorites */
    @media (max-width: 480px) {
        .shelf-laptop { display: none; }
        .shelf-tablet { display: none; }
        .shelf-mobile { display: block; }
        .bookshelf-wrapper { padding: 15px 10px; }
        .shelf-row { margin-bottom: 35px; }
    }

    /* 320px: padding sangat kecil agar buku tidak overflow */
    @media (max-width: 360px) {
        .bookshelf-wrapper {
            padding: 12px 6px;
            border-radius: 12px;
        }
        .books-grid-2 { gap: 6px; }
        .shelf-row { margin-bottom: 28px; }
        .shelf-board { left: -6px; right: -6px; }
    }

</style>


{{-- Search Bar --}}
@if($showSearch)
<div class="search-section">
    <form action="" method="GET" class="search-box">
        <input type="text" 
               name="search" 
               placeholder="Cari judul buku, penulis, atau ISBN..." 
               value="{{ request('search') }}">
        <button type="submit">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>
@endif

{{-- Bookshelf - ALWAYS 4 ROWS --}}
<div class="bookshelf-wrapper">
    @php
        $colors = [
            'linear-gradient(135deg, #A1887F, #8D6E63)',
            'linear-gradient(135deg, #7986CB, #5C6BC0)',
            'linear-gradient(135deg, #81C784, #66BB6A)',
            'linear-gradient(135deg, #FFB74D, #FFA726)',
            'linear-gradient(135deg, #E57373, #EF5350)',
            'linear-gradient(135deg, #9575CD, #7E57C2)',
        ];
        
        $booksArray = $books->items();

        // 3 versi chunk sesuai breakpoint
        $books5 = array_chunk(array_pad(array_values($booksArray), ceil(count($booksArray)/5)*5, null), 5);  // laptop
        $books3 = array_chunk(array_pad(array_values($booksArray), ceil(count($booksArray)/3)*3, null), 3);  // tablet
        $books2 = array_chunk(array_pad(array_values($booksArray), ceil(count($booksArray)/2)*2, null), 2);  // HP

        // Favorit IDs
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = \App\Models\Favorite::where('user_id', Auth::id())->pluck('book_id')->toArray();
        }
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
    @endphp

    @if(count($booksArray) > 0 || $showSearch)
        {{-- Hint teks kontras --}}
        <div style="text-align:center; margin-bottom:18px;">
            <span style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);padding:7px 18px;border-radius:20px;font-size:0.82rem;font-weight:500;color:#FFF8EE;">
                <i class="fas fa-hand-pointer"></i>
                @if($isAdmin)
                    Klik cover buku untuk melihat detail dan melakukan perubahan
                @else
                    Klik cover buku untuk melihat detail dan melakukan peminjaman
                @endif
            </span>
        </div>

        @php
        // Macro render baris buku
        $renderBook = function($book, $index, $favoriteIds, $isAdmin, $colors) {
            return $book; // hanya untuk referensi — pakai @foreach langsung
        };
        @endphp

        {{-- LAPTOP: chunk 5, tampil hanya ≥769px --}}
        <div class="shelf-laptop">
            @foreach($books5 as $row)
                <div class="shelf-row">
                    <div class="books-grid books-grid-5">
                        @foreach($row as $idx => $book)
                            @if($book)
                                <div class="book-item">
                                    @if(!$isAdmin)
                                        <button class="fav-btn {{ in_array($book->id, $favoriteIds) ? 'active' : '' }}"
                                                onclick="event.stopPropagation(); toggleFav(this, {{ $book->id }})" title="Favorit">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    @endif
                                    <div class="book-cover-only" style="background: {{ $colors[$idx % 6] }};" onclick="openBookModal({{ $book->id }})">
                                        @if($book->cover_image)
                                            <img src="{{ asset('img/covers/' . $book->cover_image) }}" alt="{{ $book->title }}">
                                        @else
                                            <i class="fas fa-book"></i>
                                        @endif
                                    </div>
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

        {{-- TABLET: chunk 3, tampil hanya 481px–768px --}}
        <div class="shelf-tablet">
            @foreach($books3 as $row)
                <div class="shelf-row">
                    <div class="books-grid books-grid-3">
                        @foreach($row as $idx => $book)
                            @if($book)
                                <div class="book-item">
                                    @if(!$isAdmin)
                                        <button class="fav-btn {{ in_array($book->id, $favoriteIds) ? 'active' : '' }}"
                                                onclick="event.stopPropagation(); toggleFav(this, {{ $book->id }})" title="Favorit">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    @endif
                                    <div class="book-cover-only" style="background: {{ $colors[$idx % 6] }};" onclick="openBookModal({{ $book->id }})">
                                        @if($book->cover_image)
                                            <img src="{{ asset('img/covers/' . $book->cover_image) }}" alt="{{ $book->title }}">
                                        @else
                                            <i class="fas fa-book"></i>
                                        @endif
                                    </div>
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

        {{-- HP: chunk 2, tampil hanya ≤480px --}}
        <div class="shelf-mobile">
            @foreach($books2 as $row)
                <div class="shelf-row">
                    <div class="books-grid books-grid-2">
                        @foreach($row as $idx => $book)
                            @if($book)
                                <div class="book-item">
                                    @if(!$isAdmin)
                                        <button class="fav-btn {{ in_array($book->id, $favoriteIds) ? 'active' : '' }}"
                                                onclick="event.stopPropagation(); toggleFav(this, {{ $book->id }})" title="Favorit">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    @endif
                                    <div class="book-cover-only" style="background: {{ $colors[$idx % 6] }};" onclick="openBookModal({{ $book->id }})">
                                        @if($book->cover_image)
                                            <img src="{{ asset('img/covers/' . $book->cover_image) }}" alt="{{ $book->title }}">
                                        @else
                                            <i class="fas fa-book"></i>
                                        @endif
                                    </div>
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

        @if($books->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Page {{ $books->currentPage() }} of {{ $books->lastPage() }}
                </div>
                
                <div class="pagination-controls">
                    @if($books->onFirstPage())
                        <span class="disabled">&lt;</span>
                    @else
                        <a href="{{ $books->previousPageUrl() }}">&lt;</a>
                    @endif

                    @foreach(range(1, $books->lastPage()) as $page)
                        @if($page == $books->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $books->url($page) }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($books->hasMorePages())
                        <a href="{{ $books->nextPageUrl() }}">&gt;</a>
                    @else
                        <span class="disabled">&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <h3>Tidak ada buku ditemukan</h3>
            <p>{{ request('search') ? 'Coba kata kunci lain' : 'Belum ada buku di perpustakaan' }}</p>
        </div>
    @endif
</div>

{{-- Modal --}}
<div class="modal-overlay" id="bookModal" onclick="closeModalOnOverlay(event)">
    <div class="modal-content">
        <button class="modal-close" onclick="closeBookModal()">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-body" id="modalBody">
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: var(--wood-medium);"></i>
                <p style="margin-top: 20px; color: var(--wood-medium);">Memuat detail buku...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const API_BASE_URL = '{{ url('/') }}';

// Data buku dari server — TANPA loading, langsung tersedia
const booksData = {
    @foreach($booksArray as $b)
    {{ $b->id }}: {
        id: {{ $b->id }},
        title: {!! json_encode($b->title, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
        author: {!! json_encode($b->author, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
        isbn: {!! json_encode($b->isbn ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
        publisher: {!! json_encode($b->publisher ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
        publication_year: {{ $b->publication_year ?? 'null' }},
        description: {!! json_encode($b->description ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
        stock: {{ $b->stock }},
        available: {{ $b->available }},
        cover_image: {!! json_encode($b->cover_image ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
    },
    @endforeach
};

function openBookModal(bookId) {
    const modal = document.getElementById('bookModal');
    const modalBody = document.getElementById('modalBody');
    const book = booksData[bookId];

    if (!book) {
        modalBody.innerHTML = `<div style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:40px;color:#E57373;"></i><p style="margin-top:20px;color:var(--wood-medium);">Data buku tidak ditemukan.</p></div>`;
        modal.classList.add('active');
        return;
    }

    const isAdmin = {{ Auth::check() && Auth::user()->role === 'admin' ? 'true' : 'false' }};

    modalBody.innerHTML = `
        <div class="modal-header">
            <div class="modal-cover-small">
                ${book.cover_image
                    ? `<img src="${API_BASE_URL}/img/covers/${book.cover_image}" alt="${escapeHtml(book.title)}">`
                    : `<i class="fas fa-book"></i>`
                }
            </div>
            <div class="modal-title-section">
                <h2 class="modal-title">${escapeHtml(book.title)}</h2>
                <p class="modal-author">oleh ${escapeHtml(book.author)}</p>
            </div>
        </div>

        <div class="modal-details-grid">
            <div class="detail-row">
                <span class="detail-label">ISBN</span>
                <span class="detail-value">${escapeHtml(book.isbn) || '-'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Penerbit</span>
                <span class="detail-value">${escapeHtml(book.publisher) || '-'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tahun Terbit</span>
                <span class="detail-value">${book.publication_year || '-'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ketersediaan</span>
                <span class="detail-value">
                    <span class="stock-badge ${book.available > 0 ? 'available' : 'unavailable'}">
                        <i class="fas fa-circle"></i>
                        ${book.available} dari ${book.stock} tersedia
                    </span>
                </span>
            </div>
        </div>

        ${book.description ? `
            <div class="modal-description">
                <h4>Deskripsi</h4>
                <p>${escapeHtml(book.description)}</p>
            </div>
        ` : ''}

        <div class="modal-actions">
            ${isAdmin
                ? `
                    <a href="${API_BASE_URL}/admin/books/${book.id}/edit" class="modal-action btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button class="modal-action btn-delete" onclick="deleteBook(${book.id})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                `
                : `
                    ${book.available > 0
                        ? `<a href="${API_BASE_URL}/form-peminjaman?book_id=${book.id}" class="modal-action btn-borrow">
                            <i class="fas fa-book-reader"></i> Pinjam Buku
                           </a>`
                        : `<button class="modal-action btn-borrow" disabled>
                            <i class="fas fa-times-circle"></i> Stok Habis
                           </button>`
                    }
                `
            }
        </div>
    `;

    modal.classList.add('active');
}
                
function closeBookModal() {
    document.getElementById('bookModal').classList.remove('active');
}

function closeModalOnOverlay(event) {
    if (event.target.id === 'bookModal') {
        closeBookModal();
    }
}

// borrowBook removed — now redirects to /form-peminjaman via anchor tag

function deleteBook(bookId) {
    if (confirm('Apakah Anda yakin ingin menghapus buku ini? Data yang sudah dihapus tidak dapat dikembalikan!')) {
        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${API_BASE_URL}/admin/books/${bookId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add DELETE method
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function toggleFav(btn, bookId) {
    const isActive = btn.classList.contains('active');
    const url = isActive
        ? `/favorit/${bookId}/remove`
        : `/favorit/${bookId}/add`;

    // Optimistic UI: langsung toggle dulu
    btn.classList.toggle('active');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        if (!data.success) {
            // Rollback jika gagal
            btn.classList.toggle('active');
        }
    })
    .catch(() => {
        // Rollback jika error
        btn.classList.toggle('active');
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBookModal();
    }
});
</script>
@endpush