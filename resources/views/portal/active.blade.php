@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Pesanan Aktif</h1>
    <p>Pantau status pesanan laundry Anda yang sedang diproses saat ini.</p>
</section>

@if(session('success'))
    <div class="alert-success" style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-error" style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
        {{ session('error') }}
    </div>
@endif

@if($pendingPickups->count() > 0)
    <h3 style="color: #f59e0b; margin-bottom: 12px; font-size: 16px;">Menunggu Penjemputan Kurir</h3>
    <div class="portal-cards-container" style="margin-bottom: 30px;">
        @foreach($pendingPickups as $pickup)
            <div class="portal-order-card" style="border-left: 4px solid #f59e0b; padding: 16px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <strong style="color: #334155;">ID Jemput: JMP-{{ str_pad($pickup->id, 3, '0', STR_PAD_LEFT) }}</strong>
                    <span style="background: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                        {{ ucwords(str_replace('_', ' ', $pickup->status)) }}
                    </span>
                </div>
                <p style="margin: 0; color: #64748b; font-size: 13px;">
                    <strong>Layanan:</strong> {{ $pickup->service->name ?? '-' }}<br>
                    <strong>Alamat:</strong> {{ $pickup->address }}<br>
                    <strong>Jadwal:</strong> {{ $pickup->scheduled_at ? \Carbon\Carbon::parse($pickup->scheduled_at)->format('d M Y H:i') : 'Secepatnya' }}
                </p>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <span style="font-size: 11px; color: #94a3b8; font-style: italic; line-height: 1.4;">
                        *Cucian Anda belum ditimbang. Transaksi resmi akan muncul setelah kurir membawa cucian ke outlet.
                    </span>

                    @if($pickup->status === 'menunggu_konfirmasi')
                        <form method="POST" action="{{ route('portal.pickups.cancel', $pickup) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan jemput ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Batalkan Jemput
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

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