<?php

namespace App\Enums;

enum PaymentChannel: string
{
    case Cash = 'cash';
    case Qris = 'qris';
}
