<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbOrder extends Model
{
    protected $fillable = [
        'order_number', 'check_in_id', 'folio_id', 'room_id',
        'order_type', 'status', 'total', 'notes', 'created_by', 'served_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'served_at' => 'datetime',
        ];
    }

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(GuestFolio::class, 'folio_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FnbOrderItem::class, 'order_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Pending', 'color' => 'warning'],
            'processing' => ['label' => 'Diproses', 'color' => 'primary'],
            'served' => ['label' => 'Disajikan', 'color' => 'success'],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'danger'],
            default => ['label' => $this->status, 'color' => 'secondary'],
        };
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $last = static::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
        $next = $last ? (intval(substr($last->order_number, -3)) + 1) : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
