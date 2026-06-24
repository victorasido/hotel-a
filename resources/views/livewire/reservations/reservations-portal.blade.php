<div class="animate-fade-in">
    {{-- Page Header --}}
    <div class="page-header">
        <h1>Reservasi & Tamu</h1>
        <p>Kelola seluruh reservasi kamar dan database tamu dalam satu tempat.</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="page-tabs">
        <button
            wire:click="setTab('reservations')"
            class="page-tab {{ $activeTab === 'reservations' ? 'active' : '' }}"
            id="tab-btn-reservations"
        >
            📅 Daftar Reservasi
        </button>
        <button
            wire:click="setTab('guests')"
            class="page-tab {{ $activeTab === 'guests' ? 'active' : '' }}"
            id="tab-btn-guests"
        >
            👥 Database Tamu
        </button>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content" wire:key="reservation-portal-tab-{{ $activeTab }}">
        @if($activeTab === 'reservations')
            <livewire:reservations.reservation-list key="portal-reservation-list" />
        @elseif($activeTab === 'guests')
            <livewire:guests.guest-list key="portal-guest-list" />
        @endif
    </div>
</div>
