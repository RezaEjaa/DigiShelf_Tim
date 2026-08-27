<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'book'])
            ->where('status', 'active')
            ->orWhere('status', 'overdue')
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        // Update overdue status
        foreach ($borrowings as $borrowing) {
            if ($borrowing->isOverdue() && $borrowing->status === 'active') {
                $borrowing->status = 'overdue';
                $borrowing->save();
            }
        }

        return view('admin.borrowings.index', compact('borrowings'));
    }

    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $books = Book::where('available', '>', 0)->orderBy('title')->get();
        
        return view('admin.borrowings.create', compact('users', 'books'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'borrowed_date' => 'required|date',
            'due_date' => 'required|date|after:borrowed_date',
        ]);

        $book = Book::find($validated['book_id']);
        
        if ($book->available <= 0) {
            return back()->with('error', 'Buku tidak tersedia');
        }

        $validated['status'] = 'active';
        
        Borrowing::create($validated);
        
        // Decrease available count
        $book->decrement('available');

        return redirect()->route('admin.borrowings.index')->with('success', 'Peminjaman berhasil dibuat');
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'returned') {
            return back()->with('error', 'Buku sudah dikembalikan');
        }

        $borrowing->update([
            'return_date' => Carbon::now(),
            'status' => 'returned',
        ]);

        // Increase available count
        $borrowing->book->increment('available');

        return redirect()->route('admin.borrowings.index')->with('success', 'Buku berhasil dikembalikan');
    }

    public function history()
    {
        $borrowings = Borrowing::with(['user', 'book'])
            ->where('status', 'returned')
            ->orderBy('return_date', 'desc')
            ->paginate(15);

        return view('admin.borrowings.history', compact('borrowings'));
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status === 'active' || $borrowing->status === 'overdue') {
            $borrowing->book->increment('available');
        }

        $borrowing->delete();

        return back()->with('success', 'Data peminjaman berhasil dihapus');
    }
}