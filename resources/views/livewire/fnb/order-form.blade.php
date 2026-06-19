<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Order F&B Baru</h1><p>Pilih menu dan konfirmasi pesanan.</p></div>
        <a href="{{ route('fnb.orders') }}" class="btn btn-outline">← Kembali</a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">

        {{-- Menu Grid --}}
        <div>
            {{-- Room Selection --}}
            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Tipe Order</label>
                            <select class="form-select" wire:model="order_type">
                                <option value="room_service">Room Service</option>
                                <option value="restaurant">Restoran</option>
                                <option value="takeaway">Takeaway</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Kamar (untuk Room Service)</label>
                            <select class="form-select" wire:model="room_id">
                                <option value="">-- Walk-in / Tanpa Kamar --</option>
                                @foreach($occupiedRooms as $room)
                                <option value="{{ $room->id }}">Kamar {{ $room->room_number }} — {{ $room->roomType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Menu by Category --}}
            @foreach($categories as $cat)
            <div style="margin-bottom:20px;">
                <div class="floor-label">{{ $cat->icon }} {{ $cat->name }}</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
                    @foreach($cat->menus as $menu)
                    <div
                        wire:click="addToCart({{ $menu->id }})"
                        style="background:white;border:1.5px solid var(--gray-200);border-radius:var(--radius-md);padding:14px;cursor:pointer;transition:all .2s;"
                        onmouseover="this.style.borderColor='var(--navy-400)';this.style.transform='translateY(-2px)';this.style.boxShadow='var(--shadow-md)'"
                        onmouseout="this.style.borderColor='var(--gray-200)';this.style.transform='';this.style.boxShadow=''"
                        id="btn-menu-{{ $menu->id }}"
                    >
                        <div style="font-size:24px;margin-bottom:6px;">{{ $cat->icon }}</div>
                        <div style="font-weight:700;font-size:13px;color:var(--navy-900);margin-bottom:4px;">{{ $menu->name }}</div>
                        <div style="font-weight:800;color:var(--navy-600);">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Cart Sidebar --}}
        <div style="position:sticky;top:80px;">
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,var(--navy-900),var(--navy-700));border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    <span class="card-title" style="color:var(--gold-300);">🛒 Pesanan ({{ count($cartItems) }} item)</span>
                </div>
                <div class="card-body" style="padding:12px;max-height:400px;overflow-y:auto;">
                    @forelse($cartItems as $i => $item)
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--gray-100);">
                        <div style="flex:1;">
                            <div style="font-weight:600;font-size:12px;">{{ $item['name'] }}</div>
                            <div style="font-size:11px;color:var(--gray-400);">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <button
                                wire:click="$set('cartItems.{{ $i }}.qty', {{ max(1, $item['qty'] - 1) }})"
                                style="width:22px;height:22px;border:1px solid var(--gray-300);border-radius:50%;background:white;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;"
                            >-</button>
                            <span style="font-weight:700;min-width:20px;text-align:center;">{{ $item['qty'] }}</span>
                            <button
                                wire:click="$set('cartItems.{{ $i }}.qty', {{ $item['qty'] + 1 }})"
                                style="width:22px;height:22px;border:1px solid var(--gray-300);border-radius:50%;background:white;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;"
                            >+</button>
                        </div>
                        <div style="font-weight:700;font-size:12px;min-width:70px;text-align:right;">Rp {{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}</div>
                        <button wire:click="removeFromCart({{ $i }})" style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:14px;">✕</button>
                    </div>
                    @empty
                    <div style="text-align:center;padding:20px;color:var(--gray-400);">
                        <div style="font-size:24px;margin-bottom:6px;">🛒</div>
                        <div style="font-size:12px;">Klik menu untuk menambah ke pesanan</div>
                    </div>
                    @endforelse
                </div>
                @if(count($cartItems) > 0)
                <div class="card-footer" style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;">
                        <span>Total</span>
                        <span>Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" wire:model="notes" rows="2" placeholder="Catatan pesanan..."></textarea>
                    </div>
                    @error('cartItems') <div class="form-error">{{ $message }}</div> @enderror
                    <button class="btn btn-gold w-full" wire:click="submitOrder" id="btn-submit-fnb-order" wire:loading.attr="disabled">
                        <span wire:loading.remove>🍽️ Buat Order</span>
                        <span wire:loading>⏳ Memproses...</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
