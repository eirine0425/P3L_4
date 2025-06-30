<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Gudang - ReuseMart</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; margin: 0; }
            .table { font-size: 10px; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #000;
        }
        
        .header-info {
            margin-bottom: 20px;
            position: relative;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-address {
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-date {
            font-size: 12px;
            margin-bottom: 20px;
        }
        
        .info-box {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            border: 1px solid #000;
            padding: 8px;
            font-size: 10px;
            background-color: #f9f9f9;
        }
        
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 20px;
        }
        
        .stock-table th,
        .stock-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: middle;
        }
        
        .stock-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .btn {
            padding: 8px 16px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print mb-3" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Laporan
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
    
    <!-- Header -->
    <div class="header-info">
        <div class="company-name">ReUse Mart</div>
        <div class="company-address">Jl. Green Eco Park No. 456 Yogyakarta</div>
        
        <!-- Info Box -->
        <div class="info-box">
            Stok yang bisa dilihat adalah stok per hari ini (sama dengan tanggal cetak). Tidak bisa dilihat stok yang kemarin-kemarin.
        </div>
        
        <div class="report-title">LAPORAN Stok Gudang</div>
        <div class="report-date">Tanggal cetak: {{ $summary['generated_at'] }}</div>
    </div>
    
    <!-- Stock Table -->
    <table class="stock-table">
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Id Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Perpanjangan</th>
                <th>ID hunter</th>
                <th>Nama Hunter</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockItems as $item)
            <tr>
                <td class="text-center">{{ $item->kode_produk }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td class="text-center">{{ $item->id_penitip ?? '-' }}</td>
                <td>{{ $item->nama_penitip_user ?? $item->nama_penitip ?? '-' }}</td>
                <td class="text-center">{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $item->perpanjangan ?? 'Tidak' }}</td>
                <td class="text-center">{{ $item->id_hunter ?? '-' }}</td>
                <td>{{ $item->nama_hunter ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data stok</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Summary -->
    <div style="margin-top: 20px; font-size: 10px;">
        <p><strong>Ringkasan:</strong></p>
        <p>Total Item: {{ $summary['total_items'] }} | Total Nilai: Rp {{ number_format($summary['total_value'], 0, ',', '.') }}</p>
        <p>Item dengan Perpanjangan: {{ $summary['items_with_extension'] }} | Item dari Hunter: {{ $summary['items_from_hunters'] }}</p>
    </div>
</body>
</html>
