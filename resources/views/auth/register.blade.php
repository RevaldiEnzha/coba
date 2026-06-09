@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="logo-box">L</div>

    <h1 class="auth-title">Sistem Laundry</h1>
    <p class="auth-subtitle">Buat akun anda</p>

    <form method="POST" action="{{ route('register.process') }}">
    @csrf

    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" class="form-control" placeholder="Masukkan nama" value="{{ old('name') }}">
        @error('name') <small style="color:red;">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}">
        @error('username') <small style="color:red;">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label>Nomor Ponsel</label>
        <input type="text" name="phone" class="form-control" placeholder="Masukkan nomor" value="{{ old('phone') }}">
        @error('phone') <small style="color:red;">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" placeholder="Masukkan email" value="{{ old('email') }}">
        @error('email') <small style="color:red;">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label>Alamat</label>
        <textarea name="address" class="form-control" placeholder="Masukkan alamat">{{ old('address') }}</textarea>
        @error('address') <small style="color:red;">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label>Kata Sandi</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan kata sandi">
        @error('password') <small style="color:red;">{{ $message }}</small> @enderror
    </div>

    <button type="submit" class="btn btn-primary">Register</button>

    <div class="auth-link">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
    </div>
</form>
</div>
@endsection
