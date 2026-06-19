<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Room Status Board')]
class RoomStatusBoard extends Component
{
    public string $filterStatus = '';
    public int $filterFloor = 0;

    public bool $showModal = false;
    public ?Room $selectedRoom = null;
    public string $newStatus = '';
    public string $statusNotes = '';

    public function selectRoom(int $roomId): void
    {
        $this->selectedRoom = Room::with(['roomType', 'activeCheckIn.guest'])->find($roomId);
        $this->newStatus = $this->selectedRoom->status;
        $this->statusNotes = '';
        $this->showModal = true;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'newStatus' => 'required|in:VC,VD,OC,OD,OOO,OOS',
        ]);

        $this->selectedRoom->update([
            'status' => $this->newStatus,
            'notes' => $this->statusNotes ?: $this->selectedRoom->notes,
        ]);

        $this->showModal = false;
        $this->selectedRoom = null;
        session()->flash('success', 'Status kamar berhasil diperbarui.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedRoom = null;
    }

    public function render()
    {
        $query = Room::with('roomType')->orderBy('floor')->orderBy('room_number');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterFloor > 0) {
            $query->where('floor', $this->filterFloor);
        }

        $rooms = $query->get()->groupBy('floor');
        $floors = Room::distinct()->pluck('floor')->sort();

        $statusCounts = Room::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');

        return view('livewire.room-status-board', compact('rooms', 'floors', 'statusCounts'));
    }
}
