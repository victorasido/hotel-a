<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Menu F&B</h1><p>Kelola menu makanan dan minuman.</p></div>
        <button class="btn btn-primary" wire:click="openCreate" id="btn-add-menu">+ Tambah Menu</button>
    </div>

    @foreach($categories as $cat)
    <div class="card mb-4" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">{{ $cat->icon }} {{ $cat->name }}</span>
            <span class="badge badge-primary">{{ $cat->menus->count() }} item</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Nama Menu</th><th>Deskripsi</th><th>Harga</th><th>Tersedia</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($cat->menus as $menu)
                    <tr>
                        <td><strong>{{ $menu->name }}</strong></td>
                        <td style="max-width:200px;color:var(--gray-500);font-size:12px;">{{ $menu->description ?? '—' }}</td>
                        <td class="price-display" style="font-weight:700;">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td>
                            <button
                                wire:click="toggleAvailability({{ $menu->id }})"
                                class="badge {{ $menu->is_available ? 'badge-success' : 'badge-secondary' }}"
                                style="border:none;cursor:pointer;"
                                id="btn-toggle-menu-{{ $menu->id }}"
                            >
                                {{ $menu->is_available ? '✅ Tersedia' : '⏸️ Tidak Tersedia' }}
                            </button>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-outline btn-sm" wire:click="openEdit({{ $menu->id }})" id="btn-edit-menu-{{ $menu->id }}">✏️</button>
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $menu->id }})" wire:confirm="Hapus menu ini?" id="btn-del-menu-{{ $menu->id }}">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--gray-400);padding:20px;">Belum ada menu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal modal-md animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">{{ $editingId ? 'Edit' : 'Tambah' }} Menu</span>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kategori<span class="required">*</span></label>
                    <select class="form-select" wire:model="category_id">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option> @endforeach
                    </select>
                    @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Menu<span class="required">*</span></label>
                    <input type="text" class="form-control" wire:model="name" placeholder="Nasi Goreng Spesial">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Harga (Rp)<span class="required">*</span></label>
                    <input type="number" class="form-control" wire:model="price" placeholder="35000">
                    @error('price') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" wire:model="description" rows="2"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_available" id="menu-available">
                    <label for="menu-available" style="font-size:13px;font-weight:600;">Menu Tersedia</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeModal">Batal</button>
                <button class="btn btn-primary" wire:click="save" id="btn-save-menu">💾 Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
