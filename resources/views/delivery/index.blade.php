@extends('layouts.app')

@section('content')
<section class="page-header delivery-page-header">
    <h1>Pesanan Jemput</h1>
    <p>Kelola permintaan jemput cucian dari pelanggan</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="delivery-table-card">
    <table class="delivery-table">
        <thead>
            <tr>
                <th>ID Jemput</th>
                <th>Pelanggan</th>
                <th>No. HP</th>
                <th>Alamat Jemput</th>
                <th>Jadwal</th>
                <th>Status</th>
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
                    <td>{{ $request->customer->phone ?? '-' }}</td>
                    <td>{{ $request->address }}</td>
                    <td>{{ $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at)->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <span class="delivery-status {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
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
                    <td colspan="7" class="empty-row">
                        Belum ada permintaan jemput.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
