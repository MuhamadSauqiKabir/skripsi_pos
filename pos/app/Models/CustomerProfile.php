<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProfile extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'visit_count', 'satisfaction_rating'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
