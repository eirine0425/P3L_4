<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Donasi Barang - ReuseMart</title>
    <style>
        @media print {
            @page {
                margin: 2cm;
                size: A4;
            }
            
            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .header {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-align: left;
        }

        .company-address {
            font-size: 12px;
            margin: 5px 0 0 0;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 5px 0;
            text-decoration: underline;
        }

        .report-info {
            margin-bottom: 20px;
        }

        .report-info p {
            margin: 3px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        table th {
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

        .summary-table {
            width: 60%;
            margin-bottom: 30px;
        }

        .summary-table td:first-child {
            font-weight: bold;
            width: 40%;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 25px 0 10px 0;
            text-decoration: underline;
        }

        .footer-info {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print Laporan
    </button>

    <!-- Header -->
    <div class="header">
        <h1 class="company-name">ReUse Mart</h1>
        <p class="company-address">Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <!-- Report Title and Info -->
    <h2 class="report-title">LAPORAN Donasi Barang</h2>
    <div class="report-info">
        <p><strong>Tahun:</strong> {{ date('Y') }}</p>
        <p><strong>Tanggal cetak:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        @if(isset($data['period']))
        <p><strong>Periode:</strong> {{ $data['period'] }}</p>
        @endif
    </div>

    <!-- Summary Table -->
    <div class="section-title">RINGKASAN DONASI</div>
    <table class="summary-table">
        <tr>
            <td>Total Donasi Barang</td>
            <td class="text-right">{{ number_format($data['summary']['total_donasi'] ?? 0) }} item</td>
        </tr>
        <tr>
            <td>Total Nilai Donasi</td>
            <td class="text-right">Rp {{ number_format($data['summary']['total_nilai_donasi'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Request Donasi</td>
            <td class="text-right">{{ number_format($data['summary']['total_request'] ?? 0) }} request</td>
        </tr>
        <tr>
            <td>Organisasi Terlibat</td>
            <td class="text-right">{{ number_format($data['summary']['organisasi_terlibat'] ?? 0) }} organisasi</td>
        </tr>
        <tr>
            <td>Success Rate</td>
            <td class="text-right">{{ number_format($data['summary']['success_rate'] ?? 0, 1) }}%</td>
        </tr>
    </table>

    <!-- Detail Donasi Table -->
    @if(isset($data['donasi']) && count($data['donasi']) > 0)
    <div class="section-title">DETAIL DONASI BARANG</div>
    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Id Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Donasi</th>
                <th>Organisasi</th>
                <th>Nama Penerima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['donasi'] as $donasi)
            <tr>
                <td class="text-center">{{ $donasi['kode_produk'] ?? 'K' . str_pad($donasi['barang_id'] ?? 0, 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $donasi['nama_barang'] ?? 'N/A' }}</td>
                <td class="text-center">{{ $donasi['id_penitip_display'] ?? 'T' . str_pad($donasi['penitip_id'] ?? 0, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $donasi['penitip_nama'] ?? 'N/A' }}</td>
                <td class="text-center">{{ $donasi['tanggal_donasi'] ? \Carbon\Carbon::parse($donasi['tanggal_donasi'])->format('j/n/Y') : '-' }}</td>
                <td>{{ $donasi['nama_organisasi'] ?? 'N/A' }}</td>
                <td>{{ $donasi['nama_penerima'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Monthly Data -->
    @if(isset($data['monthly_data']) && count($data['monthly_data']) > 0)
    <div class="section-title">DATA DONASI BULANAN</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Jumlah Donasi</th>
                <th>Nilai Donasi (Rp)</th>
                <th>Rata-rata Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['monthly_data'] as $index => $monthly)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $monthly['month_name'] }}</td>
                <td class="text-center">{{ $monthly['count'] }}</td>
                <td class="text-right">{{ number_format($monthly['value'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($monthly['avg_value'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer-info">
        <p>Laporan ini digenerate secara otomatis pada {{ $data['generated_at'] ?? \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>ReuseMart</strong> - Marketplace Barang Bekas Berkualitas</p>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
