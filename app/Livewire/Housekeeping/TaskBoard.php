<?php

namespace App\Livewire\Housekeeping;

use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Housekeeping Task Board')]
class TaskBoard extends Component
{
    public bool $showCreateModal = false;
    public ?int $room_id = null;
    public string $task_type = 'cleaning';
    public string $priority = 'normal';
    public ?int $assigned_to = null;
    public string $notes = '';

    public function createTask(): void
    {
        $this->validate([
            'room_id' => 'required|exists:rooms,id',
            'task_type' => 'required',
            'priority' => 'required|in:normal,urgent',
        ]);

        HousekeepingTask::create([
            'room_id' => $this->room_id,
            'task_type' => $this->task_type,
            'priority' => $this->priority,
            'assigned_to' => $this->assigned_to,
            'notes' => $this->notes,
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->resetTaskForm();
        session()->flash('success', 'Task housekeeping dibuat.');
    }

    public function moveTask(int $id, string $newStatus): void
    {
        $task = HousekeepingTask::findOrFail($id);
        $update = ['status' => $newStatus];

        if ($newStatus === 'in_progress') {
            $update['started_at'] = now();
        }

        if ($newStatus === 'done') {
            $update['completed_at'] = now();
            // Auto-update room status to VC
            $task->room->update(['status' => 'VC']);
            session()->flash('success', 'Task selesai! Status kamar ' . $task->room->room_number . ' diubah ke VC.');
        } else {
            session()->flash('success', 'Status task diperbarui.');
        }

        $task->update($update);
    }

    public function closeCreateModal(): void { $this->showCreateModal = false; $this->resetTaskForm(); }

    private function resetTaskForm(): void
    {
        $this->room_id = null;
        $this->task_type = 'cleaning';
        $this->priority = 'normal';
        $this->assigned_to = null;
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.housekeeping.task-board', [
            'pendingTasks' => HousekeepingTask::with(['room.roomType', 'assignedTo'])->where('status', 'pending')->orderBy('priority', 'desc')->orderBy('created_at')->get(),
            'inProgressTasks' => HousekeepingTask::with(['room.roomType', 'assignedTo'])->where('status', 'in_progress')->orderBy('created_at')->get(),
            'doneTasks' => HousekeepingTask::with(['room.roomType', 'assignedTo'])->where('status', 'done')->whereDate('completed_at', today())->orderBy('completed_at', 'desc')->get(),
            'dirtyRooms' => Room::whereIn('status', ['VD', 'OD'])->get(),
            'hkStaff' => User::role('Housekeeping')->get(),
        ]);
    }
}
