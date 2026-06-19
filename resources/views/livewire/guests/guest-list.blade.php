<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Data Tamu</h1><p>Kelola informasi tamu hotel.</p></div>
        <button class="btn btn-primary" wire:click="openCreate" id="btn-add-guest">+ Tambah Tamu</button>
    </div>

    <div class="search-filter-bar">
        <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Cari nama, no. KTP, atau telepon...">
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Nama</th><th>Identitas</th><th>Kontak</th><th>Kewarganegaraan</th><th>Total Reservasi</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $guest->name }}</div>
                            @if($guest->gender) <div style="font-size:11px;color:var(--gray-400);">{{ $guest->gender }}</div> @endif
                        </td>
                        <td>
                            <div style="font-size:11px;color:var(--gray-500);">{{ $guest->id_card_type }}</div>
                            <div style="font-family:monospace;font-size:12px;">{{ $guest->id_card_number ?? '—' }}</div>
                        </td>
                        <td>
                            <div>{{ $guest->phone ?? '—' }}</div>
                            <div style="font-size:12px;color:var(--gray-400);">{{ $guest->email ?? '' }}</div>
                        </td>
                        <td>{{ $guest->nationality }}</td>
                        <td><span class="badge badge-primary">{{ $guest->reservations_count }}x</span></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-outline btn-sm" wire:click="openEdit({{ $guest->id }})" id="btn-edit-guest-{{ $guest->id }}">✏️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">👥</div><div class="empty-state-title">Belum ada data tamu</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guests->hasPages())
        <div class="card-footer">{{ $guests->links() }}</div>
        @endif
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal modal-lg animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">{{ $editingId ? 'Edit' : 'Tambah' }} Data Tamu</span>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Nama Lengkap<span class="required">*</span></label>
                        <input type="text" class="form-control" wire:model="name" placeholder="Nama lengkap tamu">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Identitas</label>
                        <select class="form-select" wire:model="id_card_type">
                            <option value="KTP">KTP</option>
                            <option value="Passport">Passport</option>
                            <option value="SIM">SIM</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Identitas</label>
                        <input type="text" class="form-control" wire:model="id_card_number" placeholder="Nomor KTP/Passport">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" wire:model="phone" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" wire:model="email" placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kewarganegaraan</label>
                        <input type="text" class="form-control" wire:model="nationality">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" wire:model="gender">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" wire:model="address" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeModal">Batal</button>
                <button class="btn btn-primary" wire:click="save" id="btn-save-guest">💾 Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
