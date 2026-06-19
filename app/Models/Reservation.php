<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    protected $fillable = [
        'booking_code', 'guest_id', 'room_id', 'check_in_date', 'check_out_date',
        'pax', 'status', 'source', 'room_rate', 'total_amount', 'deposit',
        'special_request', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'room_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(CheckIn::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Pending', 'color' => 'warning'],
            'confirmed' => ['label' => 'Confirmed', 'color' => 'primary'],
            'checked_in' => ['label' => 'Checked In', 'color' => 'success'],
            'checked_out' => ['label' => 'Checked Out', 'color' => 'secondary'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'danger'],
            'no_show' => ['label' => 'No Show', 'color' => 'dark'],
            default => ['label' => $this->status, 'color' => 'secondary'],
        };
    }

    public static function generateBookingCode(): string
    {
        $prefix = 'RSV-' . date('Ym') . '-';
        $last = static::where('booking_code', 'like', $prefix . '%')
            ->orderBy('booking_code', 'desc')
            ->first();
        $next = $last ? (intval(substr($last->booking_code, -3)) + 1) : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
