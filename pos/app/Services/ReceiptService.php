<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReceiptService
{
    public function generateEscPos(Order $order): string
    {
        $order->loadMissing('items', 'customer', 'diningTable');

        $lines = [
            "\x1B\x40",
            "NINETIES COFFEE\n",
            "Vintage Elegant POS\n",
            str_repeat('-', 32)."\n",
            "Order : {$order->public_id}\n",
            'Table : '.($order->diningTable?->name ?? 'Take Away')."\n",
            'Customer : '.($order->customer?->name ?? 'Guest')."\n",
            str_repeat('-', 32)."\n",
        ];

        foreach ($order->items as $item) {
            $lines[] = "{$item->menu_name_snapshot} x{$item->quantity}\n";
            $lines[] = 'Rp '.number_format((float) $item->line_total, 0, ',', '.')."\n";
        }

        $lines[] = str_repeat('-', 32)."\n";
        $lines[] = 'Total: Rp '.number_format((float) $order->total_amount, 0, ',', '.')."\n";
        $lines[] = "\nTerima kasih.\n\n\n\x1D\x56\x00";

        $payload = implode('', $lines);
        $path = "receipts/{$order->public_id}.bin";

        Storage::disk('local')->put($path, $payload);

        return Storage::disk('local')->path($path);
    }

    public function sendDigitalReceipt(Order $order): array
    {
        $order->loadMissing('customer', 'items');
        $receiptText = view('exports.receipt-text', compact('order'))->render();
        $result = [
            'email' => null,
            'whatsapp_url' => null,
        ];

        try {
            if ($order->customer?->email) {
                Mail::raw($receiptText, function ($message) use ($order): void {
                    $message
                        ->to($order->customer->email)
                        ->subject("E-Receipt {$order->public_id} - Nineties Coffee");
                });

                $result['email'] = 'sent';
            }
        } catch (Throwable $exception) {
            report($exception);
            $result['email'] = 'failed';
        }

        if ($order->customer?->phone) {
            $plainPhone = preg_replace('/\D+/', '', $order->customer->phone);
            $result['whatsapp_url'] = "https://wa.me/{$plainPhone}?text=".urlencode($receiptText);
        }

        return $result;
    }
}
