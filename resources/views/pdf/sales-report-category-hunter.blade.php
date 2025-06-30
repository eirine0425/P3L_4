<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #2c3e50;
            text-transform: uppercase;
        }
        
        .header .company-info {
            font-size: 12px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }
        
        .report-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .report-info .left,
        .report-info .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .report-info .right {
            text-align: right;
        }
        
        .criteria-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .criteria-box h4 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #495057;
        }
        
        .criteria-box ul {
            margin: 0;
            padding-left: 15px;
            font-size: 9px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .hunter-names {
            font-size: 8px;
            color: #007bff;
            font-weight: bold;
        }
        
        .summary-box {
            background-color: #e9ecef;
            border: 1px solid #adb5bd;
            padding: 10px;
            margin-top: 15px;
            border-radius: 3px;
        }
        
        .summary-box h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: #495057;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            font-size: 9px;
        }
        
        .summary-value {
            font-weight: bold;
            font-size: 11px;
            color: #007bff;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 3px;
            background-color: #007bff;
            color: white;
        }
        
        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">{{ $company_name }}</div>
        <div style="font-size: 10px; margin-bottom: 5px;">{{ $company_address }}</div>
        <h1>{{ $title }}</h1>
        <div class="subtitle">Kategori dengan Hunter & Minimal {{ $min_products }} Produk</div>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <div class="left">
            <strong>Periode:</strong> {{ $period_start }} - {{ $period_end }}<br>
            <strong>Dibuat oleh:</strong> {{ $generated_by }}<br>
            <strong>Minimal Produk:</strong> {{ $min_products }} produk
        </div>
        <div class="right">
            <strong>Tanggal Cetak:</strong> {{ $generated_at }}<br>
            <strong>Total Kategori:</strong> {{ $summary['total_categories'] }}<br>
            <strong>Total Hunter:</strong> {{ $summary['total_hunters'] }}
        </div>
    </div>

    <!-- Criteria Box -->
    <div class="criteria-box">
        <h4>Kriteria Laporan:</h4>
        <ul>
            <li>Hanya menampilkan kategori yang memiliki hunter</li>
            <li>Minimal {{ $min_products }} produk per kategori</li>
            <li>Data penjualan periode {{ $period_start }} - {{ $period_end }}</li>
            <li>Hunter adalah pegawai dengan role "hunter"</li>
        </ul>
    </div>

    @if($sales_by_category->count() > 0)
        <!-- Sales by Category with Hunter Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Kategori</th>
                    <th style="width: 8%;">Total<br>Produk</th>
                    <th style="width: 8%;">Terjual</th>
                    <th style="width: 8%;">Tidak<br>Terjual</th>
                    <th style="width: 12%;">Total<br>Pendapatan</th>
                    <th style="width: 10%;">Rata-rata<br>Harga</th>
                    <th style="width: 6%;">Jml<br>Hunter</th>
                    <th style="width: 18%;">Nama Hunter</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales_by_category as $index => $category)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $category->nama_kategori }}</strong></td>
                    <td class="text-center">{{ number_format($category->total_products) }}</td>
                    <td class="text-center">{{ number_format($category->items_sold) }}</td>
                    <td class="text-center">{{ number_format($category->items_unsold) }}</td>
                    <td class="text-right">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($category->average_price ?: 0, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge">{{ $category->hunter_count }}</span>
                    </td>
                    <td class="hunter-names">{{ $category->hunter_names ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ number_format($summary['total_products']) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($summary['total_sold']) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($summary['total_unsold']) }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</strong></td>
                    <td class="text-center">-</td>
                    <td class="text-center"><strong>{{ number_format($summary['total_hunters']) }}</strong></td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>

        <!-- Summary Box -->
        <div class="summary-box">
            <h4>Ringkasan Laporan:</h4>
            <div class="summary-grid">
                <div class="summary-item">
                    <div>Total Kategori</div>
                    <div class="summary-value">{{ $summary['total_categories'] }}</div>
                </div>
                <div class="summary-item">
                    <div>Total Produk</div>
                    <div class="summary-value">{{ number_format($summary['total_products']) }}</div>
                </div>
                <div class="summary-item">
                    <div>Total Hunter</div>
                    <div class="summary-value">{{ $summary['total_hunters'] }}</div>
                </div>
            </div>
            <div style="margin-top: 10px; font-size: 9px;">
                <strong>Persentase Penjualan:</strong> 
                @if($summary['total_products'] > 0)
                    {{ number_format(($summary['total_sold'] / $summary['total_products']) * 100, 1) }}%
                @else
                    0%
                @endif
                <br>
                <strong>Rata-rata Produk per Kategori:</strong> 
                @if($summary['total_categories'] > 0)
                    {{ number_format($summary['total_products'] / $summary['total_categories'], 1) }}
                @else
                    0
                @endif
                <br>
                <strong>Rata-rata Hunter per Kategori:</strong> 
                @if($summary['total_categories'] > 0)
                    {{ number_format($summary['total_hunters'] / $summary['total_categories'], 1) }}
                @else
                    0
                @endif
            </div>
        </div>
    @else
        <div class="no-data">
            <h3>Tidak Ada Data</h3>
            <p>Tidak ditemukan kategori yang memenuhi kriteria:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>Memiliki hunter</li>
                <li>Minimal {{ $min_products }} produk</li>
                <li>Pada periode {{ $period_start }} - {{ $period_end }}</li>
            </ul>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>Catatan:</strong><br>
            - Laporan ini menampilkan kategori barang yang memiliki hunter dengan minimal {{ $min_products }} produk<br>
            - Hunter adalah pegawai dengan role "hunter" yang bertugas mengambil barang<br>
            - Data diambil pada {{ $generated_at }}<br>
            - Untuk informasi lebih lanjut, hubungi bagian administrasi
        </div>
        <div style="float: right; text-align: right;">
            <strong>{{ $company_name }}</strong><br>
            Sistem Manajemen Barang Titipan<br>
            {{ $generated_at }}
        </div>
        <div style="clear: both;"></div>
        
        <div style="text-align: center; margin-top: 10px; font-size: 7px;">
            <em>Laporan ini digenerate secara otomatis oleh sistem {{ $company_name }}</em>
        </div>
    </div>
</body>
</html>
