<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyPrediction extends Model
{
    protected $fillable = ['week_start', 'menu_item_id', 'predicted_qty', 'trend_score', 'meta'];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'meta' => 'array',
            'trend_score' => 'decimal:2',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
