<?php

namespace App\Livewire\MasterData;

use App\Models\RoomType;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Tipe Kamar')]
class RoomTypes extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $code = '';
    public string $name = '';
    public string $description = '';
    public string $base_price = '';
    public string $seasonal_price = '';
    public int $capacity = 2;
    public string $facilities = '';
    public bool $is_active = true;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $rt = RoomType::findOrFail($id);
        $this->editingId = $id;
        $this->code = $rt->code;
        $this->name = $rt->name;
        $this->description = $rt->description ?? '';
        $this->base_price = $rt->base_price;
        $this->seasonal_price = $rt->seasonal_price ?? '';
        $this->capacity = $rt->capacity;
        $this->facilities = $rt->facilities ?? '';
        $this->is_active = $rt->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'code' => 'required|max:10',
            'name' => 'required|max:100',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
        ]);

        $data = [
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'seasonal_price' => $this->seasonal_price ?: null,
            'capacity' => $this->capacity,
            'facilities' => $this->facilities,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            RoomType::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Tipe kamar berhasil diperbarui.');
        } else {
            RoomType::create($data);
            session()->flash('success', 'Tipe kamar berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        RoomType::findOrFail($id)->delete();
        session()->flash('success', 'Tipe kamar berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->base_price = '';
        $this->seasonal_price = '';
        $this->capacity = 2;
        $this->facilities = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.master-data.room-types', [
            'roomTypes' => RoomType::withCount('rooms')->get(),
        ]);
    }
}
