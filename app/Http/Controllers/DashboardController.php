<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics
        $stats = [
            'total_books' => Book::count(),
            'active_borrowings' => Borrowing::where('user_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'total_borrowings' => Borrowing::where('user_id', $user->id)->count(),
            'total_favorites' => Favorite::where('user_id', $user->id)->count(),
        ];
        
        // Get active borrowings
        $activeBorrowings = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('due_date', 'asc')
            ->limit(3)
            ->get();
        
        // Get recommended books (6 latest available books)
        $recommendedBooks = Book::where('available', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        
        return view('user.dashboard', compact('stats', 'activeBorrowings', 'recommendedBooks'));
    }
}