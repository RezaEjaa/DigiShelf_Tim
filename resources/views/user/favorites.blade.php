@extends('layouts.app-navbar')
@section('title', 'Favorit - Digishelf')

@section('content')
<style>
    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .section-head h2 {
        font-family: 'Crimson Pro', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    /* Bookshelf sama seperti books-grid */
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
        top: 0; left: 0; right: 0; bottom: 0;
        background: repeating-linear-gradient(90deg,transparent,transparent 2px,rgba(0,0,0,0.03) 2px,rgba(0,0,0,0.03) 4px);
        border-radius: 20px;
        pointer-events: none;
    }

    .shelf-row { position: relative; margin-bottom: 50px; }
    .shelf-row:last-child { margin-bottom: 0; }
    .shelf-board {
        position: absolute;
        bottom: -25px; left: -20px; right: -20px;
        height: 15px;
        background: linear-gradient(180deg, #6D4C41 0%, #5D4037 100%);
        border-radius: 3px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1), inset 0 -2px 5px rgba(0,0,0,0.3);
    }
    .shelf-board::before {
        content: '';
        position: absolute;
        bottom: -8px; left: 0; right: 0;
        height: 8px;
        background: linear-gradient(180deg, transparent, rgba(0,0,0,0.15));
        border-radius: 0 0 3px 3px;
    }

    .fav-books-grid {
        display: grid;
        gap: 20px;
        position: relative;
        z-index: 1;
        min-height: 180px;
    }

    .fav-books-grid-5 { grid-template-columns: repeat(5, 1fr); }
    .fav-books-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .fav-books-grid-2 { grid-template-columns: repeat(2, 1fr); }

    /* Show/hide shelf sets per breakpoint */
    .fav-shelf-laptop { display: block; }
    .fav-shelf-tablet { display: none; }
    .fav-shelf-mobile { display: none; }

    .fav-book-item {
        background: white;
        border-radius: 8px;
        overflow: visible;
        box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        transition: all 0.3s;
        cursor: pointer;
        aspect-ratio: 2/3;
        position: relative;
    }
    .fav-book-item:hover {
        transform: translateY(-10px) rotate(2deg);
        box-shadow: 0 12px 25px rgba(0,0,0,0.35);
    }

    .fav-cover-only {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        border-radius: 8px;
    }
    .fav-cover-only img { width: 100%; height: 100%; object-fit: cover; }
    .fav-cover-only i { font-size: 50px; color: rgba(255,255,255,0.4); }

    /* Heart button */
    .fav-heart-btn {
        position: absolute;
        top: 6px; right: 6px;
        width: 28px; height: 28px;
        background: white;
        border: none;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        transition: transform 0.2s;
        font-size: 0.72rem;
        color: #E53935;
    }
    .fav-heart-btn:hover { transform: scale(1.15); }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: rgba(255,255,255,0.7);
    }
    .empty-state i { font-size: 70px; margin-bottom: 20px; opacity: 0.5; display: block; }
    .empty-state h3 { font-size: 1.5rem; color: white; margin-bottom: 10px; }

    @media (max-width: 768px) {
        .fav-shelf-laptop { display: none; }
        .fav-shelf-tablet { display: block; }
        .fav-shelf-mobile { display: none; }
        .bookshelf-wrapper { padding: 25px 15px; }
        .shelf-row { margin-bottom: 40px; }
    }
    @media (max-width: 480px) {
        .fav-shelf-laptop { display: none; }
        .fav-shelf-tablet { display: none; }
        .fav-shelf-mobile { display: block; }
        .bookshelf-wrapper { padding: 15px 10px; }
        .shelf-row { margin-bottom: 35px; }
    }
</style>

<div class="section-head">
    <h2>Buku Favorit Saya</h2>
</div>

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
        $favArray = $favorites->all();

        // 3 versi chunk
        $fav5 = $favArray ? array_chunk(array_pad(array_values($favArray), ceil(count($favArray)/5)*5, null), 5) : [[]];
        $fav3 = $favArray ? array_chunk(array_pad(array_values($favArray), ceil(count($favArray)/3)*3, null), 3) : [[]];
        $fav2 = $favArray ? array_chunk(array_pad(array_values($favArray), ceil(count($favArray)/2)*2, null), 2) : [[]];
    @endphp

    @if($favorites->count() > 0)

        @php
        // Helper render satu buku favorit
        $renderFav = function($f, $i, $colors) { return [$f, $i, $colors]; };
        @endphp

        {{-- LAPTOP: chunk 5 --}}
        <div class="fav-shelf-laptop">
            @foreach($fav5 as $row)
                <div class="shelf-row">
                    <div class="fav-books-grid fav-books-grid-5">
                        @foreach($row as $i => $f)
                            @if($f)
                                <div class="fav-book-item">
                                    <button class="fav-heart-btn" onclick="event.stopPropagation(); removeFav(this, {{ $f->book->id }})" title="Hapus dari favorit">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <div class="fav-cover-only" style="background: {{ $colors[$i % 6] }};">
                                        @if($f->book->cover_image)
                                            <img src="{{ asset('img/covers/' . $f->book->cover_image) }}" alt="{{ $f->book->title }}">
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

        {{-- TABLET: chunk 3 --}}
        <div class="fav-shelf-tablet">
            @foreach($fav3 as $row)
                <div class="shelf-row">
                    <div class="fav-books-grid fav-books-grid-3">
                        @foreach($row as $i => $f)
                            @if($f)
                                <div class="fav-book-item">
                                    <button class="fav-heart-btn" onclick="event.stopPropagation(); removeFav(this, {{ $f->book->id }})" title="Hapus dari favorit">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <div class="fav-cover-only" style="background: {{ $colors[$i % 6] }};">
                                        @if($f->book->cover_image)
                                            <img src="{{ asset('img/covers/' . $f->book->cover_image) }}" alt="{{ $f->book->title }}">
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

        {{-- HP: chunk 2 --}}
        <div class="fav-shelf-mobile">
            @foreach($fav2 as $row)
                <div class="shelf-row">
                    <div class="fav-books-grid fav-books-grid-2">
                        @foreach($row as $i => $f)
                            @if($f)
                                <div class="fav-book-item">
                                    <button class="fav-heart-btn" onclick="event.stopPropagation(); removeFav(this, {{ $f->book->id }})" title="Hapus dari favorit">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <div class="fav-cover-only" style="background: {{ $colors[$i % 6] }};">
                                        @if($f->book->cover_image)
                                            <img src="{{ asset('img/covers/' . $f->book->cover_image) }}" alt="{{ $f->book->title }}">
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

    @else
        <div class="empty-state">
            <i class="fas fa-heart"></i>
            <h3>Belum ada buku favorit</h3>
            <p>Tandai buku favorit Anda dari koleksi buku</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function removeFav(btn, bookId) {
    fetch(`/favorit/${bookId}/remove`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Hapus card dari DOM dengan animasi
            const card = btn.closest('.fav-book-item');
            card.style.transition = 'all 0.3s';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            setTimeout(() => { card.remove(); location.reload(); }, 300);
        }
    })
    .catch(() => {});
}
</script>
@endpush
@endsection