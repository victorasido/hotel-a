<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = [
        'room_number', 'floor', 'room_type_id', 'status', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'floor' => 'integer',
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function activeCheckIn(): HasOne
    {
        return $this->hasOne(CheckIn::class)->whereHas('reservation', function ($q) {
            $q->where('status', 'checked_in');
        })->latest();
    }

    public function housekeepingTasks(): HasMany
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'VC' => 'Vacant Clean',
            'VD' => 'Vacant Dirty',
            'OC' => 'Occupied Clean',
            'OD' => 'Occupied Dirty',
            'OOO' => 'Out of Order',
            'OOS' => 'Out of Service',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'VC' => 'success',
            'VD' => 'warning',
            'OC' => 'primary',
            'OD' => 'orange',
            'OOO' => 'danger',
            'OOS' => 'secondary',
            default => 'secondary',
        };
    }
}
