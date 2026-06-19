<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Grand Nusantara Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<div class="login-page">
    <div class="login-bg-pattern"></div>

    {{-- LEFT PANEL --}}
    <div class="login-left">
        <div class="login-brand">
            <div class="login-brand-icon">🏨</div>
            <div class="login-brand-name">Grand Nusantara Hotel</div>
            <div class="login-brand-sub">Front Office Management System</div>
        </div>

        <h2 class="login-tagline">
            Kelola Hotel Anda<br>
            dengan <span>Lebih Cerdas</span>
        </h2>
        <p class="login-desc">
            Sistem manajemen front office terpadu — mulai dari reservasi, check-in/out,
            billing tamu, pemesanan F&B, hingga housekeeping, semua dalam satu platform.
        </p>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="login-right">
        <div class="login-card animate-slide-up">
            <div class="login-card-title">Selamat Datang 👋</div>
            <div class="login-card-sub">Silakan masuk untuk melanjutkan</div>

            <form wire:submit="login">
                <div class="form-group">
                    <label class="form-label" for="email">Email<span class="required">*</span></label>
                    <input
                        type="email"
                        id="login-email"
                        class="form-control"
                        wire:model="email"
                        placeholder="admin@hotel.com"
                        autocomplete="email"
                        autofocus
                    >
                    @error('email')
                        <div class="form-error">⚠️ {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password<span class="required">*</span></label>
                    <input
                        type="password"
                        id="login-password"
                        class="form-control"
                        wire:model="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                    >
                    @error('password')
                        <div class="form-error">⚠️ {{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" id="login-remember" wire:model="remember" style="width:16px;height:16px;cursor:pointer;">
                    <label for="login-remember" style="font-size:13px;color:var(--gray-600);cursor:pointer;">Ingat saya</label>
                </div>

                <button type="submit" id="btn-login" class="login-btn" wire:loading.attr="disabled">
                    <span wire:loading.remove>🔐 Masuk ke Sistem</span>
                    <span wire:loading>⏳ Memproses...</span>
                </button>
            </form>

            <div class="divider"></div>
            <div style="font-size:12px;color:var(--gray-400);text-align:center;">
                Demo: admin@hotel.com / password
            </div>
        </div>
    </div>
</div>

@livewireScripts
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
