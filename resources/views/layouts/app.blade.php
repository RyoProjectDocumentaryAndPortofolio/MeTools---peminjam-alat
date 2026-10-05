<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toolsme - Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('toolsme.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

    <div class="app-container">

        <!-- ============================================
        SIDEBAR
        ============================================ -->
        <aside class="sidebar">

            <!-- Brand -->
            <div class="sidebar-brand">
                <h1>Toolsme</h1>
                <p class="subtitle">Management System</p>
                <div class="brand-divider"></div>
            </div>

            <!-- Nav Menu -->
            <nav class="sidebar-nav">

                @auth

                    <!-- ============================================
                    MENU ADMIN
                    ============================================ -->
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">dashboard</span>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.alat.index') }}" class="nav-link {{ request()->routeIs('admin.alat.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">inventory_2</span>
                            Kelola Alat
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">category</span>
                            Kelola Kategori
                        </a>
                        <a href="{{ route('admin.peminjaman.index') }}" class="nav-link {{ request()->routeIs('admin.peminjaman.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">assignment</span>
                            Kelola Peminjaman
                        </a>
                        <a href="{{ route('admin.pengembalian.index') }}" class="nav-link {{ request()->routeIs('admin.pengembalian.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">swap_horiz</span>
                            Kelola Pengembalian
                        </a>
                        <a href="{{ route('admin.user.index') }}" class="nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">people</span>
                            Kelola User
                        </a>
                    @endif

                    <!-- ============================================
                    MENU PETUGAS
                    ============================================ -->
                    @if(auth()->user()->role === 'petugas')
                        <a href="{{ route('petugas.peminjaman.index') }}" class="nav-link {{ request()->routeIs('petugas.peminjaman.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">assignment</span>
                            Peminjaman
                        </a>
                        <a href="{{ route('petugas.pengembalian.index') }}" class="nav-link {{ request()->routeIs('petugas.pengembalian.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">swap_horiz</span>
                            Pemantauan Pengembalian
                        </a>
                        <!-- Menu Laporan -->
                        <a href="{{ route('petugas.laporan.index') }}" class="nav-link {{ request()->routeIs('petugas.laporan.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">receipt_long</span>
                            Laporan
                        </a>
                    @endif

                    <!-- ============================================
                    MENU PEMINJAM
                    ============================================ -->
                    @if(auth()->user()->role === 'peminjam')
                        <a href="{{ route('peminjam.katalog') }}" class="nav-link {{ request()->routeIs('peminjam.katalog') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">search</span>
                            Katalog Alat
                        </a>
                        <a href="{{ route('peminjam.riwayat') }}" class="nav-link {{ request()->routeIs('peminjam.riwayat') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">history</span>
                            Riwayat Pinjam
                        </a>
                    @endif

                @endauth

            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="rivet rivet-bl"></div>
                <div class="rivet rivet-br"></div>
                <p class="text-xs text-center text-on-surface-variant opacity-50">
                    ryoCompany
                </p>
            </div>

        </aside>

        <!-- ============================================
        MAIN CONTENT
        ============================================ -->
        <div class="main-content">

            <!-- Navbar -->
            <header class="navbar">
                <div class="navbar-left">
                    <h2>@yield('header-title', 'Dashboard')</h2>
                </div>
                <div class="navbar-right">
                    <div class="user-info">
                        <span class="user-avatar">
                            <span class="material-symbols-outlined">account_circle</span>
                        </span>
                        <div class="user-detail">
                            <span class="user-name">{{ auth()->user()->name ?? 'Guest' }}</span>
                            <span class="user-role">{{ auth()->user()->role ?? '' }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <span class="material-symbols-outlined">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content -->
            <main class="content">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Scripts -->
    @stack('scripts')

</body>
</html>