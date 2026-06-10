@extends('layouts.app')

@section('content')
<section class="page-header">
    <h1>Manajemen Transaksi</h1>
    <p>Kelola transaksi laundry pelanggan</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="toolbar-card">
    <div></div>

    <a href="{{ route('orders.create') }}" class="btn-internal-primary">
        + Buat Transaksi Baru
    </a>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Kode Order</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Total</th>
                <th>Status Cucian</th>
                <th>Status Bayar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->customer->user->name ?? '-' }}</td>
                    <td>{{ $order->service->name ?? '-' }}</td>
                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-blue">{{ str_replace('_', ' ', $order->status) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $order->payment_status === 'dibayar' ? 'badge-green' : 'badge-orange' }}">
                            {{ str_replace('_', ' ', $order->payment_status) }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="action-link">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Belum ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
