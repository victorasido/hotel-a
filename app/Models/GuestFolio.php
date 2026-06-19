<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestFolio extends Model
{
    protected $fillable = [
        'folio_number', 'check_in_id', 'guest_id', 'status',
        'grand_total', 'discount', 'tax',
    ];

    protected function casts(): array
    {
        return [
            'grand_total' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
        ];
    }

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FolioItem::class, 'folio_id');
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $this->grand_total = $subtotal - $this->discount + $this->tax;
        $this->save();
    }

    public static function generateFolioNumber(): string
    {
        $prefix = 'FLO-' . date('Ym') . '-';
        $last = static::where('folio_number', 'like', $prefix . '%')
            ->orderBy('folio_number', 'desc')
            ->first();
        $next = $last ? (intval(substr($last->folio_number, -3)) + 1) : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
