<?php

namespace App\Livewire\Billing;

use App\Models\FolioItem;
use App\Models\GuestFolio;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Detail Folio')]
class FolioDetail extends Component
{
    public GuestFolio $folio;

    public bool $showAddItem = false;
    public string $item_type = 'extra';
    public string $item_description = '';
    public int $item_qty = 1;
    public string $item_unit_price = '';

    public function mount(GuestFolio $folio): void
    {
        $this->folio = $folio->load(['checkIn.reservation.room.roomType', 'guest', 'items']);
    }

    public function addItem(): void
    {
        if ($this->folio->status === 'closed') {
            session()->flash('error', 'Folio sudah ditutup, tidak dapat menambah item.');
            return;
        }

        $this->validate([
            'item_description' => 'required',
            'item_qty' => 'required|integer|min:1',
            'item_unit_price' => 'required|numeric|min:0',
        ]);

        FolioItem::create([
            'folio_id' => $this->folio->id,
            'type' => $this->item_type,
            'description' => $this->item_description,
            'qty' => $this->item_qty,
            'unit_price' => $this->item_unit_price,
            'subtotal' => $this->item_qty * $this->item_unit_price,
            'item_date' => now()->toDateString(),
        ]);

        $this->folio->recalculate();
        $this->folio->refresh();

        $this->showAddItem = false;
        $this->resetItemForm();
        session()->flash('success', 'Item berhasil ditambahkan ke folio.');
    }

    public function removeItem(int $itemId): void
    {
        if ($this->folio->status === 'closed') {
            session()->flash('error', 'Folio sudah ditutup.');
            return;
        }

        FolioItem::findOrFail($itemId)->delete();
        $this->folio->recalculate();
        $this->folio->refresh();
        session()->flash('success', 'Item dihapus.');
    }

    private function resetItemForm(): void
    {
        $this->item_type = 'extra';
        $this->item_description = '';
        $this->item_qty = 1;
        $this->item_unit_price = '';
    }

    public function render()
    {
        $this->folio->load('items');
        return view('livewire.billing.folio-detail');
    }
}
