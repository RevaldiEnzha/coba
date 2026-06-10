@extends('layouts.app')

@section('content')

<section class="page-header">
    <h1>Manajemen Pelanggan</h1>
    <p>Kelola seluruh data pelanggan laundry</p>
</section>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">

    <div class="table-toolbar">

        <form method="GET">
            <input
                type="text"
                name="search"
                placeholder="Cari pelanggan..."
                value="{{ $search }}"
                class="search-input">

                <button type="submit" class="btn-search">
                    Cari
                </button>
        </form>

        <a href="{{ route('customers.create') }}"
           class="btn-primary">
            + Tambah Pelanggan
        </a>

    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Poin</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($customers as $customer)

            <tr>
                <td>{{ $customer->user->name }}</td>
                <td>{{ $customer->user->username }}</td>
                <td>{{ $customer->user->email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->points_balance }}</td>

                <td>
                    <div class="action-cell">

                        <a
                            href="{{ route('customers.edit',$customer) }}"
                            class="btn-edit">
                            Edit
                        </a>

                        <form
                            method="POST"
                            action="{{ route('customers.destroy',$customer) }}">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn-danger"
                                onclick="return confirm('Hapus pelanggan?')">
                                Hapus
                            </button>

                        </form>

                    </div>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="6">
                    Tidak ada data pelanggan.
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

    <div style="margin-top:20px">
        {{ $customers->links() }}
    </div>

</div>

@endsection