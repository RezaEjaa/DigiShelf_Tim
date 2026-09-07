# File yang Dihapus

File-file berikut sudah dihapus karena tidak digunakan lagi setelah implementasi role Petugas:

## Controllers yang Dihapus:
1. `app/Http/Controllers/Admin/QrVerificationController.php` 
   - Fungsi sudah dipindah ke `app/Http/Controllers/Petugas QrVerificationController.php`

2. `app/Http/Controllers/Admin/BorrowingController.php`
   - Controller lama yang tidak digunakan

## Views yang Dihapus:
1. `resources/views/admin/borrowings/verify-qr.blade.php`
   - Sudah dipindah ke `resources/views/petugas/borrowings/verify-qr.blade.php`

2. `resources/views/admin/borrowings/index.blade.php`
   - Sudah dipindah ke `resources/views/petugas/borrowings/index.blade.php`

3. `resources/views/admin/borrowings/history.blade.php`
   - Diganti dengan `resources/views/admin/borrowings/all-history.blade.php`

## File yang Masih Ada

### Admin:
- `app/Http/Controllers/Admin/AdminDashboardController.php`
- `app/Http/Controllers/Admin/BookController.php`
- `app/Http/Controllers/Admin/UserController.php`
- `app/Http/Controllers/Admin/PetugasController.php` (BARU)
- `app/Http/Controllers/Admin/BorrowingHistoryController.php` (BARU)

### Petugas:
- `app/Http/Controllers/Petugas/PetugasController.php` (BARU)
- `app/Http/Controllers/Petugas/QrVerificationController.php` (BARU)

### Views Admin:
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/books/*`
- `resources/views/admin/users/*`
- `resources/views/admin/petugas/*` (BARU)
- `resources/views/admin/borrowings/all-history.blade.php` (BARU)

### Views Petugas:
- `resources/views/petugas/dashboard.blade.php` (BARU)
- `resources/views/petugas/borrowings/index.blade.php` (BARU)
- `resources/views/petugas/borrowings/verify-qr.blade.php` (BARU)
- `resources/views/petugas/borrowings/history.blade.php` (BARU)