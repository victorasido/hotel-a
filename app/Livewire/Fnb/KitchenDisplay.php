<?php

namespace App\Livewire\Fnb;

use App\Models\FnbOrder;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.kitchen')]
#[Title('Kitchen Display System')]
class KitchenDisplay extends Component
{
    public function updateStatus(int $id, string $status): void
    {
        $order = FnbOrder::findOrFail($id);
        $update = ['status' => $status];
        if ($status === 'served') $update['served_at'] = now();
        $order->update($update);
    }

    public function render()
    {
        return view('livewire.fnb.kitchen-display', [
            'pendingOrders' => FnbOrder::with(['room', 'items.menu'])->where('status', 'pending')->latest()->get(),
            'processingOrders' => FnbOrder::with(['room', 'items.menu'])->where('status', 'processing')->latest()->get(),
        ]);
    }
}
