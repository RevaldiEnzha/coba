@extends('layouts.app')

@section('content')
<section class="page-header">
    <h1>Edit Data Pelanggan</h1>
    <p>Perbarui informasi profil dan kredensial login pelanggan</p>
</section>

<div class="form-card" style="max-width: 600px; margin: 0 auto; padding: 24px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #334155;">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $customer->user->name) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 14px;">
            @error('name') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #334155;">Username</label>
            <input type="text" name="username" value="{{ old('username', $customer->user->username) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 14px;">
            @error('username') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #334155;">Email Utama</label>
            <input type="email" name="email" value="{{ old('email', $customer->user->email) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 14px;">
            @error('email') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #334155;">Nomor WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 14px;">
            @error('phone') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #334155;">Alamat Lengkap</label>
            <textarea name="address" required style="width: 100%; min-height: 80px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-size: 14px; resize: none;">{{ old('address', $customer->address) }}</textarea>
            @error('address') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 24px 0;">

        <div class="form-group" style="margin-bottom: 24px;">
            <label style="font-weight: 600; font-size: 13px; color: #334155;">Ganti Password <span style="color: #94a3b8; font-weight: 400;">(Opsional)</span></label>
            <p style="font-size: 11px; color: #64748b; margin-top: 2px; margin-bottom: 8px;">Biarkan kosong jika tidak ingin mengubah sandi pelanggan saat ini.</p>
            <input type="password" name="password" placeholder="Masukkan password baru..." style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 14px;">
            @error('password') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('customers.index') }}" style="text-decoration: none; padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; background: #f8fafc;">Batal</a>
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection