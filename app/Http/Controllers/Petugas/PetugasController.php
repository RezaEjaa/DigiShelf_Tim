<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BorrowingRequest;
use Carbon\Carbon;

class PetugasController extends Controller
{
    public function dashboard()
    {
        // Auto-cancel expired pending sebelum tampil
        BorrowingRequest::where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        // Statistik untuk petugas
        $stats = [
            'pending_verifications' => BorrowingRequest::where('status', 'pending')
                                        ->where('expires_at', '>', Carbon::now())
                                        ->count(),
            'active_borrowings' => BorrowingRequest::where('status', 'active')->count(),
            'my_processed_today' => BorrowingRequest::where('processed_by', auth()->id())
                                        ->whereDate('verified_at', Carbon::today())
                                        ->count(),
            'my_total_processed' => BorrowingRequest::where('processed_by', auth()->id())
                                        ->whereIn('status', ['active', 'returned'])
                                        ->count(),
        ];

        // Peminjaman pending untuk verifikasi
        $pendingRequests = BorrowingRequest::where('status', 'pending')
            ->where('expires_at', '>', Carbon::now())
            ->with(['user', 'items.book'])
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // Peminjaman aktif terbaru
        $activeBorrowings = BorrowingRequest::where('status', 'active')
            ->with(['user', 'items.book'])
            ->orderBy('verified_at', 'desc')
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'pendingRequests', 'activeBorrowings'));
    }
}
