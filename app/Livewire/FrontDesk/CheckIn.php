<?php

namespace App\Livewire\FrontDesk;

use App\Models\CheckIn as CheckInModel;
use App\Models\GuestFolio;
use App\Models\FolioItem;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Check-in')]
class CheckIn extends Component
{
    public Reservation $reservation;
    public string $notes = '';
    public int $extra_pax = 0;

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation->load(['guest', 'room.roomType']);

        if (!in_array($reservation->status, ['confirmed', 'pending'])) {
            session()->flash('error', 'Reservasi ini tidak dapat di-check-in.');
            redirect()->route('reservations.index');
        }
    }

    public function processCheckIn()
    {
        $reservation = $this->reservation;

        // 1. Create CheckInModel record
        $checkIn = CheckInModel::create([
            'reservation_id' => $reservation->id,
            'room_id' => $reservation->room_id,
            'guest_id' => $reservation->guest_id,
            'actual_check_in' => now(),
            'extra_pax' => $this->extra_pax,
            'notes' => $this->notes,
            'checked_in_by' => auth()->id(),
        ]);

        // 2. Update reservation status
        $reservation->update(['status' => 'checked_in']);

        // 3. Update room status to OC
        $reservation->room->update(['status' => 'OC']);

        // 4. Create Guest Folio
        $folio = GuestFolio::create([
            'folio_number' => GuestFolio::generateFolioNumber(),
            'check_in_id' => $checkIn->id,
            'guest_id' => $reservation->guest_id,
            'status' => 'open',
            'grand_total' => $reservation->total_amount,
        ]);

        // 5. Add room charge to folio
        $nights = $reservation->check_in_date->diffInDays($reservation->check_out_date);
        FolioItem::create([
            'folio_id' => $folio->id,
            'type' => 'room',
            'description' => 'Biaya Kamar ' . $reservation->room->room_number . ' (' . $reservation->room->roomType->name . ') — ' . $nights . ' malam',
            'qty' => $nights,
            'unit_price' => $reservation->room_rate,
            'subtotal' => $reservation->total_amount,
            'item_date' => now()->toDateString(),
        ]);

        session()->flash('success', 'Check-in berhasil! Folio tamu telah dibuat.');
        $this->redirectRoute('folio', $folio);
    }

    public function render()
    {
        return view('livewire.front-desk.check-in');
    }
}
