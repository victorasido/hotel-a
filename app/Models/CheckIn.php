<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CheckIn extends Model
{
    protected $fillable = [
        'reservation_id', 'room_id', 'guest_id', 'actual_check_in',
        'extra_pax', 'notes', 'checked_in_by',
    ];

    protected function casts(): array
    {
        return [
            'actual_check_in' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(CheckOut::class);
    }

    public function folio(): HasOne
    {
        return $this->hasOne(GuestFolio::class);
    }
}
