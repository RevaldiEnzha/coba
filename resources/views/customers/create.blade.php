@extends('layouts.app')

@section('content')

<section class="page-header">
    <h1>Tambah Pelanggan</h1>
    <p>Tambahkan data pelanggan baru</p>
</section>

<div class="card form-card">

    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    value="{{ old('username') }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label>No HP</label>
                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    value="{{ old('phone') }}">
            </div>

        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea
                name="address"
                class="form-control">{{ old('address') }}</textarea>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                class="form-control">
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