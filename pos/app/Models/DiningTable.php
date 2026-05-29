<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'floor',
        'coordinate_x',
        'coordinate_y',
        'public_token',
        'qr_code_svg',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'floor' => 'integer',
            'coordinate_x' => 'decimal:2',
            'coordinate_y' => 'decimal:2',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
