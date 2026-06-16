@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Detail Order</h1>
    <p>Informasi status dan invoice cucian Anda</p>
</section>

<div class="portal-detail-grid">
    <div class="portal-profile-card">
        <h3>Informasi Order</h3>

        <div class="portal-profile-row">
            <span>No. Order</span>
            <strong>ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</strong>
        </div>

        <div class="portal-profile-row">
            <span>Layanan</span>
            <strong>{{ $order->service->name ?? '-' }}</strong>
        </div>

        <div class="portal-profile-row">
            <span>Status Cucian</span>
            <strong>{{ ucwords(str_replace('_', ' ', $order->status)) }}</strong>
        </div>

        <div class="portal-profile-row">
            <span>Status Pembayaran</span>
            <strong>{{ ucwords(str_replace('_', ' ', $order->payment_status)) }}</strong>
        </div>
    </div>

    <div class="portal-profile-card">
        <h3>Rincian Invoice</h3>

        <div class="portal-profile-row">
            <span>Subtotal</span>
            <strong>Rp {{ number_format($order->invoice->subtotal ?? 0, 0, ',', '.') }}</strong>
        </div>

        <div class="portal-profile-row">
            <span>Biaya Antar</span>
            <strong>Rp {{ number_format($order->invoice->delivery_fee ?? 0, 0, ',', '.') }}</strong>
        </div>

        <div class="portal-profile-row">
            <span>Diskon Poin</span>
            <strong>Rp {{ number_format($order->invoice->point_discount ?? 0, 0, ',', '.') }}</strong>
        </div>

        <div class="portal-profile-row total">
            <span>Total</span>
            <strong>Rp {{ number_format($order->invoice->total_amount ?? 0, 0, ',', '.') }}</strong>
        </div>
    </div>
</div>

<div class="portal-order-card">
    <h3>Riwayat Status</h3>

    <table class="portal-order-table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>

        <tbody>
            @forelse($order->statusHistories as $history)
                <tr>
                    <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $history->status)) }}</td>
                    <td>{{ $history->note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-row">Belum ada riwayat status.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="portal-actions">
    <a href="{{ route('portal.dashboard') }}" class="btn-cancel">Kembali</a>
</div>
@endsection
