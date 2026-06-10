@extends('layouts.app')

@section('content')
    <section class="page-header">
        <h1>Dashboard</h1>
        <p>Selamat datang di Sistem Manajemen Laundry</p>
    </section>

    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">👥</div>
            <h2>{{ $stats['total_customers'] }}</h2>
            <p>Total Pelanggan</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">▣</div>
            <h2>{{ $stats['today_transactions'] }}</h2>
            <p>Transaksi Hari Ini</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">◇</div>
            <h2>{{ $stats['active_orders'] }}</h2>
            <p>Order Aktif</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">↗</div>
            <h2>{{ $stats['monthly_income'] }}</h2>
            <p>Pendapatan Bulan Ini</p>
        </div>
    </section>

    <section class="chart-card">
        <h3>Pelanggan baru Minggu Ini</h3>

        <div class="line-chart">
            <svg viewBox="0 0 980 260" preserveAspectRatio="none">
                <line x1="55" y1="20" x2="55" y2="220" class="axis-line" />
                <line x1="55" y1="220" x2="950" y2="220" class="axis-line" />

                <line x1="55" y1="20" x2="950" y2="20" class="grid-line" />
                <line x1="55" y1="70" x2="950" y2="70" class="grid-line" />
                <line x1="55" y1="120" x2="950" y2="120" class="grid-line" />
                <line x1="55" y1="170" x2="950" y2="170" class="grid-line" />

                <text x="10" y="24" class="chart-label">10000</text>
                <text x="18" y="74" class="chart-label">7500</text>
                <text x="18" y="124" class="chart-label">5000</text>
                <text x="18" y="174" class="chart-label">2500</text>
                <text x="40" y="224" class="chart-label">0</text>

                <path
                    d="M 55 172 C 115 185, 180 195, 240 190
                       C 310 185, 365 28, 430 20
                       C 500 28, 555 145, 620 142
                       C 690 140, 735 120, 800 124
                       C 865 128, 910 116, 950 112"
                    class="chart-line"
                />

                <circle cx="55" cy="172" r="4" class="chart-dot" />
                <circle cx="240" cy="190" r="4" class="chart-dot" />
                <circle cx="430" cy="20" r="4" class="chart-dot" />
                <circle cx="620" cy="142" r="4" class="chart-dot" />
                <circle cx="800" cy="124" r="4" class="chart-dot" />
                <circle cx="950" cy="112" r="4" class="chart-dot" />

                <text x="55" y="248" class="chart-day">Sen</text>
                <text x="235" y="248" class="chart-day">Sel</text>
                <text x="425" y="248" class="chart-day">Rab</text>
                <text x="615" y="248" class="chart-day">Kam</text>
                <text x="795" y="248" class="chart-day">Jum</text>
                <text x="875" y="248" class="chart-day">Sab</text>
                <text x="940" y="248" class="chart-day">Min</text>
            </svg>
        </div>
    </section>

    <section class="quick-section">
        <h3>Aksi Cepat</h3>

        <div class="quick-grid">
            <a href="#" class="quick-card quick-primary">
                <div class="quick-icon">+</div>
                <div>
                    <h4>Buat Transaksi Baru</h4>
                    <p>Tambah order laundry baru</p>
                </div>
            </a>

            <a href="#" class="quick-card">
                <div class="quick-icon muted">⌕</div>
                <div>
                    <h4>Cari Pelanggan</h4>
                    <p>Temukan data pelanggan</p>
                </div>
            </a>
        </div>
    </section>
@endsection
