<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Proses Check-in</h1><p>Konfirmasi kedatangan tamu dan buka folio.</p></div>
        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline">← Kembali</a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
        <div style="display:flex;flex-direction:column;gap:16px;">
            {{-- Reservation Summary --}}
            <div class="card">
                <div class="card-header"><span class="card-title">📋 Ringkasan Reservasi</span></div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;margin-bottom:4px;">Kode Booking</div><div style="font-family:monospace;font-weight:700;font-size:15px;color:var(--navy-700);">{{ $reservation->booking_code }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;margin-bottom:4px;">Tamu</div><div style="font-weight:700;font-size:15px;">{{ $reservation->guest->name }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;margin-bottom:4px;">Kamar</div><div style="font-size:24px;font-weight:800;color:var(--navy-900);">{{ $reservation->room->room_number }}</div><div style="font-size:12px;color:var(--gray-500);">{{ $reservation->room->roomType->name }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;margin-bottom:4px;">Masa Menginap</div><div style="font-weight:700;">{{ $reservation->check_in_date->format('d M') }} — {{ $reservation->check_out_date->format('d M Y') }}</div><div style="font-size:12px;color:var(--gray-500);">{{ $reservation->nights }} malam</div></div>
                    </div>
                </div>
            </div>

            {{-- Extra Info --}}
            <div class="card">
                <div class="card-header"><span class="card-title">📝 Informasi Tambahan</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Tamu Tambahan (Extra Pax)</label>
                        <input type="number" class="form-control" wire:model="extra_pax" min="0" style="max-width:120px;">
                        <div class="form-hint">Selain jumlah pax yang sudah didaftarkan ({{ $reservation->pax }} pax).</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan Check-in</label>
                        <textarea class="form-control" wire:model="notes" rows="3" placeholder="Catatan khusus saat check-in..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirm Panel --}}
        <div style="position:sticky;top:80px;">
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,var(--success-dark),var(--success));border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    <span class="card-title" style="color:white;">✅ Konfirmasi Check-in</span>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Waktu Check-in</span><span style="font-weight:700;">{{ now()->format('d M Y H:i') }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Oleh</span><span style="font-weight:700;">{{ auth()->user()->name }}</span></div>
                        <div class="divider"></div>
                        <div style="background:var(--warning-light);border-radius:var(--radius-md);padding:12px;font-size:12px;color:var(--warning-dark);">
                            ⚠️ Setelah check-in:
                            <ul style="margin-top:6px;padding-left:16px;">
                                <li>Status kamar berubah → <strong>OC</strong></li>
                                <li>Status reservasi → <strong>Checked In</strong></li>
                                <li>Guest Folio akan dibuat otomatis</li>
                                <li>Biaya kamar masuk ke folio</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success w-full" wire:click="processCheckIn" id="btn-confirm-checkin" wire:loading.attr="disabled">
                        <span wire:loading.remove>✅ Konfirmasi Check-in</span>
                        <span wire:loading>⏳ Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
