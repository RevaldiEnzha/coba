@extends('layouts.app')

@section('content')
<section class="page-header delivery-page-header">
    <h1>Pesanan Jemput</h1>
    <p>Kelola permintaan jemput cucian dari pelanggan</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert-error">
        {{ $errors->first() }}
    </div>
@endif

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
            @forelse($requests as $request)
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
                    <td>
                        {{ $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at)->format('d M Y H:i') : '-' }}
                    </td>
                    <td>
                        <span class="delivery-status {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td>
                        @if($request->laundry_order_id)
                            <a href="{{ route('orders.show', $request->laundry_order_id) }}" class="delivery-order-link">
                                Lihat Transaksi
                            </a>
                        @elseif($request->status === 'menunggu_konfirmasi')
                            <form method="POST" action="{{ route('delivery.confirm', $request) }}" class="delivery-confirm-form">
                                @csrf

                                <input
                                    type="number"
                                    step="0.1"
                                    min="0.1"
                                    name="amount"
                                    placeholder="{{ ($request->service->type ?? 'kiloan') === 'kiloan' ? 'Berat kg' : 'Jumlah item' }}"
                                    required
                                >

                                <button type="submit">
                                    Buat Transaksi
                                </button>
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
                                <option value="menunggu_konfirmasi" {{ $request->status === 'menunggu_konfirmasi' ? 'selected' : '' }}>
                                    Menunggu Konfirmasi
                                </option>
                                <option value="diproses" {{ $request->status === 'diproses' ? 'selected' : '' }}>
                                    Diproses
                                </option>
                                <option value="selesai" {{ $request->status === 'selesai' ? 'selected' : '' }}>
                                    Selesai
                                </option>
                                <option value="dibatalkan" {{ $request->status === 'dibatalkan' ? 'selected' : '' }}>
                                    Dibatalkan
                                </option>
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-row">
                        Belum ada permintaan jemput.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
