<div class="animate-fade-in">
    {{-- Page Header --}}
    <div class="page-header">
        <h1>Layanan F&B</h1>
        <p>Kelola pemesanan restoran, pantau dapur secara real-time, dan atur daftar menu.</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="page-tabs">
        {{-- Semua role yang bisa akses F&B lihat tab Orders --}}
        <button
            wire:click="setTab('orders')"
            class="page-tab {{ $activeTab === 'orders' ? 'active' : '' }}"
            id="tab-btn-fnb-orders"
        >
            🍽️ Daftar Pesanan
        </button>

        {{-- Hanya FnB & Super Admin yang bisa akses tab Kitchen --}}
        @hasanyrole('Super Admin|FnB')
        <button
            wire:click="setTab('kitchen')"
            class="page-tab {{ $activeTab === 'kitchen' ? 'active' : '' }}"
            id="tab-btn-fnb-kitchen"
        >
            👨‍🍳 Kitchen Display
        </button>

        <button
            wire:click="setTab('menu')"
            class="page-tab {{ $activeTab === 'menu' ? 'active' : '' }}"
            id="tab-btn-fnb-menu"
        >
            📋 Kelola Menu
        </button>
        @endhasanyrole
    </div>

    {{-- Tab Content --}}
    <div class="tab-content" wire:key="fnb-portal-tab-{{ $activeTab }}">
        @if($activeTab === 'orders')
            <livewire:fnb.order-list key="portal-fnb-order-list" />
        @elseif($activeTab === 'kitchen')
            @hasanyrole('Super Admin|FnB')
                <livewire:fnb.kitchen-display key="portal-fnb-kitchen" />
            @endhasanyrole
        @elseif($activeTab === 'menu')
            @hasanyrole('Super Admin|FnB')
                <livewire:fnb.menu-management key="portal-fnb-menu" />
            @endhasanyrole
        @endif
    </div>
</div>
