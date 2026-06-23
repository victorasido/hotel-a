<?php

namespace App\Livewire\Reservations;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Buat Reservasi')]
class ReservationForm extends Component
{
    public ?int $reservationId = null;

    // Guest selection
    public string $guestSearch = '';
    public ?int $guest_id = null;
    public string $guestName = '';
    public bool $showGuestList = false;
    public bool $showNewGuestForm = false;

    // New guest fields
    public string $new_guest_name = '';
    public string $new_guest_id_card_type = 'KTP';
    public string $new_guest_id_card_number = '';
    public string $new_guest_phone = '';
    public string $new_guest_nationality = 'Indonesia';

    // Room selection
    public ?int $room_type_id = null;
    public ?int $room_id = null;

    // Dates & calculation
    public string $check_in_date = '';
    public string $check_out_date = '';
    public int $nights = 0;
    public float $room_rate = 0;
    public float $total_amount = 0;

    // Other fields
    public int $pax = 1;
    public string $source = 'walk_in';
    public float $deposit = 0;
    public string $special_request = '';
    public string $notes = '';

    public function mount(?Reservation $reservation = null): void
    {
        $this->check_in_date = now()->format('Y-m-d');
        $this->check_out_date = now()->addDay()->format('Y-m-d');

        if ($reservation && $reservation->exists) {
            $this->reservationId = $reservation->id;
            $this->guest_id = $reservation->guest_id;
            $this->guestName = $reservation->guest->name;
            $this->room_type_id = $reservation->room->room_type_id;
            $this->room_id = $reservation->room_id;
            $this->check_in_date = $reservation->check_in_date->format('Y-m-d');
            $this->check_out_date = $reservation->check_out_date->format('Y-m-d');
            $this->pax = $reservation->pax;
            $this->source = $reservation->source;
            $this->deposit = $reservation->deposit;
            $this->special_request = $reservation->special_request ?? '';
            $this->notes = $reservation->notes ?? '';
            $this->room_rate = $reservation->room_rate;
            $this->total_amount = $reservation->total_amount;
            $this->calculateTotal();
        }
    }

    public function searchGuest(): void
    {
        $this->showGuestList = strlen($this->guestSearch) >= 2;
    }

    public function selectGuest(int $id): void
    {
        $guest = Guest::findOrFail($id);
        $this->guest_id = $guest->id;
        $this->guestName = $guest->name;
        $this->guestSearch = $guest->name;
        $this->showGuestList = false;
    }

    public function createNewGuest(): void
    {
        $this->validate([
            'new_guest_name' => 'required|min:2',
        ]);

        $guest = Guest::create([
            'name' => $this->new_guest_name,
            'id_card_type' => $this->new_guest_id_card_type,
            'id_card_number' => $this->new_guest_id_card_number,
            'phone' => $this->new_guest_phone,
            'nationality' => $this->new_guest_nationality,
        ]);

        $this->selectGuest($guest->id);
        $this->showNewGuestForm = false;
    }

    public function updatedRoomTypeId(): void
    {
        $this->room_id = null;
        $this->room_rate = 0;
        $this->calculateTotal();
    }

    public function updatedRoomId(): void
    {
        if ($this->room_id) {
            $room = Room::with('roomType')->find($this->room_id);
            if ($room) {
                $this->room_rate = $room->roomType->seasonal_price ?? $room->roomType->base_price;
                $this->calculateTotal();
            }
        }
    }

    public function updatedCheckInDate(): void { $this->calculateTotal(); }
    public function updatedCheckOutDate(): void { $this->calculateTotal(); }

    public function calculateTotal(): void
    {
        if ($this->check_in_date && $this->check_out_date) {
            $in = \Carbon\Carbon::parse($this->check_in_date);
            $out = \Carbon\Carbon::parse($this->check_out_date);
            $this->nights = max(1, $in->diffInDays($out));
            $this->total_amount = $this->nights * $this->room_rate;
        }
    }

    public function save(): mixed
    {
        $this->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'pax' => 'required|integer|min:1',
        ], [
            'guest_id.required' => 'Pilih tamu terlebih dahulu.',
            'room_id.required' => 'Pilih kamar terlebih dahulu.',
            'check_out_date.after' => 'Tanggal check-out harus setelah check-in.',
        ]);

        $data = [
            'guest_id' => $this->guest_id,
            'room_id' => $this->room_id,
            'check_in_date' => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'pax' => $this->pax,
            'status' => 'confirmed',
            'source' => $this->source,
            'room_rate' => $this->room_rate,
            'total_amount' => $this->total_amount,
            'deposit' => $this->deposit,
            'special_request' => $this->special_request,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ];

        if ($this->reservationId) {
            Reservation::findOrFail($this->reservationId)->update($data);
            session()->flash('success', 'Reservasi berhasil diperbarui.');
        } else {
            $data['booking_code'] = Reservation::generateBookingCode();
            Reservation::create($data);
            session()->flash('success', 'Reservasi berhasil dibuat.');
        }

        $this->redirectRoute('reservations.index');
    }

    public function render()
    {
        $guestResults = [];
        if ($this->showGuestList && strlen($this->guestSearch) >= 2) {
            $guestResults = Guest::where('name', 'like', '%'.$this->guestSearch.'%')
                ->orWhere('id_card_number', 'like', '%'.$this->guestSearch.'%')
                ->limit(5)->get();
        }

        $availableRooms = [];
        if ($this->room_type_id && $this->check_in_date && $this->check_out_date) {
            // Rooms of selected type not reserved for overlapping dates
            $bookedRoomIds = Reservation::where('status', '!=', 'cancelled')
                ->where(function($q) {
                    $q->whereBetween('check_in_date', [$this->check_in_date, $this->check_out_date])
                      ->orWhereBetween('check_out_date', [$this->check_in_date, $this->check_out_date])
                      ->orWhere(fn($q2) => $q2->where('check_in_date', '<=', $this->check_in_date)->where('check_out_date', '>=', $this->check_out_date));
                })
                ->when($this->reservationId, fn($q) => $q->where('id', '!=', $this->reservationId))
                ->pluck('room_id');

            $availableRooms = Room::where('room_type_id', $this->room_type_id)
                ->where('is_active', true)
                ->whereNotIn('id', $bookedRoomIds)
                ->whereIn('status', ['VC', 'VD'])
                ->get();
        }

        return view('livewire.reservations.reservation-form', [
            'guestResults' => $guestResults,
            'roomTypes' => RoomType::where('is_active', true)->get(),
            'availableRooms' => $availableRooms,
        ]);
    }
}
