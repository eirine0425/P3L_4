<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penitipan Barang - Bulk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 10px;
        }
        
        .summary-info {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background-color: #3498db;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-available {
            background-color: #2ecc71;
            color: white;
        }
        
        .status-sold {
            background-color: #e74c3c;
            color: white;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #bdc3c7;
            font-size: 9px;
            color: #7f8c8d;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">REUSEMART</div>
        <div class="document-title">LAPORAN PENITIPAN BARANG</div>
    </div>

    <!-- Summary Information -->
    <div class="summary-info">
        <strong>Total Barang: {{ $totalItems }} | Tanggal Cetak: {{ $tanggalCetak->format('d F Y, H:i') }} WIB | Petugas: {{ $petugas }}</strong>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%">ID</th>
                <th style="width: 25%">Nama Barang</th>
                <th style="width: 15%">Penitip</th>
                <th style="width: 12%">Kategori</th>
                <th style="width: 12%">Harga</th>
                <th style="width: 10%">Status</th>
                <th style="width: 10%">Tgl Penitipan</th>
                <th style="width: 8%">Sisa Hari</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->barang_id }}</td>
                    <td><strong>{{ $item->nama_barang }}</strong></td>
                    <td>{{ $item->penitip->user->name ?? '-' }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge {{ $item->status == 'belum_terjual' ? 'status-available' : 'status-sold' }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                    <td>{{ $item->tanggal_mulai_penitipan->format('d/m/Y') }}</td>
                    <td>
                        @if($item->sisa_hari > 0)
                            <span style="color: #27ae60;">{{ $item->sisa_hari }} hari</span>
                        @elseif($item->sisa_hari == 0)
                            <span style="color: #f39c12;">Hari ini</span>
                        @else
                            <span style="color: #e74c3c;">-{{ abs($item->sisa_hari) }} hari</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem ReuseMarket pada {{ $tanggalCetak->format('d F Y, H:i') }} WIB</p>
        <p><strong>ReuseMarket</strong> - Platform Jual Beli Barang Bekas Berkualitas</p>
    </div>
</body>
</html>
