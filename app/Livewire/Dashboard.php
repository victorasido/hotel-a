<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\GuestFolio;
use App\Models\FnbOrder;
use App\Models\HousekeepingTask;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $activeTab = 'summary';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $today = now()->toDateString();
        $user = auth()->user();

        $stats = [];
        $recentReservations = collect();
        $pendingHkTasks = collect();
        $pendingFnbOrders = collect();

        if ($user->hasRole('Super Admin') || $user->hasRole('Front Office')) {
            $stats['vc'] = Room::where('status', 'VC')->count();
            $stats['oc'] = Room::where('status', 'OC')->count();
            $stats['vd'] = Room::where('status', 'VD')->count();
            $stats['check_in_today'] = Reservation::whereDate('check_in_date', $today)->whereIn('status', ['confirmed', 'pending'])->count();
            $stats['check_out_today'] = Reservation::whereDate('check_out_date', $today)->where('status', 'checked_in')->count();
            
            if ($user->hasRole('Super Admin')) {
                $stats['revenue_today'] = GuestFolio::whereDate('created_at', $today)->sum('grand_total');
                $stats['revenue_month'] = GuestFolio::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total');
            }

            $recentReservations = Reservation::with(['guest', 'room.roomType'])
                ->latest()
                ->take(5)
                ->get();
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('FnB')) {
            $stats['open_fnb_orders'] = FnbOrder::whereIn('status', ['pending', 'processing'])->count();
            $pendingFnbOrders = FnbOrder::with(['room'])
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Housekeeping')) {
            $stats['hk_pending'] = HousekeepingTask::where('status', 'pending')->count();
            $stats['vd'] = $stats['vd'] ?? Room::where('status', 'VD')->count();
            
            $pendingHkTasks = HousekeepingTask::with('room')
                ->where('status', 'pending')
                ->where('priority', 'urgent')
                ->take(5)
                ->get();
        }

        return view('livewire.dashboard', compact('stats', 'recentReservations', 'pendingHkTasks', 'pendingFnbOrders'));
    }
}
