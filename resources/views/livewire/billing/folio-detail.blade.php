<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1>Guest Folio</h1>
            <p style="font-family:monospace;font-weight:700;color:var(--navy-700);font-size:15px;">{{ $folio->folio_number }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <span class="badge {{ $folio->status === 'open' ? 'badge-success' : 'badge-secondary' }}" style="font-size:13px;padding:6px 16px;">
                {{ strtoupper($folio->status) }}
            </span>
            <a href="{{ route('invoice.pdf', $folio) }}" target="_blank" class="btn btn-gold" id="btn-print-invoice">🖨️ Cetak Invoice PDF</a>
        </div>
    </div>

    {{-- Guest & Stay Info --}}
    <div style="display:grid;grid-template-columns:3fr 1fr;gap:20px;align-items:start;">
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Guest Info Strip --}}
            <div class="card">
                <div class="card-body" style="display:flex;align-items:center;gap:20px;">
                    <div style="font-size:36px;">👤</div>
                    <div>
                        <div style="font-size:18px;font-weight:800;color:var(--navy-900);">{{ $folio->guest->name }}</div>
                        <div style="font-size:13px;color:var(--gray-500);">Kamar {{ $folio->checkIn->reservation->room->room_number }} — {{ $folio->checkIn->reservation->room->roomType->name }}</div>
                        <div style="font-size:13px;color:var(--gray-500);">{{ $folio->checkIn->actual_check_in->format('d M Y H:i') }} — {{ $folio->checkIn->reservation->check_out_date->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">📋 Daftar Tagihan</span>
                    @if($folio->status === 'open')
                    <button class="btn btn-primary btn-sm" wire:click="$toggle('showAddItem')" id="btn-add-folio-item">+ Tambah Item</button>
                    @endif
                </div>

                {{-- Add Item Form --}}
                @if($showAddItem)
                <div style="padding:16px;background:var(--gray-50);border-bottom:1px solid var(--gray-200);">
                    <div style="display:grid;grid-template-columns:auto 1fr 80px 120px auto;gap:10px;align-items:end;">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Tipe</label>
                            <select class="form-select" wire:model="item_type">
                                <option value="extra">Extra</option>
                                <option value="fnb">F&B</option>
                                <option value="laundry">Laundry</option>
                                <option value="transport">Transport</option>
                                <option value="discount">Diskon</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Keterangan<span class="required">*</span></label>
                            <input type="text" class="form-control" wire:model="item_description" placeholder="Deskripsi item">
                            @error('item_description') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Qty</label>
                            <input type="number" class="form-control" wire:model="item_qty" min="1">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" class="form-control" wire:model="item_unit_price" placeholder="0">
                            @error('item_unit_price') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div style="display:flex;gap:6px;padding-bottom:1px;">
                            <button class="btn btn-success btn-sm" wire:click="addItem" id="btn-confirm-add-item">✅</button>
                            <button class="btn btn-outline btn-sm" wire:click="$set('showAddItem', false)">✕</button>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card-body" style="padding:0;">
                    @forelse($folio->items as $item)
                    <div style="display:grid;grid-template-columns:auto 1fr auto auto auto auto;gap:12px;align-items:center;padding:14px 22px;border-bottom:1px solid var(--gray-100);">
                        <span class="badge badge-{{ $item->type === 'room' ? 'primary' : ($item->type === 'fnb' ? 'success' : ($item->type === 'discount' ? 'danger' : 'secondary')) }}" style="font-size:10px;">{{ strtoupper($item->type) }}</span>
                        <div>
                            <div style="font-weight:600;font-size:13px;">{{ $item->description }}</div>
                            <div style="font-size:11px;color:var(--gray-400);">{{ $item->item_date->format('d M Y') }}</div>
                        </div>
                        <span style="font-size:13px;color:var(--gray-500);">{{ $item->qty }}x</span>
                        <span style="font-size:13px;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        <span style="font-weight:700;font-size:14px;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        @if($folio->status === 'open' && $item->type !== 'room')
                        <button class="btn btn-icon" wire:click="removeItem({{ $item->id }})" wire:confirm="Hapus item ini?" style="color:var(--danger);" id="btn-del-item-{{ $item->id }}">🗑️</button>
                        @else
                        <span></span>
                        @endif
                    </div>
                    @empty
                    <div class="empty-state"><div class="empty-state-icon">📄</div><div class="empty-state-title">Belum ada item tagihan</div></div>
                    @endforelse
                </div>

                {{-- Grand Total --}}
                <div style="padding:16px 22px;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));border-radius:0 0 var(--radius-lg) var(--radius-lg);display:flex;justify-content:flex-end;align-items:center;gap:24px;">
                    <span style="font-size:14px;font-weight:600;color:rgba(255,255,255,.7);">GRAND TOTAL</span>
                    <span style="font-size:32px;font-weight:800;color:var(--gold-300);">Rp {{ number_format($folio->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:12px;">
            <div class="card">
                <div class="card-header"><span class="card-title">📊 Ringkasan</span></div>
                <div class="card-body">
                    @php $grouped = $folio->items->groupBy('type'); @endphp
                    @foreach(['room' => '🏠 Kamar', 'fnb' => '🍽️ F&B', 'extra' => '➕ Extra', 'laundry' => '👕 Laundry', 'discount' => '🏷️ Diskon'] as $type => $label)
                    @if(isset($grouped[$type]))
                    <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid var(--gray-100);">
                        <span>{{ $label }}</span>
                        <span style="font-weight:700;">Rp {{ number_format($grouped[$type]->sum('subtotal'), 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @endforeach
                    <div style="display:flex;justify-content:space-between;font-weight:800;font-size:15px;margin-top:8px;color:var(--navy-900);">
                        <span>Total</span>
                        <span>Rp {{ number_format($folio->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($folio->status === 'open' && $folio->checkIn)
            <a href="{{ route('check-out', $folio->checkIn) }}" class="btn btn-primary w-full" id="btn-go-checkout">🚪 Proses Check-out</a>
            @endif
        </div>
    </div>
</div>
