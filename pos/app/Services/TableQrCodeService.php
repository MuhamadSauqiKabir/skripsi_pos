<?php

namespace App\Services;

use App\Models\DiningTable;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TableQrCodeService
{
    public function refresh(DiningTable $table): DiningTable
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);
        $payload = route('customer.table', $table->public_token);

        $table->update([
            'qr_code_svg' => $writer->writeString($payload),
        ]);

        return $table->refresh();
    }
}
