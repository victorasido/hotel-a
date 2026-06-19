<?php

namespace App\Livewire\MasterData;

use App\Models\Room;
use App\Models\RoomType;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Data Kamar')]
class Rooms extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $search = '';
    public string $filterFloor = '';
    public string $filterType = '';

    public string $room_number = '';
    public int $floor = 1;
    public int $room_type_id = 0;
    public string $status = 'VC';
    public string $notes = '';
    public bool $is_active = true;

    public function openCreate(): void { $this->resetForm(); $this->showModal = true; }

    public function openEdit(int $id): void
    {
        $room = Room::findOrFail($id);
        $this->editingId = $id;
        $this->room_number = $room->room_number;
        $this->floor = $room->floor;
        $this->room_type_id = $room->room_type_id;
        $this->status = $room->status;
        $this->notes = $room->notes ?? '';
        $this->is_active = $room->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'room_number' => 'required|max:10',
            'floor' => 'required|integer|min:1',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:VC,VD,OC,OD,OOO,OOS',
        ]);

        $data = [
            'room_number' => $this->room_number,
            'floor' => $this->floor,
            'room_type_id' => $this->room_type_id,
            'status' => $this->status,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Room::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Data kamar berhasil diperbarui.');
        } else {
            Room::create($data);
            session()->flash('success', 'Kamar berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Room::findOrFail($id)->delete();
        session()->flash('success', 'Kamar berhasil dihapus.');
    }

    public function closeModal(): void { $this->showModal = false; $this->resetForm(); }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->room_number = '';
        $this->floor = 1;
        $this->room_type_id = 0;
        $this->status = 'VC';
        $this->notes = '';
        $this->is_active = true;
    }

    public function render()
    {
        $rooms = Room::with('roomType')
            ->when($this->search, fn($q) => $q->where('room_number', 'like', '%'.$this->search.'%'))
            ->when($this->filterFloor, fn($q) => $q->where('floor', $this->filterFloor))
            ->when($this->filterType, fn($q) => $q->where('room_type_id', $this->filterType))
            ->orderBy('floor')->orderBy('room_number')
            ->get();

        return view('livewire.master-data.rooms', [
            'rooms' => $rooms,
            'roomTypes' => RoomType::where('is_active', true)->get(),
            'floors' => Room::distinct()->pluck('floor')->sort(),
        ]);
    }
}
