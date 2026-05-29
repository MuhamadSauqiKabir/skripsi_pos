<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #261c15; line-height: 1.5; background-color: #ffffff; }
        .header { background-color: #3d2b1f; color: #f7f1e8; padding: 40px 30px; margin: -1cm -1cm 30px -1cm; text-align: left; position: relative; }
        .header h1 { margin: 0; font-size: 28px; letter-spacing: 2px; text-transform: uppercase; font-weight: 300; }
        .header p { margin: 5px 0 0 0; opacity: 0.8; font-size: 12px; letter-spacing: 1px; }
        .header .meta { position: absolute; right: 30px; bottom: 30px; text-align: right; font-size: 10px; opacity: 0.7; }

        .container { padding: 0 10px; }
        
        .section-title { font-size: 14px; font-weight: bold; color: #3d2b1f; border-bottom: 1px solid #ece4d9; padding-bottom: 5px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        
        .stats-grid { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin: 0 -10px 30px -10px; }
        .stats-card { background: #fdfaf5; border: 1px solid #ece4d9; padding: 15px; border-radius: 4px; text-align: center; }
        .stats-label { display: block; font-size: 9px; text-transform: uppercase; color: #9b8a72; font-weight: bold; letter-spacing: 1px; margin-bottom: 8px; }
        .stats-value { font-size: 18px; font-weight: bold; color: #3d2b1f; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f1ece3; color: #3d2b1f; padding: 12px 10px; text-align: left; font-size: 10px; border-bottom: 2px solid #3d2b1f; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #f1ece3; vertical-align: middle; }
        tr:nth-child(even) { background-color: #fdfaf5; }

        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-completed { background-color: #e5eddf; color: #63745d; }
        .status-pending { background-color: #efe8da; color: #867762; }
        
        .footer { margin-top: 40px; border-top: 1px solid #ece4d9; padding-top: 15px; text-align: center; font-size: 9px; color: #9b8a72; font-style: italic; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nineties Coffee</h1>
        <p>Official Sales Documentation</p>
        <div class="meta">
            Periode: {{ $date_range }}<br>
            Dicetak: {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    <div class="container">
        <div class="section-title">Performance Summary</div>
        <table class="stats-grid">
            <tr>
                <td class="stats-card" width="33%">
                    <span class="stats-label">Total Volume</span>
                    <span class="stats-value">{{ $orders_count }} Transaksi</span>
                </td>
                <td class="stats-card" width="33%">
                    <span class="stats-label">Total Revenue</span>
                    <span class="stats-value">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                </td>
                <td class="stats-card" width="33%">
                    <span class="stats-label">Gross Profit</span>
                    <span class="stats-value">Rp {{ number_format($profit, 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">Transaction Details</div>
        <table>
            <thead>
                <tr>
                    <th width="12%">Order ID</th>
                    <th width="20%">Date & Time</th>
                    <th>Customer / Table</th>
                    <th width="18%">Amount</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td style="font-weight: bold; color: #9b8a72;">#{{ $order->public_id }}</td>
                    <td>{{ $order->ordered_at->format('d M Y') }}<br><small style="color: #9b8a72;">{{ $order->ordered_at->format('H:i') }}</small></td>
                    <td>
                        <strong>{{ $order->customer?->name ?? 'Guest Customer' }}</strong><br>
                        <small>Table: {{ $order->diningTable?->name ?? 'N/A' }}</small>
                    </td>
                    <td style="font-weight: bold;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge {{ $order->status === 'completed' ? 'status-completed' : 'status-pending' }}">
                            {{ $order->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Confidential Document &copy; {{ date('Y') }} Nineties Coffee Management. Page <span class="page-number"></span>
        </div>
    </div>
</body>
</html>
