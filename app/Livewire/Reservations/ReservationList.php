<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Daftar Reservasi')]
class ReservationList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterDate = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function cancelReservation(int $id): void
    {
        $reservation = Reservation::findOrFail($id);
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            session()->flash('error', 'Reservasi tidak dapat dibatalkan.');
            return;
        }
        $reservation->update(['status' => 'cancelled']);
        session()->flash('success', 'Reservasi berhasil dibatalkan.');
    }

    public function render()
    {
        $reservations = Reservation::with(['guest', 'room.roomType'])
            ->when($this->search, fn($q) => $q->where('booking_code', 'like', '%'.$this->search.'%')
                ->orWhereHas('guest', fn($gq) => $gq->where('name', 'like', '%'.$this->search.'%')))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDate, fn($q) => $q->whereDate('check_in_date', $this->filterDate))
            ->latest()
            ->paginate(15);

        return view('livewire.reservations.reservation-list', compact('reservations'));
    }
}
