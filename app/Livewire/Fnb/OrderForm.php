<?php

namespace App\Livewire\Fnb;

use App\Models\CheckIn;
use App\Models\FnbCategory;
use App\Models\FnbMenu;
use App\Models\FnbOrder;
use App\Models\FnbOrderItem;
use App\Models\FolioItem;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Order F&B Baru')]
class OrderForm extends Component
{
    public ?int $room_id = null;
    public string $order_type = 'room_service';
    public string $notes = '';

    public array $cartItems = []; // ['menu_id' => x, 'name' => y, 'qty' => 1, 'price' => z, 'item_notes' => '']

    public function addToCart(int $menuId): void
    {
        $menu = FnbMenu::find($menuId);
        if (!$menu || !$menu->is_available) return;

        $existing = collect($this->cartItems)->search(fn($i) => $i['menu_id'] === $menuId);
        if ($existing !== false) {
            $this->cartItems[$existing]['qty']++;
        } else {
            $this->cartItems[] = [
                'menu_id' => $menuId,
                'name' => $menu->name,
                'qty' => 1,
                'price' => $menu->price,
                'item_notes' => '',
            ];
        }
    }

    public function removeFromCart(int $index): void
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
    }

    public function getCartTotalProperty(): float
    {
        return collect($this->cartItems)->sum(fn($i) => $i['qty'] * $i['price']);
    }

    public function submitOrder()
    {
        $this->validate([
            'cartItems' => 'required|array|min:1',
        ], ['cartItems.required' => 'Pilih minimal 1 item menu.', 'cartItems.min' => 'Pilih minimal 1 item menu.']);

        // Find folio for room service
        $checkIn = null;
        $folio = null;
        if ($this->room_id) {
            $checkIn = CheckIn::whereHas('reservation', fn($q) => $q->where('room_id', $this->room_id)->where('status', 'checked_in'))->with('folio')->first();
            $folio = $checkIn?->folio;
        }

        $order = FnbOrder::create([
            'order_number' => FnbOrder::generateOrderNumber(),
            'check_in_id' => $checkIn?->id,
            'folio_id' => $folio?->id,
            'room_id' => $this->room_id,
            'order_type' => $this->order_type,
            'status' => 'pending',
            'total' => $this->cartTotal,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->cartItems as $item) {
            FnbOrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $item['qty'] * $item['price'],
                'notes' => $item['item_notes'],
            ]);
        }

        // Auto-add to folio if room service with active guest
        if ($folio) {
            FolioItem::create([
                'folio_id' => $folio->id,
                'type' => 'fnb',
                'description' => 'F&B Order #' . $order->order_number . ' (' . collect($this->cartItems)->count() . ' item)',
                'qty' => 1,
                'unit_price' => $this->cartTotal,
                'subtotal' => $this->cartTotal,
                'item_date' => now()->toDateString(),
            ]);
            $folio->recalculate();
        }

        session()->flash('success', 'Order #' . $order->order_number . ' berhasil dibuat!');
        $this->redirectRoute('fnb.orders');
    }

    public function render()
    {
        return view('livewire.fnb.order-form', [
            'categories' => FnbCategory::with(['menus' => fn($q) => $q->where('is_available', true)])->where('is_active', true)->get(),
            'occupiedRooms' => Room::where('status', 'OC')->with('roomType')->get(),
        ]);
    }
}
