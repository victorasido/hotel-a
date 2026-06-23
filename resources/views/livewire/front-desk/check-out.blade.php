<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Proses Check-out</h1><p>Konfirmasi kepergian tamu dan tutup folio.</p></div>
        <a href="{{ route('reservations.show', $checkIn->reservation) }}" class="btn btn-outline">← Kembali</a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Folio Preview --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">💰 Ringkasan Folio Tamu</span>
                    @if($checkIn->folio)
                    <a href="{{ route('folio', $checkIn->folio) }}" class="btn btn-outline btn-sm">Buka Folio</a>
                    @endif
                </div>
                <div class="card-body">
                    @if($checkIn->folio && $checkIn->folio->items->isNotEmpty())
                    <table style="width:100%;border-collapse:collapse;">
                        <thead style="background:var(--gray-50);">
                            <tr>
                                <th style="padding:8px;text-align:left;font-size:12px;color:var(--gray-500);">Keterangan</th>
                                <th style="padding:8px;text-align:center;font-size:12px;color:var(--gray-500);">Qty</th>
                                <th style="padding:8px;text-align:right;font-size:12px;color:var(--gray-500);">Harga</th>
                                <th style="padding:8px;text-align:right;font-size:12px;color:var(--gray-500);">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checkIn->folio->items as $item)
                            <tr style="border-bottom:1px solid var(--gray-100);">
                                <td style="padding:10px 8px;">
                                    <div style="font-weight:600;font-size:13px;">{{ $item->description }}</div>
                                    <span class="badge badge-{{ $item->type === 'room' ? 'primary' : ($item->type === 'fnb' ? 'success' : 'secondary') }}" style="font-size:10px;">{{ strtoupper($item->type) }}</span>
                                </td>
                                <td style="padding:10px 8px;text-align:center;">{{ $item->qty }}</td>
                                <td style="padding:10px 8px;text-align:right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td style="padding:10px 8px;text-align:right;font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="display:flex;justify-content:flex-end;align-items:center;gap:16px;padding-top:16px;border-top:2px solid var(--navy-800);margin-top:8px;">
                        <span style="font-size:15px;font-weight:700;color:var(--gray-600);">GRAND TOTAL</span>
                        <span style="font-size:28px;font-weight:800;color:var(--navy-900);">Rp {{ number_format($checkIn->folio->grand_total, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div class="empty-state"><div class="empty-state-icon">📄</div><div class="empty-state-title">Tidak ada item di folio</div></div>
                    @endif
                </div>
            </div>

            {{-- Payment --}}
            <div class="card">
                <div class="card-header"><span class="card-title">💳 Pembayaran</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select" wire:model="payment_method">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="debit">Kartu Debit</option>
                            <option value="credit">Kartu Kredit</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" wire:model="notes" rows="2" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirm Panel --}}
        <div style="position:sticky;top:80px;">
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,var(--navy-900),var(--navy-700));border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    <span class="card-title" style="color:var(--gold-300);">🚪 Konfirmasi Check-out</span>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Tamu</span><span style="font-weight:700;">{{ $checkIn->guest->name }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Kamar</span><span style="font-weight:700;">{{ $checkIn->reservation->room->room_number }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Waktu</span><span style="font-weight:700;">{{ now()->format('d M Y H:i') }}</span></div>
                        <div class="divider"></div>
                        <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;">
                            <span>Total Bayar</span>
                            <span style="color:var(--navy-900);">Rp {{ number_format($checkIn->folio?->grand_total ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="divider"></div>
                        <div style="background:var(--warning-light);border-radius:var(--radius-md);padding:12px;font-size:12px;color:var(--warning-dark);">
                            ⚠️ Setelah check-out:
                            <ul style="margin-top:6px;padding-left:16px;">
                                <li>Folio ditutup</li>
                                <li>Status kamar → <strong>VD</strong></li>
                                <li>Task HK dibuat otomatis</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="display:flex;flex-direction:column;gap:8px;">
                    @if($checkIn->folio)
                    <a href="{{ route('invoice.pdf', $checkIn->folio) }}" target="_blank" class="btn btn-outline w-full" id="btn-preview-invoice">🖨️ Preview Invoice</a>
                    @endif
                    <button class="btn btn-primary w-full" wire:click="processCheckOut" id="btn-confirm-checkout" wire:loading.attr="disabled">
                        <span wire:loading.remove>🚪 Konfirmasi Check-out</span>
                        <span wire:loading>⏳ Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
