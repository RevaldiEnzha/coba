@extends('layouts.app')

@section('content')
<section class="page-header tracking-page-header">
    <h1>Order Tracking</h1>
    <p>Pantau dan update status order laundry</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="tracking-table-card">
    <table class="tracking-table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Nama Pelanggan</th>
                <th>Layanan</th>
                <th>Tanggal Masuk</th>
                <th>Estimasi Selesai</th>
                <th>Status Saat Ini</th>
                <th>Update Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $order)
                @php
                    $currentStatus = $order->status;
                    $statusLabel = $statusOptions[$currentStatus] ?? ucfirst(str_replace('_', ' ', $currentStatus));

                    $statusClass = match($currentStatus) {
                        'diterima' => 'status-masuk',
                        'dicuci' => 'status-cuci',
                        'dijemur' => 'status-kering',
                        'disetrika' => 'status-setrika',
                        'siap_diambil' => 'status-ambil',
                        'selesai' => 'status-selesai',
                        default => 'status-masuk',
                    };

                    $estimatedHours = $order->service->estimated_hours ?? 48;
                    $estimatedDate = $order->created_at
                        ? $order->created_at->copy()->addHours($estimatedHours)->format('d M Y')
                        : '-';
                @endphp

                <tr>
                    <td>ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $order->customer->user->name ?? '-' }}</td>
                    <td>{{ $order->service->name ?? '-' }}</td>
                    <td>{{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}</td>
                    <td>{{ $estimatedDate }}</td>
                    <td>
                        <span class="tracking-status-badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('tracking.update', $order) }}">
                            @csrf
                            @method('PATCH')

                            <select
                                name="status"
                                class="tracking-status-select"
                                onchange="this.form.submit()"
                            >
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">
                        Belum ada order laundry.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="tracking-legend-card">
    <p>Status Order:</p>

    <div class="tracking-legend-list">
        <span class="tracking-status-badge status-masuk">Masuk</span>
        <span class="tracking-status-badge status-cuci">Sedang Dicuci</span>
        <span class="tracking-status-badge status-kering">Pengeringan</span>
        <span class="tracking-status-badge status-setrika">Setrika</span>
        <span class="tracking-status-badge status-ambil">Siap Diambil</span>
        <span class="tracking-status-badge status-selesai">Selesai</span>
    </div>
</div>
@endsection
