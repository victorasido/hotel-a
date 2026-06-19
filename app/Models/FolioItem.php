<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FolioItem extends Model
{
    protected $fillable = [
        'folio_id', 'type', 'description', 'qty',
        'unit_price', 'subtotal', 'item_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'item_date' => 'date',
        ];
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(GuestFolio::class, 'folio_id');
    }
}
