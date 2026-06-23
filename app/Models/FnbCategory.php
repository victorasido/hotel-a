<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbCategory extends Model
{
    protected $fillable = ['name', 'icon', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function menus(): HasMany
    {
        return $this->hasMany(FnbMenu::class, 'category_id');
    }
}
