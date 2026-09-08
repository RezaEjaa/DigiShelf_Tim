<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowingController;
use App\Http\Controllers\Admin\BorrowingHistoryController;
use App\Http\Controllers\Admin\PetugasController as AdminPetugasController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Petugas\PetugasController;
use App\Http\Controllers\Petugas\QrVerificationController as PetugasQrController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\BackupController;
// ── Public ────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'));
Route::get('/scan-peminjaman/{qr_code}', [BorrowController::class, 'scan'])->name('borrow.scan');

// ── Auth ──────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── User ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard',    [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/koleksi-buku', [UserController::class, 'books'])->name('user.books');
    Route::get('/riwayat',      [UserController::class, 'history'])->name('user.history');
    Route::get('/favorit',      [UserController::class, 'favorites'])->name('user.favorites');
    Route::get('/akun',         [UserController::class, 'account'])->name('user.account');
    Route::put('/profile/update',   [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete',[UserController::class, 'destroy'])->name('profile.delete');
    Route::post('/favorit/{id}/add',   [FavoriteController::class, 'add'])->name('favorite.add');
    Route::post('/favorit/{id}/remove',[FavoriteController::class, 'remove'])->name('favorite.remove');

    // ── Sistem Peminjaman QR (BARU) ──────────────────────────
    Route::get('/form-peminjaman',       [BorrowController::class, 'form'])->name('borrow.form');
    Route::post('/form-peminjaman',      [BorrowController::class, 'store'])->name('borrow.store');
    Route::get('/peminjaman',            [BorrowController::class, 'index'])->name('user.borrowings');
    Route::get('/peminjaman/{id}',       [BorrowController::class, 'detail'])->name('user.borrowings.detail');
});

// ── Petugas ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');

    // Verifikasi QR / Kode
    Route::get('/verify-qr',          [PetugasQrController::class, 'index'])->name('verify-qr.index');
    Route::post('/verify-qr',         [PetugasQrController::class, 'verify'])->name('verify-qr.verify');
    Route::post('/verify-qr/confirm', [PetugasQrController::class, 'confirm'])->name('verify-qr.confirm');
    Route::post('/verify-qr/return',  [PetugasQrController::class, 'returnBook'])->name('verify-qr.return');

    // Kelola Peminjaman
    Route::get('/borrowings',         [PetugasQrController::class, 'activeList'])->name('borrowings.index');
    Route::get('/borrowings/history', [PetugasQrController::class, 'history'])->name('borrowings.history');
});

// ── Admin ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Buku
    Route::get('/books',              [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create',       [BookController::class, 'create'])->name('books.create');
    Route::post('/books',             [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit',  [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}',       [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}',    [BookController::class, 'destroy'])->name('books.destroy');

    // Pengguna
    Route::get('/users',              [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create',       [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users',             [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',       [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',    [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Petugas
    Route::get('/petugas',               [AdminPetugasController::class, 'index'])->name('petugas.index');
    Route::get('/petugas/create',        [AdminPetugasController::class, 'create'])->name('petugas.create');
    Route::post('/petugas',              [AdminPetugasController::class, 'store'])->name('petugas.store');
    Route::get('/petugas/{petuga}/edit', [AdminPetugasController::class, 'edit'])->name('petugas.edit');
    Route::put('/petugas/{petuga}',      [AdminPetugasController::class, 'update'])->name('petugas.update');
    Route::delete('/petugas/{petuga}',   [AdminPetugasController::class, 'destroy'])->name('petugas.destroy');

    // Riwayat Peminjaman
    Route::get('/borrowings/history', [BorrowingHistoryController::class, 'index'])->name('borrowings.history');

    // Backup Database
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
