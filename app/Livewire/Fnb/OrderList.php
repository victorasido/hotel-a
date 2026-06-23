<?php

namespace App\Livewire\Fnb;

use App\Models\FnbOrder;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Daftar Order F&B')]
class OrderList extends Component
{
    use WithPagination;
    public string $filterStatus = '';

    public function updateStatus(int $id, string $status): void
    {
        $order = FnbOrder::findOrFail($id);
        $update = ['status' => $status];
        if ($status === 'served') $update['served_at'] = now();
        $order->update($update);
        session()->flash('success', 'Status order diperbarui.');
    }

    public function render()
    {
        return view('livewire.fnb.order-list', [
            'orders' => FnbOrder::with(['room', 'items.menu', 'createdBy'])
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->latest()->paginate(15),
        ]);
    }
}
