<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Donasi Hunter - ReuseMart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .summary-item {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
        .summary-item-label {
            font-size: 12px;
            color: #666;
        }
        .summary-item-value {
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Donasi Hunter ReuseMart</h1>
        <p>Periode: {{ $data['period'] }}</p>
        <p>Dibuat pada: {{ $data['generated_at'] }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-title">Ringkasan</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-item-label">Total Donasi</div>
                <div class="summary-item-value">{{ $data['summary']['total_donasi'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-item-label">Total Nilai Donasi</div>
                <div class="summary-item-value">Rp {{ number_format($data['summary']['total_nilai_donasi'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-item-label">Hunter Terlibat</div>
                <div class="summary-item-value">{{ $data['summary']['hunter_terlibat'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-item-label">Organisasi Terlibat</div>
                <div class="summary-item-value">{{ $data['summary']['organisasi_terlibat'] }}</div>
            </div>
        </div>
    </div>
    
    <div class="hunter-performance">
        <div class="summary-title">Performa Hunter</div>
        <table>
            <thead>
                <tr>
                    <th>Kode Hunter</th>
                    <th>Nama Hunter</th>
                    <th>Telepon</th>
                    <th>Total Donasi</th>
                    <th>Total Nilai</th>
                    <th>Rata-rata Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['hunter_performance'] as $hunter)
                <tr>
                    <td>{{ $hunter['kode_hunter'] ?? '-' }}</td>
                    <td>{{ $hunter['hunter_nama'] ?? '-' }}</td>
                    <td>{{ $hunter['hunter_telp'] ?? '-' }}</td>
                    <td>{{ number_format($hunter['total_donasi'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($hunter['total_nilai'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($hunter['avg_nilai'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="donasi-list">
        <div class="summary-title">Daftar Donasi</div>
        <table>
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Nilai</th>
                    <th>Hunter</th>
                    <th>Organisasi</th>
                    <th>Tanggal Donasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['donasi_hunter'] as $donasi)
                <tr>
                    <td>{{ $donasi->kode_barang ?? '-' }}</td>
                    <td>{{ $donasi->nama_barang ?? '-' }}</td>
                    <td>{{ $donasi->nama_kategori ?? 'Tidak Dikategorikan' }}</td>
                    <td>Rp {{ number_format($donasi->harga ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $donasi->hunter_nama ?? $donasi->hunter_user_nama ?? '-' }}</td>
                    <td>{{ $donasi->nama_organisasi ?? '-' }}</td>
                    <td>{{ $donasi->tanggal_donasi ? date('d/m/Y', strtotime($donasi->tanggal_donasi)) : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} ReuseMart - Laporan ini dibuat secara otomatis</p>
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()">Print Laporan</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
