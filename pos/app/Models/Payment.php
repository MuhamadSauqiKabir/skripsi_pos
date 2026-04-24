<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'channel',
        'status',
        'provider',
        'reference_id',
        'provider_payment_id',
        'checkout_url',
        'qr_string',
        'payload',
        'webhook_payload',
        'expires_at',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'webhook_payload' => 'array',
            'expires_at' => 'datetime',
            'settled_at' => 'datetime',
            'status' => PaymentStatus::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
