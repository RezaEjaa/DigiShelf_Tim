<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowingRequest;

class BorrowingHistoryController extends Controller
{
    // Riwayat peminjaman dari semua petugas untuk admin
    public function index()
    {
        $requests = BorrowingRequest::whereIn('status', ['returned', 'cancelled'])
            ->with(['user', 'items.book', 'processedBy'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('admin.borrowings.all-history', compact('requests'));
    }
}
