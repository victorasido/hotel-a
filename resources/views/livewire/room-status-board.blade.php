<div wire:poll.5s class="animate-fade-in">

    {{-- Page Header --}}
    <div class="page-header flex items-center justify-between">
        <div>
            <h1>Room Status Board</h1>
            <p>Update otomatis setiap 5 detik. Klik kamar untuk detail & update status.</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gray-400);">
            <span style="width:8px;height:8px;border-radius:50%;background:var(--success);display:inline-block;animation:pulse-dot 2s infinite;"></span>
            Live — {{ now()->format('H:i:s') }}
        </div>
    </div>

    {{-- Status Legend & Filter --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div class="status-legend">
            <div class="legend-item {{ $filterStatus === '' ? 'active' : '' }}" wire:click="$set('filterStatus', '')" style="background:var(--gray-100);color:var(--gray-700);">
                <span class="legend-dot" style="background:var(--gray-500);"></span>
                Semua ({{ $statusCounts->sum() }})
            </div>
            @foreach([
                'VC' => ['#1b5e20', '#e8f5e9', 'Vacant Clean'],
                'VD' => ['#f57f17', '#fff8e1', 'Vacant Dirty'],
                'OC' => ['#0d47a1', '#e3f2fd', 'Occupied Clean'],
                'OD' => ['#e65100', '#fff3e0', 'Occupied Dirty'],
                'OOO' => ['#b71c1c', '#fce4ec', 'Out of Order'],
                'OOS' => ['#616161', '#f5f5f5', 'Out of Service'],
            ] as $status => [$color, $bg, $label])
            <div
                class="legend-item {{ $filterStatus === $status ? 'active' : '' }}"
                wire:click="$set('filterStatus', '{{ $status }}')"
                style="background:{{ $bg }};color:{{ $color }};"
            >
                <span class="legend-dot" style="background:{{ $color }};"></span>
                {{ $status }} ({{ $statusCounts[$status] ?? 0 }})
            </div>
            @endforeach
        </div>

        {{-- Floor Filter --}}
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;color:var(--gray-500);font-weight:500;">Lantai:</span>
            <select wire:model.live="filterFloor" class="form-select" style="width:auto;padding:7px 30px 7px 12px;">
                <option value="0">Semua</option>
                @foreach($floors as $floor)
                    <option value="{{ $floor }}">Lantai {{ $floor }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Room Grid per Floor --}}
    @forelse($rooms as $floor => $floorRooms)
    <div class="floor-section">
        <div class="floor-label">🏢 Lantai {{ $floor }}</div>
        <div class="room-board-grid">
            @foreach($floorRooms as $room)
            <div
                class="room-card status-{{ $room->status }}"
                wire:click="selectRoom({{ $room->id }})"
                id="room-{{ $room->room_number }}"
                title="{{ $room->status_label }}"
            >
                <div class="room-card-number">{{ $room->room_number }}</div>
                <div class="room-card-type">{{ $room->roomType->code }}</div>
                <div class="room-status-badge {{ $room->status }}">
                    <span class="status-dot"></span>
                    {{ $room->status }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <div class="empty-state-title">Tidak ada kamar ditemukan</div>
        <div class="empty-state-text">Coba ubah filter di atas</div>
    </div>
    @endforelse

    {{-- Room Detail Modal --}}
    @if($showModal && $selectedRoom)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal modal-md animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">🏠 Kamar {{ $selectedRoom->room_number }}</span>
                <button class="modal-close" wire:click="closeModal" id="btn-close-room-modal">✕</button>
            </div>
            <div class="modal-body">
                {{-- Room Info --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                        <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Lantai</div>
                        <div style="font-size:18px;font-weight:800;color:var(--navy-900);">{{ $selectedRoom->floor }}</div>
                    </div>
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                        <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Tipe</div>
                        <div style="font-size:18px;font-weight:800;color:var(--navy-900);">{{ $selectedRoom->roomType->name }}</div>
                    </div>
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                        <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Kapasitas</div>
                        <div style="font-size:18px;font-weight:800;color:var(--navy-900);">{{ $selectedRoom->roomType->capacity }} Pax</div>
                    </div>
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                        <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Harga/Malam</div>
                        <div style="font-size:15px;font-weight:800;color:var(--navy-900);">Rp {{ number_format($selectedRoom->roomType->base_price, 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Status Update --}}
                <div class="divider"></div>
                <div class="form-group">
                    <label class="form-label">Update Status Kamar</label>
                    <select wire:model="newStatus" class="form-select" id="select-room-status">
                        <option value="VC">VC — Vacant Clean (Kosong, Bersih)</option>
                        <option value="VD">VD — Vacant Dirty (Kosong, Kotor)</option>
                        <option value="OC">OC — Occupied Clean (Terisi, Bersih)</option>
                        <option value="OD">OD — Occupied Dirty (Terisi, Kotor)</option>
                        <option value="OOO">OOO — Out of Order (Rusak)</option>
                        <option value="OOS">OOS — Out of Service (Tidak Tersedia)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea wire:model="statusNotes" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeModal" id="btn-cancel-room-update">Batal</button>
                <button class="btn btn-primary" wire:click="updateStatus" id="btn-save-room-status">
                    💾 Simpan Status
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
