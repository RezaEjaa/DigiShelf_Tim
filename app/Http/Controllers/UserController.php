<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingRequest;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Auto-cancel expired pending sebelum load dashboard
        $this->cancelExpired($user->id);

        // $stats — struktur sama persis dengan asli, hanya active_borrowings
        // sekarang dihitung dari BorrowingRequest (pending+active)
        $stats = [
            'total_books'       => Book::count(),
            'active_borrowings' => BorrowingRequest::where('user_id', $user->id)
                                    ->whereIn('status', ['pending', 'active'])
                                    ->count(),
            'borrowing_history' => BorrowingRequest::where('user_id', $user->id)->count(),
            'total_favorites'   => Favorite::where('user_id', $user->id)->count(),
        ];

        // activeBorrowings sekarang dari BorrowingRequest
        $activeBorrowings = BorrowingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->with('items.book')
            ->orderByRaw("FIELD(status, 'active', 'pending')")
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recommendedBooks = Book::where('available', '>', 0)->latest()->take(6)->get();

        return view('user.dashboard', compact('stats', 'activeBorrowings', 'recommendedBooks'));
    }

    public function books(Request $request)
    {
        $query = Book::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%")
                  ->orWhere('publisher', 'LIKE', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(20);

        if ($request->ajax() || $request->wantsJson()) {
            return view('user.books', compact('books'));
        }

        return view('user.books', compact('books'));
    }

    // GET /peminjaman — ditangani BorrowController@index
    // Method ini redirect agar tidak error jika ada referensi lama
    public function borrowings()
    {
        return redirect()->route('user.borrowings');
    }

    // GET /riwayat — pakai BorrowingRequest (returned + cancelled)
    public function history(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->cancelExpired($user->id);

        $filterStatus = $request->query('status', 'all');

        $query = BorrowingRequest::where('user_id', $user->id)
            ->whereIn('status', ['returned', 'cancelled'])
            ->with('items.book');

        if ($filterStatus === 'returned') {
            $query->where('status', 'returned');
        } elseif ($filterStatus === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $requests = $query->orderBy('updated_at', 'desc')->paginate(10);

        $totalAll       = BorrowingRequest::where('user_id', $user->id)
                            ->whereIn('status', ['returned', 'cancelled'])->count();
        $totalReturned  = BorrowingRequest::where('user_id', $user->id)
                            ->where('status', 'returned')->count();
        $totalCancelled = BorrowingRequest::where('user_id', $user->id)
                            ->where('status', 'cancelled')->count();

        return view('user.history', compact(
            'requests', 'filterStatus', 'totalAll', 'totalReturned', 'totalCancelled'
        ));
    }

    public function favorites()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $favorites = Favorite::with('book')->where('user_id', $user->id)->latest()->get();
        return view('user.favorites', compact('favorites'));
    }

    public function account()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('user.akun', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = ['name' => 'required|string|max:255'];

        if (!$user->google_id) {
            $rules['email']    = 'required|email|unique:users,email,' . $user->id;
            $rules['password'] = 'nullable|min:8|confirmed';
        }

        $rules['profile_photo'] = 'nullable|image|mimes:jpeg,png,webp|max:2048';

        $validated = $request->validate($rules);

        $user->name = $validated['name'];

        if (!$user->google_id) {
            $user->email = $validated['email'];
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                $oldPath = public_path('img/profile_photos/' . $user->profile_photo);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $file     = $request->file('profile_photo');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/profile_photos'), $filename);
            $user->profile_photo = $filename;
        }

        $user->save();
        return redirect()->route('user.account')->with('success', 'Akun berhasil diupdate');
    }

    public function destroy()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek masih ada peminjaman aktif/pending
        $hasActive = BorrowingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Tidak dapat menghapus akun. Masih ada peminjaman aktif.');
        }

        Auth::logout();
        $user->delete();
        return redirect()->route('login')->with('success', 'Akun berhasil dihapus');
    }

    // ── Helper private ────────────────────────────────────────
    private function cancelExpired(int $userId): void
    {
        BorrowingRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);
    }
}