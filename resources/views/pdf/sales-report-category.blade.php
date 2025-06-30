<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
        }
        .period {
            font-size: 14px;
            color: #666;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 20px 0;
        }
        .summary-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .summary-item {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }
        .summary-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .performance-good { color: #28a745; font-weight: bold; }
        .performance-average { color: #ffc107; font-weight: bold; }
        .performance-poor { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name }}</div>
        <div class="company-address">{{ $company_address }}</div>
        <div class="report-title">{{ $title }}</div>
        <div class="period">Periode: {{ $period_start }} - {{ $period_end }}</div>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-title">RINGKASAN PENJUALAN</div>
        <div class="summary-item">
            <span class="summary-label">Total Kategori:</span>
            <span>{{ $summary['total_categories_qualified'] }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Produk Terjual:</span>
            <span>{{ $summary['total_products_sold'] }} unit</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Transaksi:</span>
            <span>{{ $summary['total_transactions'] }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Pendapatan:</span>
            <span>Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Rata-rata per Item:</span>
            <span>Rp {{ number_format($summary['average_revenue_per_item'], 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Tingkat Penjualan:</span>
            <span>{{ $summary['overall_sell_through_rate'] }}%</span>
        </div>
    </div>

    <!-- Main Sales Table -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Terjual</th>
                <th>Belum Terjual</th>
                <th>Total</th>
                <th>Pendapatan</th>
                <th>Rata-rata Harga</th>
                <th>Tingkat Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales_by_category as $index => $category)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $category->nama_kategori }}</td>
                    <td class="text-center">{{ $category->items_sold }}</td>
                    <td class="text-center">{{ $category->items_unsold }}</td>
                    <td class="text-center">{{ $category->total_items }}</td>
                    <td class="text-right">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($category->average_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($category->sell_through_rate, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-center">{{ $sales_by_category->sum('items_sold') }}</td>
                <td class="text-center">{{ $sales_by_category->sum('items_unsold') }}</td>
                <td class="text-center">{{ $sales_by_category->sum('total_items') }}</td>
                <td class="text-right">Rp {{ number_format($sales_by_category->sum('total_revenue'), 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($summary['overall_sell_through_rate'], 1) }}%</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan dibuat pada: {{ $generated_at }}</p>
        <p>Dibuat oleh: {{ $generated_by }}</p>
    </div>
</body>
</html>
