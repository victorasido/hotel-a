<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1>Detail Reservasi</h1>
            <p style="font-family:monospace;font-weight:700;color:var(--navy-700);font-size:16px;">{{ $reservation->booking_code }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline">← Kembali</a>
            @if(in_array($reservation->status, ['confirmed', 'pending']))
            <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-outline">✏️ Edit</a>
            <a href="{{ route('check-in', $reservation) }}" class="btn btn-success" id="btn-proceed-checkin">✅ Proses Check-in</a>
            @endif
            @if($reservation->status === 'checked_in' && $reservation->checkIn)
            <a href="{{ route('check-out', $reservation->checkIn) }}" class="btn btn-primary" id="btn-proceed-checkout">🚪 Proses Check-out</a>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Status Card --}}
            <div class="card">
                <div class="card-body" style="display:flex;align-items:center;gap:20px;">
                    <div style="font-size:48px;">{{ $reservation->status === 'checked_in' ? '🛏️' : ($reservation->status === 'checked_out' ? '✅' : ($reservation->status === 'cancelled' ? '❌' : '📅')) }}</div>
                    <div>
                        <div style="font-size:14px;color:var(--gray-500);margin-bottom:4px;">Status Reservasi</div>
                        <span class="badge badge-{{ $reservation->status_badge['color'] }}" style="font-size:14px;padding:6px 16px;">{{ $reservation->status_badge['label'] }}</span>
                    </div>
                    @if($reservation->checkIn && $reservation->checkIn->folio)
                    <a href="{{ route('folio', $reservation->checkIn->folio) }}" class="btn btn-gold" style="margin-left:auto;">💰 Lihat Folio</a>
                    @endif
                </div>
            </div>

            {{-- Guest Info --}}
            <div class="card">
                <div class="card-header"><span class="card-title">👤 Informasi Tamu</span></div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Nama</div><div style="font-weight:700;font-size:15px;">{{ $reservation->guest->name }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Identitas</div><div>{{ $reservation->guest->id_card_type }}: {{ $reservation->guest->id_card_number ?? '—' }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Telepon</div><div>{{ $reservation->guest->phone ?? '—' }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Kewarganegaraan</div><div>{{ $reservation->guest->nationality }}</div></div>
                    </div>
                </div>
            </div>

            {{-- Room & Stay Info --}}
            <div class="card">
                <div class="card-header"><span class="card-title">🏠 Informasi Kamar & Menginap</span></div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Kamar</div><div style="font-size:24px;font-weight:800;color:var(--navy-900);">{{ $reservation->room->room_number }}</div><div style="font-size:12px;color:var(--gray-500);">{{ $reservation->room->roomType->name }} — Lantai {{ $reservation->room->floor }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Durasi</div><div style="font-size:24px;font-weight:800;color:var(--navy-900);">{{ $reservation->nights }} <span style="font-size:14px;font-weight:500;color:var(--gray-500);">malam</span></div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Check-in</div><div style="font-weight:700;">{{ $reservation->check_in_date->format('d M Y') }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Check-out</div><div style="font-weight:700;">{{ $reservation->check_out_date->format('d M Y') }}</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Jumlah Tamu</div><div style="font-weight:700;">{{ $reservation->pax }} Pax</div></div>
                        <div><div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Sumber</div><div style="font-weight:700;text-transform:capitalize;">{{ str_replace('_', ' ', $reservation->source) }}</div></div>
                    </div>
                    @if($reservation->special_request)
                    <div class="mt-3" style="background:var(--gold-100);border-radius:var(--radius-md);padding:12px;">
                        <div style="font-size:11px;color:var(--gold-700);font-weight:700;margin-bottom:4px;">✨ PERMINTAAN KHUSUS</div>
                        <div style="font-size:13px;color:var(--gray-700);">{{ $reservation->special_request }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Summary --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,var(--navy-900),var(--navy-700));border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    <span class="card-title" style="color:var(--gold-300);">💰 Ringkasan Pembayaran</span>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;"><span style="color:var(--gray-500);">Harga Kamar/malam</span><span>Rp {{ number_format($reservation->room_rate, 0, ',', '.') }}</span></div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;"><span style="color:var(--gray-500);">Jumlah Malam</span><span>{{ $reservation->nights }}</span></div>
                        <div class="divider"></div>
                        <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;"><span>Total Kamar</span><span>Rp {{ number_format($reservation->total_amount, 0, ',', '.') }}</span></div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--success);"><span>Deposit</span><span>Rp {{ number_format($reservation->deposit, 0, ',', '.') }}</span></div>
                        <div class="divider"></div>
                        <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:800;color:var(--navy-900);"><span>Sisa Tagihan</span><span>Rp {{ number_format($reservation->total_amount - $reservation->deposit, 0, ',', '.') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><span class="card-title">📝 Timeline</span></div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot done"></div>
                            <div class="timeline-content">
                                <div class="timeline-label">Reservasi Dibuat</div>
                                <div>{{ $reservation->created_at->format('d M Y H:i') }}</div>
                                <div style="font-size:11px;color:var(--gray-400);">oleh {{ $reservation->createdBy?->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot {{ in_array($reservation->status, ['checked_in','checked_out']) ? 'done' : 'active' }}"></div>
                            <div class="timeline-content">
                                <div class="timeline-label">Check-in</div>
                                <div>{{ $reservation->check_in_date->format('d M Y') }}</div>
                                @if($reservation->checkIn)
                                <div style="font-size:11px;color:var(--success);">✅ {{ $reservation->checkIn->actual_check_in->format('H:i') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $reservation->status === 'checked_out' ? 'done' : '' }}"></div>
                            <div class="timeline-content">
                                <div class="timeline-label">Check-out</div>
                                <div>{{ $reservation->check_out_date->format('d M Y') }}</div>
                                @if($reservation->checkIn?->checkOut)
                                <div style="font-size:11px;color:var(--success);">✅ {{ $reservation->checkIn->checkOut->actual_check_out->format('H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
