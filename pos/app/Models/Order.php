<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'public_id',
        'customer_profile_id',
        'dining_table_id',
        'created_by',
        'status',
        'payment_status',
        'payment_channel',
        'subtotal',
        'tax_amount',
        'total_amount',
        'gross_profit',
        'notes',
        'ordered_at',
        'paid_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'ordered_at' => 'datetime',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_channel' => PaymentChannel::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_profile_id');
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(CustomerFeedback::class);
    }
}
