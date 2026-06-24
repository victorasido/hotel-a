<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Manajemen Front Office - Grand Nusantara Hotel">
    <title>{{ $title ?? 'Dashboard' }} — Grand Nusantara Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<div class="app-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🏨</div>
            <div class="sidebar-hotel-name">Grand Nusantara</div>
            <div class="sidebar-hotel-sub">Hotel</div>
        </div>

        <nav class="sidebar-nav">
            {{-- ─── UTAMA (Semua Role) ─────────────────── --}}
            <div class="nav-section-label">Utama</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               id="nav-dashboard">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>

            {{-- ─── OPERASIONAL (Front Office & Admin) ──── --}}
            @hasanyrole('Super Admin|Front Office')
            <div class="nav-section-label">Operasional</div>

            <a href="{{ route('reservations.portal') }}"
               class="nav-item {{ request()->routeIs('reservations*', 'guests*') ? 'active' : '' }}"
               id="nav-reservations">
                <span class="nav-icon">📅</span>
                <span>Reservasi & Tamu</span>
            </a>
            @endhasanyrole

            {{-- ─── F&B (FnB, Front Office, Admin) ──────── --}}
            @hasanyrole('Super Admin|FnB|Front Office')
            <div class="nav-section-label">F&amp;B</div>

            <a href="{{ route('fnb.portal') }}"
               class="nav-item {{ request()->routeIs('fnb*') ? 'active' : '' }}"
               id="nav-fnb">
                <span class="nav-icon">🍽️</span>
                <span>Layanan F&amp;B</span>
            </a>
            @endhasanyrole

            {{-- ─── HOUSEKEEPING ──────────────────────────── --}}
            @hasanyrole('Super Admin|Housekeeping')
            <div class="nav-section-label">Housekeeping</div>

            <a href="{{ route('housekeeping.tasks') }}"
               class="nav-item {{ request()->routeIs('housekeeping*') ? 'active' : '' }}"
               id="nav-housekeeping">
                <span class="nav-icon">🧹</span>
                <span>Task Board</span>
            </a>
            @endhasanyrole

            {{-- ─── PENGATURAN (Super Admin only) ────────── --}}
            @role('Super Admin')
            <div class="nav-section-label">Pengaturan</div>

            <a href="{{ route('settings.portal') }}"
               class="nav-item {{ request()->routeIs('settings*', 'master*') ? 'active' : '' }}"
               id="nav-settings">
                <span class="nav-icon">⚙️</span>
                <span>Pengaturan Hotel</span>
            </a>
            @endrole
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <div class="main-wrapper">

        {{-- TOP BAR --}}
        <header class="topbar">
            <div class="topbar-left">
                <h1 class="topbar-title">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="topbar-right">
                <span class="topbar-badge">{{ now()->format('d M Y') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="topbar-logout-btn" id="btn-logout">
                        🚪 Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- FLASH NOTIFICATIONS --}}
        <div class="flash-container" id="flash-container">
            @if(session('success'))
                <div class="flash-message flash-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash-message flash-error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    ❌ {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- PAGE CONTENT --}}
        <main class="page-content">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
