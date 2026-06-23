<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Detail Reservasi')]
class ReservationDetail extends Component
{
    public Reservation $reservation;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation->load(['guest', 'room.roomType', 'checkIn.folio', 'createdBy']);
    }

    public function render()
    {
        return view('livewire.reservations.reservation-detail');
    }
}
