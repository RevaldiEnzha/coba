@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="logo-box">L</div>

    <h1 class="auth-title">Sistem Laundry</h1>
    <p class="auth-subtitle">Masuk ke akun Anda</p>

    <form method="POST" action="#">
        @csrf

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username">
        </div>

        <div class="form-group">
            <label>Kata Sandi</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan kata sandi">
        </div>

        <a href="#" class="forgot-link">Lupa Kata Sandi?</a>

        <button type="submit" class="btn btn-primary">Masuk</button>

        <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
    </form>
</div>
@endsection
