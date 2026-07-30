<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowingRequest;
use App\Models\BorrowingRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowController extends Controller
{
    public function scan(string $qrCode)
    {
        $borrowingRequest = BorrowingRequest::where('qr_code', strtoupper($qrCode))
            ->with('user')
            ->firstOrFail();

        return view('user.qr-scan', compact('borrowingRequest'));
    }

    // ── GET /form-peminjaman ──────────────────────────────────
    public function form(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Hitung jumlah buku yang sedang aktif dipinjam user
        $activeBooksCount = BorrowingRequest::where('user_id', $user->id)
            ->where('status', 'active')
            ->withCount('items')
            ->get()
            ->sum('items_count');

        $maxBorrow = 5;
        $remaining = max(0, $maxBorrow - $activeBooksCount);

        // Semua buku yang tersedia (stok > 0)
        $books = Book::where('available', '>', 0)
            ->orderBy('title')
            ->paginate(20);

        // Buku yang sudah dipinjam/pending user (tidak bisa dipinjam lagi)
        $borrowedBookIds = BorrowingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->with('items')
            ->get()
            ->flatMap(fn($r) => $r->items->pluck('book_id'))
            ->unique()
            ->toArray();

        // Pre-select buku dari query string ?book_id=X (dari tombol modal)
        $preselectedId = $request->query('book_id');

        return view('user.form-peminjaman', compact(
            'books',
            'borrowedBookIds',
            'remaining',
            'maxBorrow',
            'preselectedId'
        ));
    }

    // ── POST /form-peminjaman ─────────────────────────────────
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'book_ids'    => 'required|array|min:1|max:5',
            'book_ids.*'  => 'required|exists:books,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
        ], [
            'book_ids.required'    => 'Pilih minimal 1 buku.',
            'book_ids.min'         => 'Pilih minimal 1 buku.',
            'book_ids.max'         => 'Maksimal 5 buku dalam satu peminjaman.',
            'pickup_date.required' => 'Tanggal pengambilan harus diisi.',
            'pickup_date.after_or_equal' => 'Tanggal pengambilan tidak boleh di masa lalu.',
            'return_date.required' => 'Tanggal pengembalian harus diisi.',
            'return_date.after'    => 'Tanggal pengembalian harus setelah tanggal pengambilan.',
        ]);

        $pickupDate = Carbon::parse($request->pickup_date)->startOfDay();
        $returnDate = Carbon::parse($request->return_date)->startOfDay();

        if ($returnDate->gt($pickupDate->copy()->addDays(4))) {
            return back()
                ->withErrors(['return_date' => 'Tanggal pengembalian maksimal 4 hari setelah tanggal pengambilan.'])
                ->withInput();
        }

        $bookIds = array_unique($request->book_ids);

        // Cek batas maksimal (termasuk yg sedang aktif)
        $activeBooksCount = BorrowingRequest::where('user_id', $user->id)
            ->where('status', 'active')
            ->withCount('items')
            ->get()
            ->sum('items_count');

        if (($activeBooksCount + count($bookIds)) > 5) {
            return back()->withErrors(['book_ids' => 'Total buku yang dipinjam melebihi batas maksimal 5.'])->withInput();
        }

        // Validasi per buku
        foreach ($bookIds as $bookId) {
            $book = Book::find($bookId);
            if (!$book || $book->available <= 0) {
                return back()->withErrors(['book_ids' => "Buku \"{$book->title}\" tidak tersedia / stok habis."])->withInput();
            }

            // Cek user sudah pinjam buku ini (pending/aktif)
            $alreadyBorrowed = BorrowingRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'active'])
                ->whereHas('items', fn($q) => $q->where('book_id', $bookId))
                ->exists();

            if ($alreadyBorrowed) {
                return back()->withErrors(['book_ids' => "Anda sudah meminjam buku \"{$book->title}\"." ])->withInput();
            }
        }

        // Buat BorrowingRequest
        $borrowingRequest = BorrowingRequest::create([
            'user_id'     => $user->id,
            'qr_code'     => BorrowingRequest::generateQrCode(),
            'pickup_date' => $request->pickup_date,
            'return_date' => $request->return_date,
            'status'      => 'pending',
            'expires_at'  => Carbon::now()->addHours(24),
        ]);

        // Simpan item buku (BELUM kurangi stok — stok dikurangi saat admin verifikasi)
        foreach ($bookIds as $bookId) {
            BorrowingRequestItem::create([
                'borrowing_request_id' => $borrowingRequest->id,
                'book_id'              => $bookId,
            ]);
        }

        return redirect()
            ->route('user.borrowings.detail', $borrowingRequest->id)
            ->with('success', 'Permintaan peminjaman berhasil dibuat! Tunjukkan QR Code ke petugas perpustakaan.');
    }

    // ── GET /peminjaman ───────────────────────────────────────
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Auto-cancel yang sudah expired sebelum tampil
        $this->cancelExpired($user->id);

        $requests = BorrowingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->with(['items.book'])
            ->orderByRaw("FIELD(status, 'active', 'pending')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.borrowings', compact('requests'));
    }

    // ── GET /peminjaman/{id} ──────────────────────────────────
    public function detail(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $borrowingRequest = BorrowingRequest::where('user_id', $user->id)
            ->with(['items.book', 'user'])
            ->findOrFail($id);

        // Auto cancel jika sudah expired
        if ($borrowingRequest->isExpired()) {
            $this->doCancelRequest($borrowingRequest);
        }

        return view('user.peminjaman-detail', compact('borrowingRequest'));
    }

    // ── Helper: cancel expired ────────────────────────────────
    private function cancelExpired(int $userId): void
    {
        $expired = BorrowingRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->with('items.book')
            ->get();

        foreach ($expired as $req) {
            $this->doCancelRequest($req);
        }
    }

    private function doCancelRequest(BorrowingRequest $req): void
    {
        // Status pending = stok belum dikurangi, jadi tidak perlu restore
        $req->update(['status' => 'cancelled']);
    }
}
