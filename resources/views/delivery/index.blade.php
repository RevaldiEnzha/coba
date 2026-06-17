@extends('layouts.app')

@section('content')
<section class="page-header delivery-page-header">
    <h1>Manajemen Jemput & Antar</h1>
    <p>Kelola permintaan penjemputan awal dan pengantaran cucian selesai.</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert-error">
        {{ $errors->first() }}
    </div>
@endif

<h2 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; color: #334155;">Permintaan Jemput (Pickup)</h2>
<div class="delivery-table-card">
    <table class="delivery-table">
        <thead>
            <tr>
                <th>ID Jemput</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Alamat Jemput</th>
                <th>Catatan</th>
                <th>Jadwal</th>
                <th>Status</th>
                <th>Konfirmasi Transaksi</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pickups as $request)
                @php
                    $statusLabel = match($request->status) {
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $request->status,
                    };

                    $statusClass = match($request->status) {
                        'menunggu_konfirmasi' => 'delivery-waiting',
                        'diproses' => 'delivery-process',
                        'selesai' => 'delivery-done',
                        'dibatalkan' => 'delivery-cancelled',
                        default => 'delivery-waiting',
                    };
                @endphp
                <tr>
                    <td>JMP-{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $request->customer->user->name ?? '-' }}</td>
                    <td>{{ $request->service->name ?? '-' }}</td>
                    <td>{{ $request->address }}</td>
                    <td>{{ $request->note ?? '-' }}</td>
                    <td>{{ $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at)->format('d M Y H:i') : '-' }}</td>
                    <td><span class="delivery-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td>
                        @if($request->laundry_order_id)
                            <a href="{{ route('orders.show', $request->laundry_order_id) }}" class="delivery-order-link">Lihat Transaksi</a>
                        @elseif($request->status === 'menunggu_konfirmasi')
                            <form method="POST" action="{{ route('delivery.confirm', $request) }}" class="delivery-confirm-form">
                                @csrf
                                <input type="number" step="0.1" min="0.1" name="amount" placeholder="{{ ($request->service->type ?? 'kiloan') === 'kiloan' ? 'Berat kg' : 'Jumlah item' }}" required>
                                <button type="submit">Buat Transaksi</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('delivery.update', $request) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="delivery-status-select" onchange="this.form.submit()">
                                <option value="menunggu_konfirmasi" {{ $request->status === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="diproses" {{ $request->status === 'diproses' ? 'selected' : '' }}>Diproses (Kurir Jalan)</option>
                                <option value="selesai" {{ $request->status === 'selesai' ? 'selected' : '' }}>Selesai Dijemput</option>
                                <option value="dibatalkan" {{ $request->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty-row">Belum ada permintaan jemput.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2 style="margin-top: 36px; margin-bottom: 12px; font-size: 18px; color: #334155;">Permintaan Antar (Delivery)</h2>
<div class="delivery-table-card">
    <table class="delivery-table">
        <thead>
            <tr>
                <th>ID Antar</th>
                <th>Order Terkait</th>
                <th>Pelanggan</th>
                <th>Alamat Tujuan</th>
                <th>Jarak & Biaya</th>
                <th>Status</th>
                <th>Aksi / Update Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deliveries as $delivery)
                @php
                    $statusLabel = match($delivery->status) {
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $delivery->status,
                    };

                    $statusClass = match($delivery->status) {
                        'menunggu_konfirmasi' => 'delivery-waiting',
                        'diproses' => 'delivery-process',
                        'selesai' => 'delivery-done',
                        'dibatalkan' => 'delivery-cancelled',
                        default => 'delivery-waiting',
                    };
                @endphp
                <tr>
                    <td>ANT-{{ str_pad($delivery->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        @if($delivery->laundry_order_id)
                            <a href="{{ route('orders.show', $delivery->laundry_order_id) }}" style="color: #0ea5e9; font-weight: 700; text-decoration: none;">
                                ORD-{{ str_pad($delivery->laundry_order_id, 3, '0', STR_PAD_LEFT) }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $delivery->customer->user->name ?? '-' }}</td>
                    <td>{{ $delivery->address }}</td>
                    <td>
                        <strong>{{ $delivery->distance_km }} KM</strong><br>
                        <small style="color: #64748b;">Rp {{ number_format($delivery->fee, 0, ',', '.') }}</small>
                    </td>
                    <td><span class="delivery-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('delivery.update', $delivery) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="delivery-status-select" onchange="this.form.submit()">
                                <option value="menunggu_konfirmasi" {{ $delivery->status === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="diproses" {{ $delivery->status === 'diproses' ? 'selected' : '' }}>Diproses (Kurir Jalan)</option>
                                <option value="selesai" {{ $delivery->status === 'selesai' ? 'selected' : '' }}>Selesai Diantar</option>
                                <option value="dibatalkan" {{ $delivery->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Belum ada permintaan antar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection