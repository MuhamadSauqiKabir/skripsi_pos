<table>
    <thead>
        <tr>
            <th colspan="2">Nineties Coffee Weekly Report</th>
        </tr>
        <tr>
            <th>Periode</th>
            <th>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Revenue</td><td>{{ $report['revenue'] }}</td></tr>
        <tr><td>Gross Profit</td><td>{{ $report['gross_profit'] }}</td></tr>
        <tr><td>Average Rating</td><td>{{ $report['customer_rating'] }}</td></tr>
        <tr><td colspan="2">Top Menus</td></tr>
        @foreach ($report['top_menus'] as $menu)
            <tr>
                <td>{{ $menu->name }}</td>
                <td>{{ $menu->sold_qty }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
