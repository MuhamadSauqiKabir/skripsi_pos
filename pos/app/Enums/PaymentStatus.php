<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Settlement = 'settlement';
    case Expired = 'expired';
    case Failed = 'failed';
    case Refunded = 'refunded';
}
