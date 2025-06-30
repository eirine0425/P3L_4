<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang - {{ $item->nama_barang }}</title>
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
        
        .item-container {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .item-image {
            flex: 0 0 200px;
        }
        
        .item-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        
        .no-image {
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 14px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .detail-table th,
        .detail-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .detail-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
            color: #495057;
        }
        
        .detail-table td {
            color: #333;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-available { background-color: #d4edda; color: #155724; }
        .status-sold { background-color: #d1ecf1; color: #0c5460; }
        .status-soldout { background-color: #d6d8db; color: #383d41; }
        .status-donation { background-color: #fff3cd; color: #856404; }
        
        .price {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        
        .description-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .description-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .description-text {
            color: #333;
            line-height: 1.8;
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
        <div class="document-title">DETAIL BARANG TITIPAN</div>
    </div>

    <!-- Print Info -->
    <div class="print-info">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}<br>
        ID Dokumen: {{ $item->barang_id }}-{{ date('Ymd') }}
    </div>

    <!-- Item Container -->
    <div class="item-container">
        <!-- Item Image -->
        <div class="item-image">
            @if($item->foto_barang && file_exists(public_path('storage/' . $item->foto_barang)))
                <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="{{ $item->nama_barang }}">
            @else
                <div class="no-image">
                    <div>
                        <i class="fas fa-image" style="font-size: 24px; margin-bottom: 10px;"></i><br>
                        Tidak ada foto
                    </div>
                </div>
            @endif
        </div>

        <!-- Item Details -->
        <div class="item-details">
            <table class="detail-table">
                <tr>
                    <th>ID Barang</th>
                    <td><strong>{{ $item->barang_id }}</strong></td>
                </tr>
                <tr>
                    <th>Nama Barang</th>
                    <td><strong>{{ $item->nama_barang }}</strong></td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kondisi</th>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->kondisi ?? '-')) }}</td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td class="price">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($item->status == 'belum_terjual')
                            <span class="status-badge status-available">Belum Terjual</span>
                        @elseif($item->status == 'terjual')
                            <span class="status-badge status-sold">Terjual</span>
                        @elseif($item->status == 'sold out')
                            <span class="status-badge status-soldout">Sold Out</span>
                        @elseif($item->status == 'untuk_donasi')
                            <span class="status-badge status-donation">Untuk Donasi</span>
                        @else
                            <span class="status-badge">{{ ucfirst($item->status) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Penitip</th>
                    <td>{{ $item->penitip->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Penitipan</th>
                    <td>{{ $item->tanggal_penitipan ? \Carbon\Carbon::parse($item->tanggal_penitipan)->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <th>Batas Penitipan</th>
                    <td>{{ $item->batas_penitipan ? \Carbon\Carbon::parse($item->batas_penitipan)->format('d/m/Y') : '-' }}</td>
                </tr>
                @if(isset($item->sisa_hari))
                <tr>
                    <th>Sisa Waktu</th>
                    <td>
                        @if($item->sisa_hari > 0)
                            <span style="color: #28a745;">{{ $item->sisa_hari }} hari lagi</span>
                        @elseif($item->sisa_hari == 0)
                            <span style="color: #ffc107;">Berakhir hari ini</span>
                        @else
                            <span style="color: #dc3545;">Kadaluarsa {{ abs($item->sisa_hari) }} hari</span>
                        @endif
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Description Section -->
    @if($item->deskripsi)
    <div class="description-section">
        <div class="description-title">Deskripsi Barang</div>
        <div class="description-text">{{ $item->deskripsi }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem ReuSeMart</p>
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
