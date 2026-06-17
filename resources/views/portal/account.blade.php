@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Manajemen Akun</h1>
    <p>Kelola informasi profil pribadi dan alamat Anda.</p>
</section>

@if(session('success'))
    <div class="alert-success" style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #dcfce7; color: #166534;">
        {{ session('success') }}
    </div>
@endif

<div class="portal-profile-card" style="max-width: 650px; background: #ffffff; border-radius: 14px; padding: 26px; border: 1px solid #e2e8f0;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px; font-weight: 800;">Profil & Kontak</h3>
    
    <form method="POST" action="{{ route('portal.account.update') }}" class="customer-modal-form">
        @csrf
        
        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label style="font-weight: 700;">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label style="font-weight: 700;">Email Utama</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label style="font-weight: 700;">Nomor WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $user->customer->phone) }}" required>
            @error('phone') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label style="font-weight: 700;">Alamat Utama</label>
            <textarea name="address" required style="width: 100%; min-height: 80px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-size: 14px; resize: none;">{{ old('address', $user->customer->address) }}</textarea>
            @error('address') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
        
        <h4 style="margin: 0 0 10px 0; color: #0ea5e9; font-size: 15px; font-weight: 700;">Ganti Kata Sandi (Opsional)</h4>
        <p style="margin: 0 0 16px 0; color: #64748b; font-size: 13px; line-height: 1.4;">
            *Biarkan semua kolom password di bawah ini <b>KOSONG</b> jika Anda hanya ingin menyimpan perubahan profil (nama, no HP, alamat) tanpa mengubah password.
        </p>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label>Password Saat Ini (Konfirmasi Lama)</label>
            <input type="password" name="password_sekarang" placeholder="Masukkan password saat ini jika ingin mengganti sandi">
            @error('password_sekarang') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 14px;">
            <label>Password Baru</label>
            <input type="password" name="password_baru" placeholder="Minimal 8 karakter">
            @error('password_baru') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-bottom: 20px;">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="password_baru_confirmation" placeholder="Ketik ulang password baru">
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="modal-submit-btn" style="width: auto; padding: 0 24px; min-width: 160px; height: 44px; font-weight: 700;">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection