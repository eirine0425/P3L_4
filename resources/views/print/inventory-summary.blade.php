<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Inventaris</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
        }
        
        .print-info {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            font-weight: 500;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .chart-placeholder {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .summary-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .summary-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .print-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        @page {
            margin: 1cm;
            size: A4;
        }
    </style>
</head>
<body>
    <!-- Print Buttons -->
    <div class="print-buttons no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="downloadPDF()" class="btn btn-success">
            <i class="fas fa-download"></i> Download PDF
        </button>
        <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ config('app.name', 'ReuSeMart') }}</div>
        <div class="company-info">
            Jl. Contoh No. 123, Kota, Provinsi 12345<br>
            Telp: +62 123 456 789 | Email: info@reusemart.com
        </div>
        <div class="document-title">RINGKASAN INVENTARIS</div>
    </div>

    <!-- Print Info -->
    <div class="print-info">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}<br>
        Periode: {{ now()->format('F Y') }}<br>
        ID Dokumen: INVENTORY-{{ date('Ymd-His') }}
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $totalItems ?? 0 }}</div>
            <div class="stat-label">Total Barang</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $activeItems ?? 0 }}</div>
            <div class="stat-label">Belum Terjual</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $soldItems ?? 0 }}</div>
            <div class="stat-label">Terjual</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $soldOutItems ?? 0 }}</div>
            <div class="stat-label">Sold Out</div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="section-title">Distribusi Status Barang</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Status</th>
                <th style="text-align: center;">Jumlah</th>
                <th style="text-align: center;">Persentase</th>
                <th style="text-align: right;">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($itemsByStatus))
                @foreach($itemsByStatus as $status)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $status->status)) }}</td>
                    <td style="text-align: center;">{{ $status->total }}</td>
                    <td style="text-align: center;">
                        {{ $totalItems > 0 ? number_format(($status->total / $totalItems) * 100, 1) : 0 }}%
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($status->total_value ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">Data tidak tersedia</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Category Distribution -->
    <div class="section-title">Distribusi Kategori Barang</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th style="text-align: center;">Jumlah</th>
                <th style="text-align: center;">Persentase</th>
                <th style="text-align: right;">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($itemsByCategory))
                @foreach($itemsByCategory as $category)
                <tr>
                    <td>{{ $category->nama_kategori }}</td>
                    <td style="text-align: center;">{{ $category->total }}</td>
                    <td style="text-align: center;">
                        {{ $totalItems > 0 ? number_format(($category->total / $totalItems) * 100, 1) : 0 }}%
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($category->total_value ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">Data tidak tersedia</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Recent Items -->
    @if(isset($recentItems) && $recentItems->count() > 0)
    <div class="section-title">Barang Terbaru (10 Terakhir)</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Penitip</th>
                <th>Kategori</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentItems as $item)
            <tr>
                <td><strong>{{ $item->barang_id }}</strong></td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->penitip->user->name ?? '-' }}</td>
                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                <td style="text-align: center;">{{ $item->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem ReuSeMart</p>
        <p>Ringkasan inventaris per {{ now()->format('d F Y') }}</p>
        <p>Untuk informasi lebih lanjut, hubungi customer service kami</p>
    </div>

    <script>
        function downloadPDF() {
            // Hide print buttons before generating PDF
            const printButtons = document.querySelector('.print-buttons');
            if (printButtons) {
                printButtons.style.display = 'none';
            }
            
            // Trigger browser's save as PDF
            window.print();
            
            // Show print buttons again after a delay
            setTimeout(() => {
                if (printButtons) {
                    printButtons.style.display = 'block';
                }
            }, 1000);
        }

        // Auto-focus for better printing experience
        window.addEventListener('load', function() {
            // Optional: Auto-open print dialog
            // window.print();
        });
    </script>
</body>
</html>
