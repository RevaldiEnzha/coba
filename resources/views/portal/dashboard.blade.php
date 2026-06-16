@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Dashboard Pelanggan</h1>
    <p>Selamat datang, {{ auth()->user()->name }}</p>
</section>

<div class="portal-stats-grid">
    <div class="portal-stat-card">
        <span>Total Poin</span>
        <h2>{{ $customer->points_balance ?? 0 }}</h2>
        <p>Poin loyalitas Anda</p>
    </div>

    <div class="portal-stat-card">
        <span>Order Aktif</span>
        <h2>{{ $activeOrders->count() }}</h2>
        <p>Cucian sedang diproses</p>
    </div>

    <div class="portal-stat-card">
        <span>Order Selesai</span>
        <h2>{{ $completedOrders->count() }}</h2>
        <p>Riwayat cucian selesai</p>
    </div>
</div>

<div class="portal-profile-card">
    <h3>Informasi Pelanggan</h3>

    <div class="portal-profile-row">
        <span>Nama</span>
        <strong>{{ auth()->user()->name }}</strong>
    </div>

    <div class="portal-profile-row">
        <span>Nomor Telepon</span>
        <strong>{{ $customer->phone ?? '-' }}</strong>
    </div>

    <div class="portal-profile-row">
        <span>Alamat</span>
        <strong>{{ $customer->address ?? '-' }}</strong>
    </div>
</div>

<div class="portal-pickup-card">
    <div>
        <h3>Ajukan Jemput Cucian</h3>
        <p>Buat permintaan agar cucian Anda dijemput oleh staff laundry.</p>
    </div>

    <button type="button" class="portal-pickup-btn" id="openPickupModal">
        + Ajukan Jemput
    </button>
</div>

<div class="modal-overlay" id="pickupModal">
    <div class="customer-modal-card">
        <div class="customer-modal-header">
            <h3>Ajukan Jemput Cucian</h3>
            <button type="button" class="modal-close-btn" data-close-pickup-modal>&times;</button>
        </div>

        <form method="POST" action="{{ route('portal.pickups.store') }}" class="customer-modal-form">
            @csrf

            <div class="modal-form-group">
                <label>Alamat Jemput</label>
                <textarea name="address" placeholder="Masukkan alamat jemput">{{ old('address', $customer->address ?? '') }}</textarea>
                @error('address') <small class="error-text">{{ $message }}</small> @enderror
            </div>

            <div class="modal-form-group">
                <label>Jadwal Jemput</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}">
                @error('scheduled_at') <small class="error-text">{{ $message }}</small> @enderror
            </div>

            <div class="customer-modal-actions">
                <button type="button" class="modal-cancel-btn" data-close-pickup-modal>Batal</button>
                <button type="submit" class="modal-submit-btn">Kirim Permintaan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pickupModal = document.getElementById('pickupModal');
    const openPickupBtn = document.getElementById('openPickupModal');
    const closePickupButtons = document.querySelectorAll('[data-close-pickup-modal]');

    function openPickupModal() {
        pickupModal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closePickupModal() {
        pickupModal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    if (openPickupBtn) {
        openPickupBtn.addEventListener('click', openPickupModal);
    }

    closePickupButtons.forEach(button => {
        button.addEventListener('click', closePickupModal);
    });

    pickupModal.addEventListener('click', function (event) {
        if (event.target === pickupModal) {
            closePickupModal();
        }
    });
});
</script>

<div class="portal-order-card">
    <h3>Riwayat Order Terbaru</h3>

    <table class="portal-order-table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Layanan</th>
                <th>Total</th>
                <th>Status Cucian</th>
                <th>Status Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($recentOrders as $order)
                <tr>
                    <td>ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
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
                    <td colspan="6" class="empty-row">Belum ada order laundry.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
