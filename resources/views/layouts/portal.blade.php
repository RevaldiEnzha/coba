<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pelanggan - Sistem Laundry</title>
    <link rel="stylesheet" href="{{ asset('css/laundry.css') }}">
</head>
<body class="portal-body">
    <div class="portal-shell">
        <aside class="portal-sidebar">
            <div class="brand">
                <div class="brand-logo">L</div>
                <span>Laundry System</span>
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon">▦</span>
                    Dashboard
                </a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="logout-area">
                @csrf
                <button type="submit">
                    <span class="menu-icon">↪</span>
                    Keluar
                </button>
            </form>
        </aside>

        <main class="portal-main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
