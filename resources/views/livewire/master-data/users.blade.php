<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div><h1>Pengguna Sistem</h1><p>Kelola akun pengguna dan hak akses.</p></div>
        <button class="btn btn-primary" wire:click="openCreate" id="btn-add-user">+ Tambah Pengguna</button>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--navy-700),var(--navy-400));display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:13px;">{{ substr($user->name,0,1) }}</div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge {{ $role->name === 'Super Admin' ? 'badge-gold' : ($role->name === 'FnB' ? 'badge-success' : 'badge-primary') }}">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-outline btn-sm" wire:click="openEdit({{ $user->id }})" id="btn-edit-user-{{ $user->id }}">✏️</button>
                                @if($user->id !== auth()->id())
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $user->id }})" wire:confirm="Yakin hapus pengguna ini?" id="btn-del-user-{{ $user->id }}">🗑️</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal modal-md animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">{{ $editingId ? 'Edit' : 'Tambah' }} Pengguna</span>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap<span class="required">*</span></label>
                    <input type="text" class="form-control" wire:model="name" placeholder="Nama pengguna">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email<span class="required">*</span></label>
                    <input type="email" class="form-control" wire:model="email" placeholder="email@hotel.com">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password {{ $editingId ? '(kosongkan jika tidak diubah)' : '' }}<span class="required">*</span></label>
                    <input type="password" class="form-control" wire:model="password" placeholder="Minimal 6 karakter">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Role<span class="required">*</span></label>
                    <select class="form-select" wire:model="role">
                        <option value="Super Admin">Super Admin</option>
                        <option value="FnB">F&B</option>
                        <option value="Housekeeping">Housekeeping</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeModal">Batal</button>
                <button class="btn btn-primary" wire:click="save" id="btn-save-user">💾 Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
