@extends('layouts.app')

@section('content')
<section class="page-header delivery-page-header">
    <h1>Manajemen Jemput & Antar</h1>
    <p>Kelola permintaan penjemputan awal dan pengantaran cucian selesai.</p>
</section>

@if(session('success'))
    <div class="alert-success" style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #dcfce7; color: #166534;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert-error" style="margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #fee2e2; color: #991b1b;">
        {{ $errors->first() }}
    </div>
@endif

<div class="customer-toolbar" style="margin-bottom: 16px;">
    <form method="GET" action="{{ route('delivery.index') }}" style="display: flex; gap: 8px;">
        <div style="position: relative; flex: 1; max-width: 400px;">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama pelanggan, ID, atau alamat..."
                style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px 0 12px; outline: none;"
            >
            {{-- <span style="position: absolute; left: 12px; top: 12px; color: #94a3b8;">⌕</span> --}}
        </div>
        <button type="submit" style="height: 42px; background: #0f172a; color: white; border: none; border-radius: 8px; padding: 0 20px; font-weight: 600; cursor: pointer;">
            Cari
        </button>
        @if(request('search'))
            <a href="{{ route('delivery.index') }}" style="height: 42px; display: inline-flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 16px; color: #64748b; text-decoration: none; font-weight: 600; background: #f8fafc;">
                Reset
            </a>
        @endif
    </form>
</div>

<div class="delivery-tabs" style="display: flex; gap: 32px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px;">
    <button class="tab-link active" onclick="openTab(event, 'pickup-tab')" id="btn-pickup-tab" style="background: none; border: none; padding: 12px 0; font-size: 16px; font-weight: 700; cursor: pointer; color: #0ea5e9; border-bottom: 3px solid #0ea5e9; transition: all 0.3s;">
        🛵 Permintaan Jemput <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 12px; font-size: 13px; margin-left: 4px;">{{ $pickups->total() }}</span>
    </button>
    <button class="tab-link" onclick="openTab(event, 'delivery-tab')" id="btn-delivery-tab" style="background: none; border: none; padding: 12px 0; font-size: 16px; font-weight: 600; cursor: pointer; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s;">
        📦 Permintaan Antar <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 13px; margin-left: 4px;">{{ $deliveries->total() }}</span>
    </button>
</div>

