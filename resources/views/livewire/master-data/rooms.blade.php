<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Data Kamar</h1><p>Kelola inventaris kamar hotel.</p></div>
        <button class="btn btn-primary" wire:click="openCreate" id="btn-add-room">+ Tambah Kamar</button>
    </div>

    {{-- Filters --}}
    <div class="search-filter-bar">
        <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Cari nomor kamar...">
        </div>
        <select class="form-select" wire:model.live="filterFloor" style="width:auto;">
            <option value="">Semua Lantai</option>
            @foreach($floors as $f) <option value="{{ $f }}">Lantai {{ $f }}</option> @endforeach
        </select>
        <select class="form-select" wire:model.live="filterType" style="width:auto;">
            <option value="">Semua Tipe</option>
            @foreach($roomTypes as $rt) <option value="{{ $rt->id }}">{{ $rt->name }}</option> @endforeach
        </select>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. Kamar</th>
                        <th>Lantai</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td><strong>{{ $room->room_number }}</strong></td>
                        <td>{{ $room->floor }}</td>
                        <td><span class="badge badge-primary">{{ $room->roomType->name }}</span></td>
                        <td><span class="room-status-badge {{ $room->status }}">{{ $room->status }}</span></td>
                        <td><span class="badge {{ $room->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $room->is_active ? 'Ya' : 'Tidak' }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-outline btn-sm" wire:click="openEdit({{ $room->id }})" id="btn-edit-room-{{ $room->id }}">✏️</button>
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $room->id }})" wire:confirm="Yakin hapus kamar ini?" id="btn-del-room-{{ $room->id }}">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">🔍</div><div class="empty-state-title">Tidak ada kamar ditemukan</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal modal-md animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">{{ $editingId ? 'Edit' : 'Tambah' }} Kamar</span>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nomor Kamar<span class="required">*</span></label>
                        <input type="text" class="form-control" wire:model="room_number" placeholder="101">
                        @error('room_number') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lantai<span class="required">*</span></label>
                        <input type="number" class="form-control" wire:model="floor" min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Kamar<span class="required">*</span></label>
                    <select class="form-select" wire:model="room_type_id">
                        <option value="0">-- Pilih Tipe --</option>
                        @foreach($roomTypes as $rt) <option value="{{ $rt->id }}">{{ $rt->name }} ({{ $rt->code }})</option> @endforeach
                    </select>
                    @error('room_type_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status Awal</label>
                    <select class="form-select" wire:model="status">
                        <option value="VC">Vacant Clean (VC)</option>
                        <option value="VD">Vacant Dirty (VD)</option>
                        <option value="OOO">Out of Order (OOO)</option>
                        <option value="OOS">Out of Service (OOS)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeModal">Batal</button>
                <button class="btn btn-primary" wire:click="save" id="btn-save-room">💾 Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
