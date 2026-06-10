@extends('layouts.app')

@section('content')

<section class="page-header">
    <h1>Edit Pelanggan</h1>
    <p>Perbarui data pelanggan laundry</p>
</section>

<div class="card form-card">

    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $customer->user->name) }}">

                @error('name')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    value="{{ old('username', $customer->user->username) }}">

                @error('username')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email', $customer->user->email) }}">

                @error('email')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>No HP</label>
                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    value="{{ old('phone', $customer->phone) }}">

                @error('phone')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea
                name="address"
                class="form-control">{{ old('address', $customer->address) }}</textarea>
            @error('address')
                <small style="color:red;">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                class="form-control">

            <small>
                Kosongkan jika tidak ingin mengubah password
            </small>

            @error('password')
                <small style="color:red;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <div class="form-group">
            <label>Konfirmasi Password Baru</label>

            <input
                type="password"
                name="password_confirmation"
                class="form-control">

            <small>
                Harus sama dengan Password Baru
            </small>
        </div>

        <div class="action-buttons">

            <a href="{{ route('customers.index') }}"
               class="btn-secondary">
                Kembali
            </a>

            <button type="submit" class="btn-primary">
                Simpan
            </button>

        </div>

    </form>

</div>

@endsection
