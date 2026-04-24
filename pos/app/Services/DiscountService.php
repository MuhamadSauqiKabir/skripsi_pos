<?php

namespace App\Services;

use App\Models\CustomerProfile;
use Carbon\CarbonInterface;

class DiscountService
{
    /**
     * Kombinasi diskon loyalitas dan happy hour dibuat terpisah agar mudah dijelaskan
     * saat sidang dan aman dikembangkan lagi pada iterasi prototype berikutnya.
     */
    public function calculate(?CustomerProfile $customer, float $subtotal, ?CarbonInterface $orderedAt = null): array
    {
        $orderedAt ??= now();

        $discounts = [];

        if ($customer && $customer->visit_count >= 5) {
            $discounts[] = [
                'code' => 'LOYALTY_5X',
                'label' => 'Diskon loyalitas pelanggan',
                'type' => 'percentage',
                'value' => 10,
                'amount' => round($subtotal * 0.10, 2),
            ];
        }

        if ($orderedAt->hour >= 14 && $orderedAt->hour < 17) {
            $discounts[] = [
                'code' => 'HAPPY_HOUR',
                'label' => 'Flash sale happy hour',
                'type' => 'percentage',
                'value' => 8,
                'amount' => round($subtotal * 0.08, 2),
            ];
        }

        $amount = collect($discounts)->sum('amount');

        return [
            'amount' => min($amount, $subtotal),
            'reason' => collect($discounts)->pluck('label')->implode(' + '),
            'breakdown' => $discounts,
        ];
    }
}
