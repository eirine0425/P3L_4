<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .header .company-info {
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .report-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
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
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
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
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <strong>ATMA THRIFT SHOP</strong><br>
            Sistem Manajemen Barang Titipan
        </div>
        <h1>{{ $title }}</h1>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <div class="left">
            <strong>Periode:</strong> {{ $period }}<br>
            <strong>Dibuat oleh:</strong> {{ $generated_by }}
        </div>
        <div class="right">
            <strong>Tanggal Cetak:</strong> {{ $generated_at }}
        </div>
    </div>

    @if($sales_by_category->count() > 0)
        <!-- Sales by Category Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Kategori</th>
                    <th style="width: 15%;">Jumlah Terjual</th>
                    <th style="width: 20%;">Total Pendapatan</th>
                    <th style="width: 25%;">Rata-rata Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales_by_category as $index => $category)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $category->nama_kategori }}</td>
                    <td class="text-center">{{ number_format($category->total_items_sold) }}</td>
                    <td class="text-right">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($category->average_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-center">{{ number_format($sales_by_category->sum('total_items_sold')) }}</td>
                    <td class="text-right">Rp {{ number_format($sales_by_category->sum('total_revenue'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($sales_by_category->avg('average_price'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            <h3>Tidak Ada Data</h3>
            <p>Tidak ditemukan data penjualan pada periode yang dipilih.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>Catatan:</strong><br>
            - Laporan ini menampilkan data penjualan berdasarkan kategori barang<br>
            - Data diambil pada {{ $generated_at }}<br>
            - Untuk informasi lebih lanjut, hubungi bagian administrasi
        </div>
        <div style="float: right; text-align: right;">
            <strong>Reuse mart</strong><br>
            Sistem Manajemen Barang Titipan<br>
            {{ $generated_at }}
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
