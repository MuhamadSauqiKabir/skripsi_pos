<table>
    <thead>
        <tr>
            <th colspan="3" style="font-weight: bold; font-size: 14px; text-align: center;">NINETIES COFFEE - LAPORAN PENJUALAN MINGGUAN</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center;">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</th>
        </tr>
        <tr><th></th></tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f3f3;">Metrik Utama</th>
            <th style="font-weight: bold; background-color: #f3f3f3;">Nilai</th>
            <th style="font-weight: bold; background-color: #f3f3f3;">Satuan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Pendapatan</td>
            <td>{{ $report['revenue'] }}</td>
            <td>IDR</td>
        </tr>
        <tr>
            <td>Total Keuntungan Kotor</td>
            <td>{{ $report['gross_profit'] }}</td>
            <td>IDR</td>
        </tr>
        <tr>
            <td>Total Pesanan</td>
            <td>{{ $report['total_orders'] }}</td>
            <td>Pesanan</td>
        </tr>
        <tr>
            <td>Rata-rata Rating</td>
            <td>{{ $report['average_rating'] }}</td>
            <td>/ 5.0</td>
        </tr>
        <tr><th></th></tr>
        <tr>
            <th colspan="3" style="font-weight: bold; background-color: #3d2b1f; color: #ffffff;">DAFTAR MENU TERLARIS</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f3f3;">Nama Menu</th>
            <th style="font-weight: bold; background-color: #f3f3f3;">Kuantitas Terjual</th>
            <th style="font-weight: bold; background-color: #f3f3f3;">Total Penjualan</th>
        </tr>
        @foreach ($report['top_menus'] as $menu)
            <tr>
                <td>{{ $menu->name }}</td>
                <td>{{ $menu->sold_qty }}</td>
                <td>{{ $menu->sales }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
