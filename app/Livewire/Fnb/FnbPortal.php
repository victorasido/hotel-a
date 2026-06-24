<?php

namespace App\Livewire\Fnb;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Layanan F&B')]
class FnbPortal extends Component
{
    public string $activeTab = 'orders';

    public function mount(): void
    {
        $user = auth()->user();

        // Front Office hanya boleh lihat orders
        if ($user->hasRole('Front Office') && $this->activeTab !== 'orders') {
            $this->activeTab = 'orders';
        }

        // Default tab untuk FnB/Admin adalah orders
        if ($user->hasRole('FnB') || $user->hasRole('Super Admin')) {
            $this->activeTab = $this->activeTab;
        }
    }

    public function setTab(string $tab): void
    {
        $user = auth()->user();

        // Front Office tidak boleh akses tab kitchen/menu
        if ($user->hasRole('Front Office') && in_array($tab, ['kitchen', 'menu'])) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.fnb.fnb-portal');
    }
}
