<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1>Tipe Kamar</h1>
            <p>Kelola tipe kamar beserta harga dan fasilitas.</p>
        </div>
        <button class="btn btn-primary" wire:click="openCreate" id="btn-add-room-type">+ Tambah Tipe Kamar</button>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga Dasar</th>
                        <th>Harga Musim</th>
                        <th>Kapasitas</th>
                        <th>Jumlah Kamar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roomTypes as $rt)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;color:var(--navy-700);">{{ $rt->code }}</span></td>
                        <td><strong>{{ $rt->name }}</strong></td>
                        <td class="price-display">Rp {{ number_format($rt->base_price, 0, ',', '.') }}</td>
                        <td class="price-display">{{ $rt->seasonal_price ? 'Rp '.number_format($rt->seasonal_price, 0, ',', '.') : '—' }}</td>
                        <td>{{ $rt->capacity }} Pax</td>
                        <td><span class="badge badge-primary">{{ $rt->rooms_count }} kamar</span></td>
                        <td>
                            <span class="badge {{ $rt->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $rt->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-outline btn-sm" wire:click="openEdit({{ $rt->id }})" id="btn-edit-rt-{{ $rt->id }}">✏️</button>
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $rt->id }})" wire:confirm="Yakin hapus tipe kamar ini?" id="btn-del-rt-{{ $rt->id }}">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal modal-md animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">{{ $editingId ? 'Edit' : 'Tambah' }} Tipe Kamar</span>
                <button class="modal-close" wire:click="closeModal" id="btn-close-rt-modal">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Kode<span class="required">*</span></label>
                        <input type="text" class="form-control" wire:model="code" placeholder="STD" maxlength="10">
                        @error('code') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Tipe<span class="required">*</span></label>
                        <input type="text" class="form-control" wire:model="name" placeholder="Standard">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Harga Dasar (Rp)<span class="required">*</span></label>
                        <input type="number" class="form-control" wire:model="base_price" placeholder="350000">
                        @error('base_price') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Musim (Rp)</label>
                        <input type="number" class="form-control" wire:model="seasonal_price" placeholder="Opsional">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas (Pax)<span class="required">*</span></label>
                    <input type="number" class="form-control" wire:model="capacity" min="1" max="10">
                </div>
                <div class="form-group">
                    <label class="form-label">Fasilitas</label>
                    <input type="text" class="form-control" wire:model="facilities" placeholder="AC, TV, WiFi, Kamar Mandi Dalam">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" wire:model="description" rows="2"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" id="rt-is-active">
                    <label for="rt-is-active" style="font-size:13px;font-weight:600;color:var(--gray-700);">Tipe Kamar Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeModal" id="btn-cancel-rt">Batal</button>
                <button class="btn btn-primary" wire:click="save" id="btn-save-rt">💾 Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
