<?php

namespace App\Livewire\Guests;

use App\Models\Guest;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Tamu')]
class GuestList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $id_card_type = 'KTP';
    public string $id_card_number = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $nationality = 'Indonesia';
    public string $gender = '';
    public string $notes = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void { $this->resetForm(); $this->showModal = true; }

    public function openEdit(int $id): void
    {
        $g = Guest::findOrFail($id);
        $this->editingId = $id;
        $this->name = $g->name;
        $this->id_card_type = $g->id_card_type;
        $this->id_card_number = $g->id_card_number ?? '';
        $this->phone = $g->phone ?? '';
        $this->email = $g->email ?? '';
        $this->address = $g->address ?? '';
        $this->nationality = $g->nationality;
        $this->gender = $g->gender ?? '';
        $this->notes = $g->notes ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|max:100', 'id_card_type' => 'required']);

        $data = [
            'name' => $this->name,
            'id_card_type' => $this->id_card_type,
            'id_card_number' => $this->id_card_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'nationality' => $this->nationality,
            'gender' => $this->gender ?: null,
            'notes' => $this->notes,
        ];

        if ($this->editingId) {
            Guest::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Data tamu berhasil diperbarui.');
        } else {
            Guest::create($data);
            session()->flash('success', 'Tamu berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function closeModal(): void { $this->showModal = false; $this->resetForm(); }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->id_card_type = 'KTP';
        $this->id_card_number = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->nationality = 'Indonesia';
        $this->gender = '';
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.guests.guest-list', [
            'guests' => Guest::when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('id_card_number', 'like', '%'.$this->search.'%')->orWhere('phone', 'like', '%'.$this->search.'%'))
                ->withCount('reservations')
                ->latest()
                ->paginate(15),
        ]);
    }
}