<div id="pickup-tab" class="tab-content" style="display: block;">
    <div class="delivery-table-card">
        <table class="delivery-table">
            <thead>
                <tr>
                    <th>ID Jemput</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Alamat Jemput</th>
                    <th>Catatan</th>
                    <th>Jarak & Biaya</th>
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
                        <td>
                            <strong>{{ $request->distance_km }} KM</strong><br>
                            <small style="color: #64748b;">Rp {{ number_format($request->fee, 0, ',', '.') }}</small>
                        </td>
                        <td>{{ $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at)->format('d M Y H:i') : '-' }}</td>
                        <td><span class="delivery-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                        <td>
                            @if($request->laundry_order_id)
                                <a href="{{ route('orders.show', $request->laundry_order_id) }}" class="delivery-order-link">
                                    Lihat Transaksi
                                </a>
                            @elseif($request->status === 'selesai')
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
                            @elseif($request->status === 'dibatalkan')
                                <span class="delivery-muted-text">Dibatalkan</span>
                            @else
                                <span class="delivery-muted-text">Selesaikan jemput dulu</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('delivery.update', $request) }}">
                                @csrf @method('PATCH')
                                <select name="status" class="delivery-status-select" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-size: 13px;">
                                    <option value="menunggu_konfirmasi" {{ $request->status === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                    <option value="diproses" {{ $request->status === 'diproses' ? 'selected' : '' }}>Diproses (Kurir Jalan)</option>
                                    <option value="selesai" {{ $request->status === 'selesai' ? 'selected' : '' }}>Selesai Dijemput</option>
                                    <option value="dibatalkan" {{ $request->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="empty-row" style="text-align: center; padding: 40px; color: #64748b;">Belum ada permintaan jemput yang sesuai pencarian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $pickups->links('pagination::bootstrap-4') }}
    </div>
</div>

<div id="delivery-tab" class="tab-content" style="display: none;">
    <div class="delivery-table-card">
        <table class="delivery-table">
            <thead>
                <tr>
                    <th>ID Antar</th>
                    <th>No. Order</th>
                    <th>Pelanggan</th>
                    <th>Alamat Tujuan</th>
                    <th>Jarak & Biaya</th>
                    <th>Status</th>
                    <th>Update Status</th>
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
                                <a href="{{ route('orders.show', $delivery->laundry_order_id) }}" style="color: #0ea5e9; font-weight: 700; text-decoration: none;">ORD-{{ str_pad($delivery->laundry_order_id, 3, '0', STR_PAD_LEFT) }}</a>
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
                                @csrf @method('PATCH')
                                <select name="status" class="delivery-status-select" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-size: 13px;">
                                    <option value="menunggu_konfirmasi" {{ $delivery->status === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                    <option value="diproses" {{ $delivery->status === 'diproses' ? 'selected' : '' }}>Diproses (Kurir Jalan)</option>
                                    <option value="selesai" {{ $delivery->status === 'selesai' ? 'selected' : '' }}>Selesai Diantar</option>
                                    <option value="dibatalkan" {{ $delivery->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row" style="text-align: center; padding: 40px; color: #64748b;">Belum ada permintaan antar yang sesuai pencarian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $deliveries->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
        tablinks[i].style.color = "#64748b";
        tablinks[i].style.fontWeight = "600";
        tablinks[i].style.borderBottom = "3px solid transparent";

        var badge = tablinks[i].querySelector('span');
        badge.style.background = "#f1f5f9";
        badge.style.color = "#475569";
    }

    document.getElementById(tabName).style.display = "block";

    evt.currentTarget.className += " active";
    evt.currentTarget.style.color = "#0ea5e9";
    evt.currentTarget.style.fontWeight = "700";
    evt.currentTarget.style.borderBottom = "3px solid #0ea5e9";

    var activeBadge = evt.currentTarget.querySelector('span');
    activeBadge.style.background = "#e0f2fe";
    activeBadge.style.color = "#0284c7";

    // Simpan tab yang sedang aktif di memori browser
    localStorage.setItem('activeDeliveryTab', tabName);
}

// Buka otomatis tab terakhir yang dibuka setelah halaman di-reload (karena pencarian/paginasi)
document.addEventListener('DOMContentLoaded', function() {
    let activeTab = localStorage.getItem('activeDeliveryTab');
    if (activeTab) {
        let tabBtn = document.getElementById(activeTab === 'pickup-tab' ? 'btn-pickup-tab' : 'btn-delivery-tab');
        if (tabBtn) tabBtn.click();
    }
});
</script>
<style>
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 6px;
        justify-content: flex-end; /* Posisi di kanan */
        margin-top: 10px;
        margin-bottom: 0;
    }

    .page-item .page-link {
        position: relative;
        display: block;
        padding: 8px 14px;
        color: #64748b;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px; /* Membuat sudut membulat */
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }

    /* Warna tombol saat kursor diarahkan (hover) */
    .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: #f8fafc;
        color: #0ea5e9;
        border-color: #bae6fd;
    }

    /* Warna tombol untuk halaman yang sedang aktif */
    .page-item.active .page-link {
        z-index: 3;
        color: #ffffff;
        background-color: #0ea5e9;
        border-color: #0ea5e9;
        box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);
    }

    /* Tampilan tombol yang tidak bisa diklik (misal tombol '<' di halaman pertama) */
    .page-item.disabled .page-link {
        color: #94a3b8;
        pointer-events: none;
        background-color: #f1f5f9;
        border-color: #e2e8f0;
    }
</style>
@endsection
