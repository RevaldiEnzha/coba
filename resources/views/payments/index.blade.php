@extends('layouts.app')

@section('content')

<section class="page-header">
    <h1>Manajemen Pembayaran</h1>
    <p>Kelola pembayaran transaksi laundry</p>
</section>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="table-card">

    <table class="data-table">

        <thead>
            <tr>
                <th>No Order</th>
                <th>Pelanggan</th>
                <th>Total Tagihan</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($orders as $order)

                <tr>

                    <td>
                        {{ $order->order_code }}
                    </td>

                    <td>
                        {{ $order->customer->user->name }}
                    </td>

                    <td>
                        Rp {{ number_format($order->total_price,0,',','.') }}
                    </td>

                    <td>

                        @if($order->payment_status == 'belum_bayar')

                            <span class="badge badge-orange">
                                Belum Lunas
                            </span>

                        @else

                            <span class="badge badge-green">
                                Lunas
                            </span>

                        @endif

                    </td>

                    <td>
                        
                        <a href="#">
                            Bayar
                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5">
                        Tidak ada transaksi yang perlu dibayar.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection