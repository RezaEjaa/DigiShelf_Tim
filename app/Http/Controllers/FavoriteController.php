<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function add(int $bookId)
    {
        $user = Auth::user();

        // Cek duplikat
        $exists = Favorite::where('user_id', $user->id)
                          ->where('book_id', $bookId)
                          ->exists();

        if (!$exists) {
            Favorite::create([
                'user_id' => $user->id,
                'book_id' => $bookId,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function remove(int $bookId)
    {
        $user = Auth::user();

        Favorite::where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->delete();

        return response()->json(['success' => true]);
    }
}