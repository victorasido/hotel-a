<?php

namespace App\Livewire\Reservations;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Reservasi & Tamu')]
class ReservationsPortal extends Component
{
    public string $activeTab = 'reservations';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.reservations.reservations-portal');
    }
}
