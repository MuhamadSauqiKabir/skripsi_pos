<?php

namespace App\Services;

class RajaOngkirService
{
    /**
     * Fallback sample digunakan agar fitur online order tetap bisa didemokan
     * saat API RajaOngkir belum dihubungkan ke environment skripsi.
     */
    public function sampleCouriers(): array
    {
        return [
            ['service' => 'REG', 'courier' => 'JNE', 'etd' => '1-2 hari', 'cost' => 18000],
            ['service' => 'YES', 'courier' => 'JNE', 'etd' => '1 hari', 'cost' => 26000],
            ['service' => 'REG', 'courier' => 'SiCepat', 'etd' => '1-2 hari', 'cost' => 17000],
        ];
    }
}
