<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckOut extends Model
{
    protected $fillable = [
        'check_in_id', 'actual_check_out', 'total_paid',
        'payment_method', 'notes', 'checked_out_by',
    ];

    protected function casts(): array
    {
        return [
            'actual_check_out' => 'datetime',
            'total_paid' => 'decimal:2',
        ];
    }

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}
