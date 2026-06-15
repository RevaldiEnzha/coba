<!DOCTYPE html>
<html>
<head>
    <title>Portal Pelanggan</title>

    <link rel="stylesheet"
          href="{{ asset('css/laundry.css') }}">
</head>

<body class="app-body">

<div class="app-shell">

    <aside class="sidebar">

        <div class="brand">
            <div class="brand-logo">L</div>
            <span>Laundry Portal</span>
        </div>

        <nav class="sidebar-menu">

            <a href="#" class="active">
                
                Dashboard
            </a>

            <a href="#">
                Pesanan Aktif
            </a>

            <a href="#">
                Riwayat
            </a>

            <a href="#">
                Poin Saya
            </a>

            <a href="#">
                Akun Saya
            </a>

        </nav>

        <div class="logout-area">

            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button type="submit">

                    <span class="menu-icon">↩</span>

                    Logout

                </button>

            </form>

        </div>

    </aside>

    <main class="main-content">

        @yield('content')

    </main>

</div>

</body>
</html>