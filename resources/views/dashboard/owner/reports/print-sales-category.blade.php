<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: left;
            margin-bottom: 30px;
            border: 2px solid #000;
            padding: 15px;
            background: #f8f9fa;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-align: center;
            text-transform: uppercase;
        }
        
        .company-info {
            font-size: 11px;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
        }
        
        .report-period {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        
        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }
        
        .table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        
        .table td {
            text-align: center;
        }
        
        .table td:first-child {
            text-align: left;
            padding-left: 12px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .text-center {
            text-align: center;
        }
        
        .category-name {
            text-align: left !important;
        }
        
        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #666;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
        
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .btn {
            padding: 8px 15px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print
        </button>
        <a href="{{ route('dashboard.owner.sales-report-category-form') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <strong>{{ $company_name }}</strong><br>
            {{ $company_address }}
        </div>
        
        <div class="report-title">
            {{ $title }}
        </div>
        
        <div class="report-period">
            <strong>Tahun:</strong> {{ $year }}<br>
            <strong>Tanggal cetak:</strong> {{ $generated_at }}<br>
            <strong>Dicetak oleh:</strong> {{ $generated_by }}
        </div>
    </div>

    <!-- Sales Table -->
    <table class="table">
        <thead>
            <tr>
                <th style="width: 50%;">Kategori Barang</th>
                <th style="width: 25%;">Jumlah Item<br>Terjual</th>
                <th style="width: 25%;">Jumlah Item<br>Gagal Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales_by_category as $category)
            <tr>
                <td class="category-name">{{ $category->nama_kategori }}</td>
                <td class="text-center">{{ $category->items_sold ?: '-' }}</td>
                <td class="text-center">{{ $category->items_unsold ?: '-' }}</td>
            </tr>
            @endforeach
            
            <!-- Total Row -->
            <tr class="total-row">
                <td class="category-name"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ $total_sold }}</strong></td>
                <td class="text-center"><strong>{{ $total_unsold }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Information -->
    <div style="margin-top: 30px; font-size: 11px;">
        <h4>Ringkasan Laporan:</h4>
        <ul>
            <li>Total kategori barang: {{ count($sales_by_category) }} kategori</li>
            <li>Total item terjual: {{ $total_sold }} item</li>
            <li>Total item gagal terjual: {{ $total_unsold }} item</li>
            <li>Total keseluruhan item: {{ $total_sold + $total_unsold }} item</li>
            @if(($total_sold + $total_unsold) > 0)
            <li>Persentase penjualan: {{ number_format(($total_sold / ($total_sold + $total_unsold)) * 100, 1) }}%</li>
            @endif
        </ul>
    </div>

    <!-- Signature Section -->
    <div class="signature-section no-print">
        <div class="signature-box">
            <p>Mengetahui,<br>Pemilik</p>
            <div class="signature-line">
                <strong>{{ $generated_by }}</strong>
            </div>
        </div>
        
        <div class="signature-box">
            <p>Yogyakarta, {{ $generated_at }}</p>
            <div class="signature-line">
                <strong>Staff Administrasi</strong>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <hr>
        <p style="text-align: center; margin: 10px 0;">
            <em>Laporan ini digenerate secara otomatis oleh sistem ReUse Mart</em><br>
            <small>Dicetak pada: {{ $generated_at }} | Halaman 1 dari 1</small>
        </p>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // }
        
        // Print function
        function printReport() {
            window.print();
        }
        
        // Back function
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
