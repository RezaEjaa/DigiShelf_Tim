<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowingRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QrVerificationController extends Controller
{
    // ── GET /admin/verify-qr ──────────────────────────────────
    public function index()
    {
        // Daftar pending yang bisa diverifikasi
        $pendingRequests = BorrowingRequest::where('status', 'pending')
            ->where('expires_at', '>', Carbon::now())
            ->with(['user', 'items.book'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.borrowings.verify-qr', compact('pendingRequests'));
    }

    // ── POST /admin/verify-qr ─────────────────────────────────
    // Dipanggil saat admin scan / input QR code manual
    public function verify(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCode = strtoupper(trim($request->qr_code));

        $borrowingRequest = BorrowingRequest::where('qr_code', $qrCode)
            ->with(['user', 'items.book'])
            ->first();

        if (!$borrowingRequest) {
            return back()->with('error', 'QR Code tidak ditemukan.');
        }

        if ($borrowingRequest->isExpired() || $borrowingRequest->isCancelled()) {
            // Pastikan statusnya cancelled
            if ($borrowingRequest->isPending()) {
                $borrowingRequest->update(['status' => 'cancelled']);
            }
            return back()->with('error', 'QR Code sudah kadaluarsa atau dibatalkan.');
        }

        if ($borrowingRequest->isActive()) {
            return back()->with('info', 'Buku ini sudah dalam status "Sedang Dipinjam".')
                         ->with('kode_result', $borrowingRequest);
        }

        if ($borrowingRequest->isReturned()) {
            return back()->with('info', 'Peminjaman ini sudah dikembalikan.')
                         ->with('kode_result', $borrowingRequest);
        }

        // Status pending → tampilkan detail untuk dikonfirmasi
        return back()
            ->with('kode_result', $borrowingRequest)
            ->with('kode_action', 'confirm');
    }

    // ── POST /admin/verify-qr/confirm ────────────────────────
    // Admin konfirmasi: ubah status pending → active, kurangi stok
    public function confirm(Request $request)
    {
        $request->validate(['request_id' => 'required|exists:borrowing_requests,id']);

        $borrowingRequest = BorrowingRequest::with('items.book')->findOrFail($request->request_id);

        if (!$borrowingRequest->isPending()) {
            return redirect()->route('admin.borrowings.index')
                ->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        if ($borrowingRequest->isExpired()) {
            $borrowingRequest->update(['status' => 'cancelled']);
            return redirect()->route('admin.borrowings.index')
                ->with('error', 'Kode sudah kadaluarsa, peminjaman dibatalkan.');
        }

        // Kurangi stok setiap buku
        foreach ($borrowingRequest->items as $item) {
            $book = $item->book;
            if ($book->available <= 0) {
                return redirect()->route('admin.verify-qr.index')
                    ->with('error', "Stok buku \"{$book->title}\" sudah habis, tidak bisa diverifikasi.");
            }
            $book->decrement('available');
        }

        $borrowingRequest->update([
            'status'      => 'active',
            'verified_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.borrowings.index')
            ->with('success', "Peminjaman {$borrowingRequest->qr_code} berhasil diverifikasi! Buku siap diserahkan.");
    }

    // ── POST /admin/verify-qr/return ─────────────────────────
    // Admin kembalikan buku: active → returned, stok naik
    public function returnBook(Request $request)
    {
        $request->validate(['request_id' => 'required|exists:borrowing_requests,id']);

        $borrowingRequest = BorrowingRequest::with('items.book')->findOrFail($request->request_id);

        if (!$borrowingRequest->isActive()) {
            return redirect()->route('admin.verify-qr.index')
                ->with('error', 'Peminjaman ini tidak dalam status "Sedang Dipinjam".');
        }

        // Kembalikan stok
        foreach ($borrowingRequest->items as $item) {
            $item->book->increment('available');
        }

        $borrowingRequest->update([
            'status'      => 'returned',
            'returned_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.borrowings.index')
            ->with('success', "Buku dari peminjaman {$borrowingRequest->qr_code} berhasil dikembalikan.");
    }

    // ── GET /admin/borrowings/active ──────────────────────────
    // Daftar peminjaman aktif di panel admin
    public function activeList()
    {
        $requests = BorrowingRequest::whereIn('status', ['pending', 'active'])
            ->with(['user', 'items.book'])
            ->orderByRaw("FIELD(status, 'active', 'pending')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Cancel yang expired saat load
        foreach ($requests as $req) {
            if ($req->isExpired()) {
                $req->update(['status' => 'cancelled']);
            }
        }

        return view('admin.borrowings.index', compact('requests'));
    }

    // ── GET /admin/borrowings/history ─────────────────────────
    public function history()
    {
        $requests = BorrowingRequest::whereIn('status', ['returned', 'cancelled'])
            ->with(['user', 'items.book'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('admin.borrowings.history', compact('requests'));
    }
}