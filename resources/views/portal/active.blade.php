@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Pesanan Aktif</h1>
    <p>Pantau status pesanan laundry Anda yang sedang diproses saat ini.</p>
</section>

<div class="portal-order-card table-card">
    <table class="portal-order-table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Layanan</th>
                <th>Total Harga</th>
                <th>Status Cucian</th>
                <th>Status Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activeOrders as $order)
                <tr>
                    <td><strong>ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $order->service->name ?? '-' }}</td>
                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="portal-status-badge">
                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="portal-payment-badge {{ $order->payment_status === 'dibayar' ? 'paid' : 'unpaid' }}">
                            {{ ucwords(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('portal.orders.show', $order) }}" class="portal-detail-link">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row" style="text-align: center; padding: 40px !important; color: #64748b;">
                        Belum ada order laundry yang sedang aktif berjalan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection