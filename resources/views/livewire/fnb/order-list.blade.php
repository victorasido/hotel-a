<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Daftar Order F&B</h1><p>Monitor semua pesanan makanan dan minuman.</p></div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('fnb.kitchen') }}" class="btn btn-outline">👨‍🍳 Kitchen Display</a>
            <a href="{{ route('fnb.orders.create') }}" class="btn btn-primary" id="btn-new-fnb-order">+ Order Baru</a>
        </div>
    </div>

    <div class="search-filter-bar">
        <select class="form-select" wire:model.live="filterStatus" style="width:auto;">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Diproses</option>
            <option value="served">Disajikan</option>
            <option value="cancelled">Dibatalkan</option>
        </select>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>No. Order</th><th>Kamar</th><th>Item</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;font-size:12px;color:var(--navy-700);">{{ $order->order_number }}</span></td>
                        <td>{{ $order->room ? 'Kamar '.$order->room->room_number : 'Walk-in' }}</td>
                        <td>
                            @foreach($order->items->take(2) as $item)
                            <div style="font-size:12px;">{{ $item->qty }}x {{ $item->menu->name }}</div>
                            @endforeach
                            @if($order->items->count() > 2) <div style="font-size:11px;color:var(--gray-400);">+{{ $order->items->count()-2 }} lainnya</div> @endif
                        </td>
                        <td class="price-display">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $order->status_badge['color'] }}">{{ $order->status_badge['label'] }}</span></td>
                        <td style="font-size:12px;color:var(--gray-400);">{{ $order->created_at->format('d M H:i') }}</td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                @if($order->status === 'pending')
                                <button class="btn btn-primary btn-sm" wire:click="updateStatus({{ $order->id }}, 'processing')" id="btn-process-order-{{ $order->id }}">▶ Proses</button>
                                @elseif($order->status === 'processing')
                                <button class="btn btn-success btn-sm" wire:click="updateStatus({{ $order->id }}, 'served')" id="btn-serve-order-{{ $order->id }}">✅ Sajikan</button>
                                @endif
                                @if(in_array($order->status, ['pending']))
                                <button class="btn btn-danger btn-sm" wire:click="updateStatus({{ $order->id }}, 'cancelled')" wire:confirm="Batalkan order ini?" id="btn-cancel-order-{{ $order->id }}">✕</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-state-icon">🍽️</div><div class="empty-state-title">Tidak ada order</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages()) <div class="card-footer">{{ $orders->links() }}</div> @endif
    </div>
</div>
