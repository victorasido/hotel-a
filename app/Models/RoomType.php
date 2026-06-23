<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'base_price',
        'seasonal_price', 'capacity', 'facilities', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'seasonal_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function getActivePriceAttribute(): float
    {
        return $this->seasonal_price ?? $this->base_price;
    }
}
