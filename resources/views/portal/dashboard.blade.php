@extends('layouts.portal')

@section('content')

<section class="page-header">

    <h1>
        Selamat Datang,
        {{ auth()->user()->name }}
    </h1>

    <p>
        Pantau status cucian dan poin loyalitas Anda.
    </p>

</section>

<div class="stats-grid">

    <div class="stat-card">

        <h2>
            {{ $stats['active_orders'] }}
        </h2>

        <p>Pesanan Aktif</p>

    </div>

    <div class="stat-card">

        <h2>
            {{ $stats['completed_orders'] }}
        </h2>

        <p>Pesanan Selesai</p>

    </div>

    <div class="stat-card">

        <h2>
            {{ $stats['points'] }}
        </h2>

        <p>Poin Loyalitas</p>

    </div>

</div>

<div class="chart-card">

    <h3>Informasi Akun</h3>

    <div class="detail-row">
        <span>Nama</span>
        <strong>
            {{ auth()->user()->name }}
        </strong>
    </div>

    <div class="detail-row">
        <span>Email</span>
        <strong>
            {{ auth()->user()->email }}
        </strong>
    </div>

    {{-- <div class="detail-row">
        <span>No HP</span>
        <strong>
            {{ $customer->phone }}
        </strong>
    </div> --}}

    {{-- <div class="detail-row">
        <span>Alamat</span>
        <strong>
            {{ $customer->address }}
        </strong>
    </div> --}}

</div>

@endsection
