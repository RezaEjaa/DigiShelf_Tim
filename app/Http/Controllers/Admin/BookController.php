<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%");
            });
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        return view('admin.books.create');
    }

    /**
     * Store a newly created book in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/avif|max:2048',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/covers'), $filename);
            $validated['cover_image'] = $filename;
        }

        // Set available = stock initially
        $validated['available'] = $validated['stock'];

        Book::create($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    /**
     * Update the specified book in database.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/avif|max:2048',
            'remove_cover' => 'nullable|in:0,1',
        ]);

        // Handle cover removal
        if ($request->remove_cover == '1' && $book->cover_image) {
            // Delete old cover file
            if (file_exists(public_path('img/covers/' . $book->cover_image))) {
                unlink(public_path('img/covers/' . $book->cover_image));
            }
            $validated['cover_image'] = null;
        }

        // Handle new cover upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover if exists
            if ($book->cover_image && file_exists(public_path('img/covers/' . $book->cover_image))) {
                unlink(public_path('img/covers/' . $book->cover_image));
            }

            $file = $request->file('cover_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/covers'), $filename);
            $validated['cover_image'] = $filename;
        } elseif (!isset($validated['cover_image'])) {
            // Keep existing cover if no new upload and not removed
            unset($validated['cover_image']);
        }

        // Update available books based on stock change
        $stockDiff = $validated['stock'] - $book->stock;
        $validated['available'] = max(0, $book->available + $stockDiff);

        // Remove remove_cover from validated data before update
        unset($validated['remove_cover']);

        $book->update($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diupdate!');
    }

    /**
     * Remove the specified book from database.
     */
    public function destroy(Book $book)
    {
        // Check if book has active borrowings
        $activeBorrowings = $book->borrowings()->whereNull('returned_at')->count();
        
        if ($activeBorrowings > 0) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Buku tidak dapat dihapus karena masih ada peminjaman aktif!');
        }

        // Delete cover image if exists
        if ($book->cover_image && file_exists(public_path('img/covers/' . $book->cover_image))) {
            unlink(public_path('img/covers/' . $book->cover_image));
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }
}
