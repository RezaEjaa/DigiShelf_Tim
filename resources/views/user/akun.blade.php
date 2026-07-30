@extends('layouts.app-navbar')
@section('title', 'Akun - Digishelf')

@section('content')
<style>
.account-container{max-width:620px;margin:0 auto;background:white;border-radius:20px;padding:50px;box-shadow:0 10px 40px rgba(0,0,0,0.1);text-align:center}
.profile-photo-wrap{position:relative;width:130px;height:130px;margin:0 auto 30px;cursor:pointer}
.profile-photo{width:130px;height:130px;border-radius:50%;background:linear-gradient(135deg,var(--wood-medium),var(--wood-dark));display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(0,0,0,0.15);overflow:hidden}
.profile-photo img{width:100%;height:100%;object-fit:cover}
.profile-initials{font-size:52px;font-weight:700;color:white;font-family:'Crimson Pro',serif}
.photo-overlay{position:absolute;inset:0;border-radius:50%;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;color:white;font-size:1.2rem}
.profile-photo-wrap:hover .photo-overlay{opacity:1}
.profile-info{margin-bottom:35px}
.info-item{background:var(--cream);padding:18px 20px;border-radius:12px;margin-bottom:12px;text-align:left;display:flex;justify-content:space-between;align-items:center}
.info-label{font-weight:600;color:var(--wood-dark);font-size:0.85rem;text-transform:uppercase;letter-spacing:0.5px}
.info-value{font-size:1rem;color:var(--text-dark);font-weight:500}
.google-badge{display:inline-flex;align-items:center;gap:8px;background:#F0F7FF;border:1px solid #C5D9F1;padding:10px 16px;border-radius:10px;font-size:0.85rem;color:#333;width:100%}
.google-badge svg{flex-shrink:0}
.google-badge-text small{display:block;color:#888;font-size:0.75rem;margin-top:2px}
.action-buttons{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn{padding:13px 30px;border-radius:12px;border:none;cursor:pointer;font-weight:600;font-size:0.95rem;transition:all 0.3s;font-family:'Poppins',sans-serif;display:inline-flex;align-items:center;gap:8px}
.btn-edit{background:linear-gradient(135deg,var(--wood-medium),var(--wood-dark));color:white}
.btn-edit:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(141,110,99,0.3)}
.btn-delete{background:#C62828;color:white}
.btn-delete:hover{background:#B71C1C;transform:translateY(-2px)}
.modal{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:9999;justify-content:center;align-items:center;padding:20px}
.modal.active{display:flex}
.modal-content{background:white;border-radius:20px;padding:40px;max-width:480px;width:100%;position:relative;animation:slideDown 0.3s}
@keyframes slideDown{from{opacity:0;transform:translateY(-40px)}to{opacity:1;transform:translateY(0)}}
.modal-close{position:absolute;top:15px;right:15px;background:none;border:none;font-size:28px;cursor:pointer;color:#999;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;transition:all 0.2s}
.modal-close:hover{background:#f0f0f0;color:var(--wood-dark);transform:rotate(90deg)}
.modal-title{font-family:'Crimson Pro',serif;font-size:1.7rem;color:var(--wood-dark);margin-bottom:22px;text-align:center}
.form-group{margin-bottom:18px;text-align:left}
.form-group label{display:block;margin-bottom:7px;font-weight:600;color:var(--text-dark);font-size:0.88rem}
.form-group input{width:100%;padding:13px 16px;border:2px solid var(--cream);border-radius:10px;font-size:0.95rem;transition:all 0.3s;font-family:'Poppins',sans-serif}
.form-group input:focus{outline:none;border-color:var(--wood-medium);box-shadow:0 0 0 3px rgba(141,110,99,0.1)}
.form-group input:disabled{background:#f5f5f5;color:#aaa;cursor:not-allowed}
.btn-primary{background:linear-gradient(135deg,var(--wood-medium),var(--wood-dark));color:white;width:100%;padding:14px;justify-content:center}
.btn-primary:hover{transform:translateY(-2px)}
.alert{padding:14px 18px;border-radius:10px;margin-bottom:25px;text-align:left;font-size:0.9rem}
.alert-success{background:#E8F5E9;color:#2E7D32;border-left:4px solid #2E7D32}
.alert-danger{background:#FFEBEE;color:#C62828;border-left:4px solid #C62828}
.google-info-modal{background:#F0F7FF;border:1px solid #C5D9F1;border-radius:10px;padding:14px 16px;margin-bottom:18px;display:flex;align-items:center;gap:12px;font-size:0.85rem;color:#444}
.google-info-modal small{display:block;color:#888;margin-top:2px}
</style>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="account-container">
    {{-- FOTO PROFIL --}}
    <div class="profile-photo-wrap" onclick="openEditModal()">
        <div class="profile-photo">
            @if($user->profile_photo)
                <img src="{{ asset('img/profile_photos/' . $user->profile_photo) }}" alt="{{ $user->name }}">
            @else
                <div class="profile-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
        </div>
        <div class="photo-overlay"><i class="fas fa-camera"></i></div>
    </div>

    <div class="profile-info">
        <div class="info-item">
            <span class="info-label">Nama</span>
            <span class="info-value">{{ $user->name }}</span>
        </div>

        @if($user->google_id)
            {{-- Google user: tampilkan badge Google --}}
            <div class="info-item" style="flex-direction:column; align-items:flex-start; gap:10px;">
                <span class="info-label">Email & Password</span>
                <div class="google-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    <div>
                        <strong>Akun Google</strong>
                        <small>{{ $user->email }} · Email & password dikelola oleh Google</small>
                    </div>
                </div>
            </div>
        @else
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Password</span>
                <span class="info-value" style="font-size:1.4rem;letter-spacing:3px;color:#ccc;">••••••••</span>
            </div>
        @endif
    </div>

    <div class="action-buttons">
        <button onclick="openEditModal()" class="btn btn-edit">
            <i class="fas fa-edit"></i> Edit Akun
        </button>
        <button onclick="confirmDelete()" class="btn btn-delete">
            <i class="fas fa-trash-alt"></i> Hapus Akun
        </button>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal" id="editModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeEditModal()">&times;</button>
        <h2 class="modal-title">Edit Akun</h2>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Upload foto profil --}}
            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" style="padding:10px 14px; border-style:dashed;">
            </div>

            {{-- Nama selalu bisa diedit --}}
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<p style="color:#C62828;font-size:0.82rem;margin-top:4px">{{ $message }}</p>@enderror
            </div>

            @if($user->google_id)
                {{-- Google user: tampilkan badge saja, TIDAK ADA field email & password --}}
                <div class="google-info-modal">
                    <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    <div>
                        <strong>Akun Google</strong>
                        <small>{{ $user->email }} · Email & password dikelola oleh Google</small>
                    </div>
                </div>
            @else
                {{-- User biasa: email & password bisa diedit --}}
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<p style="color:#C62828;font-size:0.82rem;margin-top:4px">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Password Baru <small style="color:#aaa;font-weight:400">(kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter">
                    @error('password')<p style="color:#C62828;font-size:0.82rem;margin-top:4px">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru">
                </div>
            @endif

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<form id="deleteForm" action="{{ route('profile.delete') }}" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

<script>
function openEditModal() {
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Akun?',
        html: '<p style="color:#666;margin-top:10px">Tindakan ini akan menghapus akun Anda secara <strong>permanen</strong>.</p><p style="color:#C62828;font-weight:600;margin-top:12px">Tidak dapat dibatalkan!</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C62828',
        cancelButtonColor: '#E0E0E0',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => { if (r.isConfirmed) document.getElementById('deleteForm').submit(); });
}
document.getElementById('editModal').addEventListener('click', e => { if (e.target === document.getElementById('editModal')) closeEditModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });
@if($errors->any()) openEditModal(); @endif
</script>
@endsection