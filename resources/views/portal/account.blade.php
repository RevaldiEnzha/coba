@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Manajemen Akun</h1>
    <p>Kelola informasi profil pribadi dan amankan kata sandi akun Anda.</p>
</section>

@if(session('success'))
    <div class="alert-success" style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #dcfce7; color: #166534;">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #e0f2fe; color: #0369a1; font-weight: 600;">
        {{ session('info') }}
    </div>
@endif

<div class="portal-profile-card" style="max-width: 650px; background: #ffffff; border-radius: 14px; padding: 26px; border: 1px solid #e2e8f0;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px; font-weight: 800;">Informasi Pengguna & Keamanan</h3>
    
    <form method="POST" action="{{ route('portal.account.update') }}" class="customer-modal-form">
        @csrf
        
        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label>Nama Pelanggan</label>
            <input type="text" value="{{ $user->name }}" readonly style="background: #f1f5f9; color: #64748b; cursor: not-allowed;">
        </div>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label>Email Utama</label>
            <input type="email" value="{{ $user->email }}" readonly style="background: #f1f5f9; color: #64748b; cursor: not-allowed;">
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
        
        <h4 style="margin: 0 0 10px 0; color: #0ea5e9; font-size: 15px; font-weight: 700;">Ganti Kata Sandi Akun</h4>
        <p style="margin: 0 0 16px 0; color: #64748b; font-size: 13px; line-height: 1.4;">
            *Isi kolom di bawah ini jika Anda ingin mengganti password. Jika tidak ingin mengganti password, Anda cukup mengosongkan kolom password baru.
        </p>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label style="font-weight: 700;">Password Saat Ini (Password Lama) <span style="color: #ef4444;">*</span></label>
            <input type="password" name="password_sekarang" placeholder="Masukkan password Anda saat ini untuk konfirmasi">
            @error('password_sekarang') 
                <small class="error-text" style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</small> 
            @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label>Password Baru</label>
            <input type="password" name="password_baru" placeholder="Masukkan password baru (minimal 8 karakter)">
            @error('password_baru') 
                <small class="error-text" style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</small> 
            @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 20px;">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="password_baru_confirmation" placeholder="Ulangi pengetikan password baru Anda">
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="modal-submit-btn" style="width: auto; padding: 0 24px; min-width: 160px; height: 44px; font-weight: 700;">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection