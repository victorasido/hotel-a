<div wire:poll.3s class="kds-container">
    {{-- KDS Header --}}
    <div class="kds-header">
        <div>
            <div class="kds-title">🍽️ Kitchen Display System</div>
            <div style="font-size:13px;color:var(--gray-600);margin-top:4px;">Grand Nusantara Hotel — Auto refresh tiap 3 detik</div>
        </div>
        <div style="text-align:right;">
            <div class="kds-time" id="kds-clock">{{ now()->format('H:i:s') }}</div>
            <div style="font-size:12px;color:var(--gray-600);margin-top:4px;">{{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <div class="kds-columns-grid">

        {{-- PENDING Column --}}
        <div class="kds-column-card">
            <div class="kds-column-header">
                <div class="kds-column-title" style="color:var(--warning);">⏳ Pending</div>
                <span class="kds-column-badge" style="background:var(--warning);">{{ $pendingOrders->count() }}</span>
            </div>
            
            <div class="kds-orders-list">
                @forelse($pendingOrders as $order)
                <div class="kds-order-card {{ $order->created_at->diffInMinutes(now()) > 5 ? 'urgent' : '' }}">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <div class="kds-order-number">{{ $order->order_number }}</div>
                        <span style="font-size:11px;color:var(--warning);font-weight:600;">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="kds-order-room">
                        🏠 {{ $order->room ? 'Kamar '.$order->room->room_number : 'Walk-in / Restaurant' }}
                    </div>
                    <ul class="kds-item-list">
                        @foreach($order->items as $item)
                        <li class="kds-item">
                            <span>{{ $item->qty }}x {{ $item->menu->name }}</span>
                            @if($item->notes) <span style="font-size:11px;color:var(--gray-500);">📝 {{ $item->notes }}</span> @endif
                        </li>
                        @endforeach
                    </ul>
                    @if($order->notes)
                    <div style="font-size:12px;color:var(--gold-700);margin-bottom:12px;font-style:italic;">📋 Catatan: {{ $order->notes }}</div>
                    @endif
                    <button
                        wire:click="updateStatus({{ $order->id }}, 'processing')"
                        style="width:100%;padding:10px;border-radius:var(--radius-md);background:var(--navy-600);border:none;color:white;font-weight:700;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--navy-500)'"
                        onmouseout="this.style.background='var(--navy-600)'"
                        id="btn-kds-process-{{ $order->id }}"
                    >
                        ▶ MULAI PROSES
                    </button>
                </div>
                @empty
                <div class="kds-empty-state">
                    <div class="kds-empty-icon">✅</div>
                    <div class="kds-empty-text">Tidak ada pesanan pending</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- PROCESSING Column --}}
        <div class="kds-column-card">
            <div class="kds-column-header">
                <div class="kds-column-title" style="color:var(--navy-700);">🔥 Sedang Diproses</div>
                <span class="kds-column-badge" style="background:var(--navy-600);">{{ $processingOrders->count() }}</span>
            </div>
            
            <div class="kds-orders-list">
                @forelse($processingOrders as $order)
                <div class="kds-order-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <div class="kds-order-number">{{ $order->order_number }}</div>
                        <span style="font-size:11px;color:var(--gray-500);">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="kds-order-room">🏠 {{ $order->room ? 'Kamar '.$order->room->room_number : 'Walk-in' }}</div>
                    <ul class="kds-item-list">
                        @foreach($order->items as $item)
                        <li class="kds-item"><span>{{ $item->qty }}x {{ $item->menu->name }}</span></li>
                        @endforeach
                    </ul>
                    @if($order->notes)
                    <div style="font-size:12px;color:var(--gold-700);margin-bottom:12px;font-style:italic;">📋 Catatan: {{ $order->notes }}</div>
                    @endif
                    <button
                        wire:click="updateStatus({{ $order->id }}, 'served')"
                        style="width:100%;padding:10px;border-radius:var(--radius-md);background:var(--success);border:none;color:white;font-weight:700;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--success-dark)'"
                        onmouseout="this.style.background='var(--success)'"
                        id="btn-kds-serve-{{ $order->id }}"
                    >
                        ✅ PESANAN SIAP DISAJIKAN
                    </button>
                </div>
                @empty
                <div class="kds-empty-state">
                    <div class="kds-empty-icon">👨‍🍳</div>
                    <div class="kds-empty-text">Tidak ada pesanan diproses</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
