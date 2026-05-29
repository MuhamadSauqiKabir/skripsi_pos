NINETIES COFFEE
Pesanan: {{ $order->public_id }}
Meja: {{ $order->diningTable?->name ?? 'Bawa pulang' }}
Pelanggan: {{ $order->customer?->name ?? 'Pelanggan' }}
@foreach ($order->items as $item)
{{ $item->menu_name_snapshot }} x{{ $item->quantity }} - Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}
@endforeach
Total: Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
