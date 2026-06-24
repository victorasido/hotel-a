<?php

namespace App\Livewire\FrontDesk;

use App\Models\CheckIn as CheckInModel;
use App\Models\CheckOut as CheckOutModel;
use App\Models\HousekeepingTask;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Check-out')]
class CheckOut extends Component
{
    public CheckInModel $checkIn;
    public string $payment_method = 'cash';
    public string $notes = '';

    public function mount(CheckInModel $checkIn): void
    {
        $this->checkIn = $checkIn->load(['reservation.room.roomType', 'guest', 'folio.items']);
    }

    public function processCheckOut()
    {
        $checkIn = $this->checkIn;
        $folio = $checkIn->folio;
        $grandTotal = $folio ? $folio->grand_total : 0;

        // 1. Create CheckOut record
        CheckOutModel::create([
            'check_in_id' => $checkIn->id,
            'actual_check_out' => now(),
            'total_paid' => $grandTotal,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'checked_out_by' => auth()->id(),
        ]);

        // 2. Close folio
        if ($folio) {
            $folio->update(['status' => 'closed']);
        }

        // 3. Update reservation status
        $checkIn->reservation->update(['status' => 'checked_out']);

        // 4. Update room status to VD
        $checkIn->reservation->room->update(['status' => 'VD']);

        // 5. Auto-create housekeeping task
        HousekeepingTask::create([
            'room_id' => $checkIn->reservation->room_id,
            'task_type' => 'cleaning',
            'status' => 'pending',
            'priority' => 'normal',
            'notes' => 'Auto-generated setelah check-out. Tamu: ' . $checkIn->guest->name,
            'requested_by' => auth()->id(),
        ]);

        session()->flash('success', 'Check-out berhasil! Kamar dikunci untuk pembersihan dan task HK dibuat otomatis.');
        $this->redirectRoute('reservations.index');
    }

    public function render()
    {
        return view('livewire.front-desk.check-out');
    }
}
