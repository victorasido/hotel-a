<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousekeepingTask extends Model
{
    protected $fillable = [
        'room_id', 'assigned_to', 'task_type', 'status',
        'priority', 'notes', 'requested_by', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function getTaskTypeLabelAttribute(): string
    {
        return match($this->task_type) {
            'cleaning' => 'Pembersihan',
            'inspection' => 'Inspeksi',
            'maintenance' => 'Perbaikan',
            'turndown' => 'Turndown Service',
            default => $this->task_type,
        };
    }
}
