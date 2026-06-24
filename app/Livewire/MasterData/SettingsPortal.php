<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Pengaturan Hotel')]
class SettingsPortal extends Component
{
    public string $activeTab = 'room-types';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.master-data.settings-portal');
    }
}
