<div wire:poll.10s class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1>Housekeeping Task Board</h1>
            <p>Kelola tugas pembersihan dan pemeliharaan kamar. Auto-refresh tiap 10 detik.</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <div style="font-size:12px;color:var(--gray-400);">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--success);display:inline-block;animation:pulse-dot 2s infinite;"></span>
                Live — {{ now()->format('H:i:s') }}
            </div>
            <button class="btn btn-primary" wire:click="$set('showCreateModal', true)" id="btn-create-hk-task">+ Buat Task</button>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="kanban-board">

        {{-- Pending Column --}}
        <div class="kanban-column">
            <div class="kanban-column-header pending">
                <span>⏳ Pending</span>
                <span style="background:rgba(0,0,0,.1);padding:2px 10px;border-radius:20px;font-size:12px;">{{ $pendingTasks->count() }}</span>
            </div>
            <div class="kanban-column-body">
                @forelse($pendingTasks as $task)
                <div class="kanban-task-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                        <div class="kanban-task-room">{{ $task->room->room_number }}</div>
                        @if($task->priority === 'urgent')
                        <span class="badge badge-danger" style="font-size:10px;">🚨 URGENT</span>
                        @endif
                    </div>
                    <div class="kanban-task-type">{{ $task->task_type_label }} — {{ $task->room->roomType->name }}</div>
                    @if($task->assignedTo) <div style="font-size:11px;color:var(--gray-400);margin-bottom:8px;">👤 {{ $task->assignedTo->name }}</div> @endif
                    @if($task->notes) <div style="font-size:11px;color:var(--gray-500);margin-bottom:8px;font-style:italic;">📝 {{ $task->notes }}</div> @endif
                    <div style="font-size:10px;color:var(--gray-400);margin-bottom:10px;">{{ $task->created_at->diffForHumans() }}</div>
                    <button class="btn btn-primary btn-sm w-full" wire:click="moveTask({{ $task->id }}, 'in_progress')" id="btn-hk-start-{{ $task->id }}">▶ Mulai</button>
                </div>
                @empty
                <div class="empty-state" style="padding:30px 10px;"><div class="empty-state-icon" style="font-size:28px;">✅</div><div class="empty-state-text">Tidak ada task pending</div></div>
                @endforelse
            </div>
        </div>

        {{-- In Progress Column --}}
        <div class="kanban-column">
            <div class="kanban-column-header in_progress">
                <span>🔧 Sedang Dikerjakan</span>
                <span style="background:rgba(0,0,0,.1);padding:2px 10px;border-radius:20px;font-size:12px;">{{ $inProgressTasks->count() }}</span>
            </div>
            <div class="kanban-column-body">
                @forelse($inProgressTasks as $task)
                <div class="kanban-task-card" style="border-left:3px solid var(--navy-500);">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                        <div class="kanban-task-room">{{ $task->room->room_number }}</div>
                        @if($task->priority === 'urgent') <span class="badge badge-danger" style="font-size:10px;">🚨 URGENT</span> @endif
                    </div>
                    <div class="kanban-task-type">{{ $task->task_type_label }} — {{ $task->room->roomType->name }}</div>
                    @if($task->assignedTo) <div style="font-size:11px;color:var(--gray-400);margin-bottom:8px;">👤 {{ $task->assignedTo->name }}</div> @endif
                    @if($task->started_at) <div style="font-size:10px;color:var(--navy-500);margin-bottom:10px;">Mulai: {{ $task->started_at->format('H:i') }}</div> @endif
                    <button class="btn btn-success btn-sm w-full" wire:click="moveTask({{ $task->id }}, 'done')" id="btn-hk-done-{{ $task->id }}">✅ Selesai (→ Kamar Jadi VC)</button>
                </div>
                @empty
                <div class="empty-state" style="padding:30px 10px;"><div class="empty-state-icon" style="font-size:28px;">🧹</div><div class="empty-state-text">Tidak ada task berjalan</div></div>
                @endforelse
            </div>
        </div>

        {{-- Done Column --}}
        <div class="kanban-column">
            <div class="kanban-column-header done">
                <span>✅ Selesai Hari Ini</span>
                <span style="background:rgba(0,0,0,.1);padding:2px 10px;border-radius:20px;font-size:12px;">{{ $doneTasks->count() }}</span>
            </div>
            <div class="kanban-column-body">
                @forelse($doneTasks as $task)
                <div class="kanban-task-card" style="opacity:.8;border-left:3px solid var(--success);">
                    <div class="kanban-task-room" style="font-size:16px;">{{ $task->room->room_number }}</div>
                    <div class="kanban-task-type">{{ $task->task_type_label }}</div>
                    @if($task->completed_at) <div style="font-size:10px;color:var(--success);margin-top:6px;">✅ Selesai {{ $task->completed_at->format('H:i') }}</div> @endif
                </div>
                @empty
                <div class="empty-state" style="padding:30px 10px;"><div class="empty-state-icon" style="font-size:28px;">📋</div><div class="empty-state-text">Belum ada task selesai hari ini</div></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Create Task Modal --}}
    @if($showCreateModal)
    <div class="modal-overlay" wire:click.self="closeCreateModal">
        <div class="modal modal-md animate-slide-up">
            <div class="modal-header">
                <span class="modal-title">🧹 Buat Task Housekeeping</span>
                <button class="modal-close" wire:click="closeCreateModal">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kamar<span class="required">*</span></label>
                    <select class="form-select" wire:model="room_id">
                        <option value="">-- Pilih Kamar --</option>
                        @foreach($dirtyRooms as $room)
                        <option value="{{ $room->id }}">Kamar {{ $room->room_number }} — {{ $room->status }}</option>
                        @endforeach
                    </select>
                    @error('room_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Tipe Task</label>
                        <select class="form-select" wire:model="task_type">
                            <option value="cleaning">Pembersihan</option>
                            <option value="inspection">Inspeksi</option>
                            <option value="maintenance">Perbaikan</option>
                            <option value="turndown">Turndown</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prioritas</label>
                        <select class="form-select" wire:model="priority">
                            <option value="normal">Normal</option>
                            <option value="urgent">🚨 Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign ke Staf</label>
                    <select class="form-select" wire:model="assigned_to">
                        <option value="">-- Pilih Staf (opsional) --</option>
                        @foreach($hkStaff as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" wire:click="closeCreateModal">Batal</button>
                <button class="btn btn-primary" wire:click="createTask" id="btn-save-hk-task">🧹 Buat Task</button>
            </div>
        </div>
    </div>
    @endif
</div>
