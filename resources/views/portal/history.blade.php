@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Riwayat Pesanan</h1>
    <p>Daftar seluruh pesanan laundry Anda yang sudah selesai atau dibatalkan.</p>
</section>

<div class="portal-order-card">
    <table class="portal-order-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Order</th>
                <th>Layanan</th>
                <th>Status</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($completedOrders as $order)
                <tr>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td>ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $order->service->name ?? '-' }}</td>
                    <td>
                        <span class="portal-status-badge {{ $order->status == 'selesai' ? 'badge-green' : '' }}">
                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('portal.orders.show', $order) }}" class="portal-detail-link">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row" style="text-align: center; color: #64748b; padding: 20px;">
                        Belum ada riwayat pesanan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection