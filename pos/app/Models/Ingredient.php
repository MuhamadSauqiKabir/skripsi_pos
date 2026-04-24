<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['name', 'unit', 'stock', 'par_stock', 'cost_per_unit', 'is_active'];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'par_stock' => 'decimal:2',
            'cost_per_unit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
