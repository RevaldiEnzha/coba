@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Poin Saya</h1>
    <p>Kelola dan lihat riwayat perolehan serta penggunaan poin loyalitas Anda.</p>
</section>

<div class="portal-stat-card" style="width: max-content; padding: 20px 40px; margin-bottom: 24px;">
    <span>Total Poin Saat Ini</span>
    <h2 style="color: #0ea5e9;">{{ $customer->points_balance ?? 0 }} <small style="font-size: 14px; color: #64748b;">Poin</small></h2>
</div>

<div class="portal-order-card">
    <h3>Riwayat Transaksi Poin</h3>
    <table class="portal-order-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Tipe</th>
                <th>Jumlah Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pointTransactions as $pt)
                <tr>
                    <td>{{ $pt->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $pt->description ?? '-' }}</td>
                    <td>
                        <span class="portal-status-badge {{ $pt->type == 'earn' ? 'badge-green' : 'badge-orange' }}">
                            {{ $pt->type == 'earn' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td>
                        <strong style="color: {{ $pt->type == 'earn' ? '#16a34a' : '#ea580c' }}">
                            {{ $pt->type == 'earn' ? '+' : '-' }}{{ $pt->points }}
                        </strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty-row" style="text-align: center; color: #64748b; padding: 20px;">
                        Belum ada histori poin tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection