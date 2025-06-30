<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang Terpilih</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.4;
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
        
        .summary-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #007bff;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .summary-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }
        
        .items-table th,
        .items-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .items-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .items-table tr:hover {
            background-color: #e9ecef;
        }
        
        .item-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .no-image {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border: 1px dashed #adb5bd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6c757d;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        
        .status-available { background-color: #d4edda; color: #155724; }
        .status-sold { background-color: #d1ecf1; color: #0c5460; }
        .status-soldout { background-color: #d6d8db; color: #383d41; }
        .status-donation { background-color: #fff3cd; color: #856404; }
        
        .price {
            font-weight: bold;
            color: #007bff;
            text-align: right;
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
        <div class="document-title">DAFTAR BARANG TERPILIH</div>
    </div>

    <!-- Print Info -->
    <div class="print-info">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}<br>
        ID Dokumen: SELECTED-{{ date('Ymd-His') }}
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-title">Ringkasan</div>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $items->count() }}</div>
                <div class="stat-label">Total Barang</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">Rp {{ number_format($items->sum('harga'), 0, ',', '.') }}</div>
                <div class="stat-label">Total Nilai</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $items->where('status', 'belum_terjual')->count() }}</div>
                <div class="stat-label">Belum Terjual</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $items->where('status', 'terjual')->count() }}</div>
                <div class="stat-label">Terjual</div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 8%;">Foto</th>
                <th style="width: 12%;">ID Barang</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 15%;">Penitip</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 12%;">Harga</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 5%;">Sisa Hari</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">
                    @if($item->foto_barang && file_exists(public_path('storage/' . $item->foto_barang)))
                        <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="{{ $item->nama_barang }}" class="item-image">
                    @else
                        <div class="no-image">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                </td>
                <td><strong>{{ $item->barang_id }}</strong></td>
                <td>
                    <strong>{{ $item->nama_barang }}</strong>
                    @if($item->deskripsi)
                        <br><small style="color: #666;">{{ Str::limit($item->deskripsi, 50) }}</small>
                    @endif
                </td>
                <td>{{ $item->penitip->user->name ?? '-' }}</td>
                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                <td class="price">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td style="text-align: center;">
                    @if($item->status == 'belum_terjual')
                        <span class="status-badge status-available">Belum Terjual</span>
                    @elseif($item->status == 'terjual')
                        <span class="status-badge status-sold">Terjual</span>
                    @elseif($item->status == 'sold out')
                        <span class="status-badge status-soldout">Sold Out</span>
                    @elseif($item->status == 'untuk_donasi')
                        <span class="status-badge status-donation">Donasi</span>
                    @else
                        <span class="status-badge">{{ ucfirst($item->status) }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if(isset($item->sisa_hari))
                        @if($item->sisa_hari > 7)
                            <span style="color: #28a745;">{{ $item->sisa_hari }}</span>
                        @elseif($item->sisa_hari > 0)
                            <span style="color: #ffc107;">{{ $item->sisa_hari }}</span>
                        @elseif($item->sisa_hari == 0)
                            <span style="color: #dc3545;">0</span>
                        @else
                            <span style="color: #dc3545;">-{{ abs($item->sisa_hari) }}</span>
                        @endif
                    @else
                        <span style="color: #6c757d;">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem ReuSeMart</p>
        <p>Total {{ $items->count() }} barang dengan nilai Rp {{ number_format($items->sum('harga'), 0, ',', '.') }}</p>
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
