<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Daftar Reservasi</h1><p>Kelola semua reservasi tamu hotel.</p></div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary" id="btn-new-res">+ Reservasi Baru</a>
    </div>

    <div class="search-filter-bar">
        <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Cari kode booking atau nama tamu...">
        </div>
        <select class="form-select" wire:model.live="filterStatus" style="width:auto;">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="checked_in">Checked In</option>
            <option value="checked_out">Checked Out</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <input type="date" class="form-control" wire:model.live="filterDate" style="width:auto;">
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Kode Booking</th><th>Tamu</th><th>Kamar</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($reservations as $r)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;font-size:12px;color:var(--navy-700);">{{ $r->booking_code }}</span></td>
                        <td><div style="font-weight:600;">{{ $r->guest->name }}</div><div style="font-size:11px;color:var(--gray-400);">{{ $r->pax }} Pax</div></td>
                        <td><div>{{ $r->room->room_number }}</div><div style="font-size:11px;color:var(--gray-400);">{{ $r->room->roomType->name }}</div></td>
                        <td>{{ $r->check_in_date->format('d M Y') }}</td>
                        <td>{{ $r->check_out_date->format('d M Y') }}</td>
                        <td class="price-display">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $r->status_badge['color'] }}">{{ $r->status_badge['label'] }}</span></td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                <a href="{{ route('reservations.show', $r) }}" class="btn btn-outline btn-sm" id="btn-view-res-{{ $r->id }}">👁️</a>
                                @if(in_array($r->status, ['confirmed', 'pending']))
                                <a href="{{ route('reservations.edit', $r) }}" class="btn btn-outline btn-sm">✏️</a>
                                <a href="{{ route('check-in', $r) }}" class="btn btn-success btn-sm" id="btn-checkin-{{ $r->id }}">✅ CI</a>
                                <button class="btn btn-danger btn-sm" wire:click="cancelReservation({{ $r->id }})" wire:confirm="Yakin batalkan reservasi ini?" id="btn-cancel-res-{{ $r->id }}">✕</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8"><div class="empty-state"><div class="empty-state-icon">📭</div><div class="empty-state-title">Tidak ada reservasi</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reservations->hasPages())
        <div class="card-footer">{{ $reservations->links() }}</div>
        @endif
    </div>
</div>
