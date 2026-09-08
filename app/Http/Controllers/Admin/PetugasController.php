<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = User::where('role', 'petugas')
            ->withCount('processedBorrowings as processed_count')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('admin.petugas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'petugas';

        User::create($validated);

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil ditambahkan');
    }

    public function edit(User $petuga)
    {
        if ($petuga->role !== 'petugas') {
            return back()->with('error', 'Pengguna ini bukan petugas');
        }

        return view('admin.petugas.edit', compact('petuga'));
    }

    public function update(Request $request, User $petuga)
    {
        if ($petuga->role !== 'petugas') {
            return back()->with('error', 'Pengguna ini bukan petugas');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $petuga->id,
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $petuga->update($validated);

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil diupdate');
    }

    public function destroy(User $petuga)
    {
        if ($petuga->role !== 'petugas') {
            return back()->with('error', 'Pengguna ini bukan petugas');
        }

        $petuga->delete();

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dihapus');
    }
}
