<div class="animate-fade-in">
    {{-- Page Header --}}
    <div class="page-header">
        <h1>Pengaturan Hotel</h1>
        <p>Kelola konfigurasi dasar hotel: tipe kamar, inventaris kamar, dan akun pengguna.</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="page-tabs">
        <button
            wire:click="setTab('room-types')"
            class="page-tab {{ $activeTab === 'room-types' ? 'active' : '' }}"
            id="tab-btn-room-types"
        >
            🏷️ Tipe Kamar
        </button>
        <button
            wire:click="setTab('rooms')"
            class="page-tab {{ $activeTab === 'rooms' ? 'active' : '' }}"
            id="tab-btn-rooms"
        >
            🔑 Daftar Kamar
        </button>
        <button
            wire:click="setTab('users')"
            class="page-tab {{ $activeTab === 'users' ? 'active' : '' }}"
            id="tab-btn-users"
        >
            👤 Pengguna
        </button>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content" wire:key="settings-portal-tab-{{ $activeTab }}">
        @if($activeTab === 'room-types')
            <livewire:master-data.room-types key="portal-room-types" />
        @elseif($activeTab === 'rooms')
            <livewire:master-data.rooms key="portal-rooms" />
        @elseif($activeTab === 'users')
            <livewire:master-data.users key="portal-users" />
        @endif
    </div>
</div>
