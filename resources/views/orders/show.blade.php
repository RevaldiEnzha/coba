@extends('layouts.app')

@section('content')
<section class="page-header">
    <h1>Detail Transaksi</h1>
    <p>Rincian order laundry pelanggan</p>
</section>

<div class="detail-grid">
    <div class="detail-card">
        <h3>Informasi Order</h3>

        <div class="detail-row">
            <span>Kode Order</span>
            <strong>{{ $order->order_code }}</strong>
        </div>

        <div class="detail-row">
            <span>Pelanggan</span>
            <strong>{{ $order->customer->user->name ?? '-' }}</strong>
        </div>

        <div class="detail-row">
            <span>No. HP</span>
            <strong>{{ $order->customer->phone ?? '-' }}</strong>
        </div>

        <div class="detail-row">
            <span>Layanan</span>
            <strong>{{ $order->service->name ?? '-' }}</strong>
        </div>

        <div class="detail-row">
            <span>Status Cucian</span>
            <strong>{{ str_replace('_', ' ', $order->status) }}</strong>
        </div>

        <div class="detail-row">
            <span>Status Pembayaran</span>
            <strong>{{ str_replace('_', ' ', $order->payment_status) }}</strong>
        </div>
    </div>

    <div class="detail-card">
        <h3>Rincian Invoice</h3>

        <div class="detail-row">
            <span>Subtotal</span>
            <strong>Rp {{ number_format($order->invoice->subtotal ?? 0, 0, ',', '.') }}</strong>
        </div>

        <div class="detail-row">
            <span>Biaya Antar</span>
            <strong>Rp {{ number_format($order->invoice->delivery_fee ?? 0, 0, ',', '.') }}</strong>
        </div>

        <div class="detail-row">
            <span>Diskon Poin</span>
            <strong>Rp {{ number_format($order->invoice->point_discount ?? 0, 0, ',', '.') }}</strong>
        </div>

        <div class="detail-row total">
            <span>Total</span>
            <strong>Rp {{ number_format($order->invoice->total_amount ?? 0, 0, ',', '.') }}</strong>
        </div>
    </div>
</div>

<div class="table-card" style="margin-top:24px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->statusHistories as $history)
                <tr>
                    <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                    <td>{{ str_replace('_', ' ', $history->status) }}</td>
                    <td>{{ $history->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="form-actions" style="margin-top:24px;">
    <a href="{{ route('orders.index') }}" class="btn-cancel">Kembali</a>
</div>
@endsection
