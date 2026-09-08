<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowingRequest;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Auto-cancel expired pending sebelum tampil
        BorrowingRequest::where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $stats = [
            'total_books'      => Book::count(),
            'active_borrowings'=> BorrowingRequest::where('status', 'active')->count(),
            'pending_borrowings'=> BorrowingRequest::where('status', 'pending')
                                    ->where('expires_at', '>', Carbon::now())
                                    ->count(),
            'total_users'      => User::where('role', 'user')->count(),
            'total_petugas'    => User::where('role', 'petugas')->count(),
        ];

        // Peminjaman aktif untuk ditampilkan di dashboard
        $activeBorrowings = BorrowingRequest::whereIn('status', ['active', 'pending'])
            ->with(['user', 'items.book'])
            ->orderByRaw("FIELD(status, 'active', 'pending')")
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 5 buku terbaru
        $latestBooks = Book::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'activeBorrowings', 'latestBooks'));
    }

}