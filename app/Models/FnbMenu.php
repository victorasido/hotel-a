<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbMenu extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'is_available', 'image', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FnbCategory::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(FnbOrderItem::class, 'menu_id');
    }
}
