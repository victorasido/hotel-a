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
    public function render()
    {
        $today = now()->toDateString();

        $stats = [
            'vc' => Room::where('status', 'VC')->count(),
            'oc' => Room::where('status', 'OC')->count(),
            'vd' => Room::where('status', 'VD')->count(),
            'ooo' => Room::whereIn('status', ['OOO', 'OOS'])->count(),
            'total_rooms' => Room::count(),
            'check_in_today' => Reservation::whereDate('check_in_date', $today)->whereIn('status', ['confirmed', 'pending'])->count(),
            'check_out_today' => Reservation::whereDate('check_out_date', $today)->where('status', 'checked_in')->count(),
            'revenue_today' => GuestFolio::whereDate('created_at', $today)->sum('grand_total'),
            'revenue_month' => GuestFolio::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total'),
            'open_fnb_orders' => FnbOrder::whereIn('status', ['pending', 'processing'])->count(),
            'hk_pending' => HousekeepingTask::where('status', 'pending')->count(),
        ];

        $recentReservations = Reservation::with(['guest', 'room.roomType'])
            ->latest()
            ->take(5)
            ->get();

        $pendingHkTasks = HousekeepingTask::with('room')
            ->where('status', 'pending')
            ->where('priority', 'urgent')
            ->take(3)
            ->get();

        $pendingFnbOrders = FnbOrder::with(['room'])
            ->where('status', 'pending')
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.dashboard', compact('stats', 'recentReservations', 'pendingHkTasks', 'pendingFnbOrders'));
    }
}
