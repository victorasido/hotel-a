<div wire:poll.3s>
    {{-- KDS Header --}}
    <div class="kds-header">
        <div>
            <div class="kds-title">🍽️ Kitchen Display System</div>
            <div style="font-size:13px;color:var(--navy-300);">Grand Nusantara Hotel — Auto refresh tiap 3 detik</div>
        </div>
        <div style="text-align:right;">
            <div class="kds-time" id="kds-clock">{{ now()->format('H:i:s') }}</div>
            <div style="font-size:12px;color:var(--navy-300);">{{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

        {{-- PENDING Column --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                <span style="font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--warning);">⏳ Pending</span>
                <span style="background:var(--warning);color:white;font-size:12px;font-weight:800;padding:3px 10px;border-radius:20px;">{{ $pendingOrders->count() }}</span>
            </div>
            <div class="kds-grid" style="grid-template-columns:1fr;">
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
                            @if($item->notes) <span style="font-size:11px;color:var(--navy-300);">📝 {{ $item->notes }}</span> @endif
                        </li>
                        @endforeach
                    </ul>
                    @if($order->notes)
                    <div style="font-size:11px;color:var(--gold-300);margin-bottom:10px;">📋 {{ $order->notes }}</div>
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
                <div style="text-align:center;padding:40px;color:var(--navy-400);">
                    <div style="font-size:36px;margin-bottom:8px;">✅</div>
                    <div style="font-size:14px;">Tidak ada pesanan pending</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- PROCESSING Column --}}
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                <span style="font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--navy-300);">🔥 Sedang Diproses</span>
                <span style="background:var(--navy-600);color:white;font-size:12px;font-weight:800;padding:3px 10px;border-radius:20px;">{{ $processingOrders->count() }}</span>
            </div>
            <div class="kds-grid" style="grid-template-columns:1fr;">
                @forelse($processingOrders as $order)
                <div class="kds-order-card" style="border-color:var(--navy-500);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <div class="kds-order-number">{{ $order->order_number }}</div>
                        <span style="font-size:11px;color:var(--navy-300);">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="kds-order-room">🏠 {{ $order->room ? 'Kamar '.$order->room->room_number : 'Walk-in' }}</div>
                    <ul class="kds-item-list">
                        @foreach($order->items as $item)
                        <li class="kds-item"><span>{{ $item->qty }}x {{ $item->menu->name }}</span></li>
                        @endforeach
                    </ul>
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
                <div style="text-align:center;padding:40px;color:var(--navy-400);">
                    <div style="font-size:36px;margin-bottom:8px;">👨‍🍳</div>
                    <div style="font-size:14px;">Tidak ada pesanan diproses</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
