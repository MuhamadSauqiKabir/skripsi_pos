NINETIES COFFEE
Order: {{ $order->public_id }}
Table: {{ $order->diningTable?->name ?? 'Take Away' }}
Customer: {{ $order->customer?->name ?? 'Guest' }}
@foreach ($order->items as $item)
{{ $item->menu_name_snapshot }} x{{ $item->quantity }} - Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}
@endforeach
Total: Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
