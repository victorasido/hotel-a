<?php

namespace App\Livewire\MasterData;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Pengguna')]
class Users extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'Housekeeping';

    public function openCreate(): void { $this->resetForm(); $this->showModal = true; }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->getRoleNames()->first() ?? 'Housekeeping';
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email' . ($this->editingId ? ','.$this->editingId : ''),
            'role' => 'required|in:Super Admin,FnB,Housekeeping',
        ];

        if (!$this->editingId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update(['name' => $this->name, 'email' => $this->email]);
            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
            $user->syncRoles([$this->role]);
            session()->flash('success', 'Pengguna berhasil diperbarui.');
        } else {
            $user = User::create(['name' => $this->name, 'email' => $this->email, 'password' => Hash::make($this->password)]);
            $user->assignRole($this->role);
            session()->flash('success', 'Pengguna berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }
        User::findOrFail($id)->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
    }

    public function closeModal(): void { $this->showModal = false; $this->resetForm(); }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'Housekeeping';
    }

    public function render()
    {
        return view('livewire.master-data.users', [
            'users' => User::with('roles')->get(),
            'roles' => Role::all(),
        ]);
    }
}
