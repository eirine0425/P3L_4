<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Request Donasi - ReuseMart {{ date('Y') }}</title>

    <style>
        /* === SETTING KHUSUS CETAK === */
        @media print {
            @page   { margin: 2cm; size: A4; }
            .no-print { display: none !important; }
        }

        /* === GAYA UMUM === */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        /* === HEADER === */
        .header {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }

        .company-name   { font-size: 18px; font-weight: bold; margin: 0; }
        .company-address{ font-size: 12px; margin: 5px 0 0 0; }

        /* === JUDUL & INFO === */
        .report-title   { font-size: 16px; font-weight: bold; margin: 20px 0 5px 0; }
        .report-info p  { margin: 3px 0; }

        /* === TABEL === */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        /* === ALIGN KHUSUS === */
        .text-center { text-align: center; }
        .text-right  { text-align: right;  }

        /* === TOMBOL PRINT === */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        .print-button:hover { background: #0056b3; }

        /* === FOOTER === */
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

    <!-- Tombol cetak (tak muncul di hasil print) -->
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Laporan</button>

    <!-- HEADER -->
    <div class="header">
        <h1 class="company-name">ReUse Mart</h1>
        <p class="company-address">Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <!-- JUDUL & INFO -->
    <h2 class="report-title">LAPORAN REQUEST DONASI</h2>
    <div class="report-info">
        <p><strong>Tanggal cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        @if(!empty($data['period']))
            <p><strong>Periode:</strong> {{ $data['period'] }}</p>
        @endif
    </div>

    <!-- TABEL DATA REQUEST -->
    <table>
        <thead>
            <tr>
                <th>ID Organisasi</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Request</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['requests'] ?? [] as $request)
                <tr>
                    <td class="text-center">{{ $request['id_organisasi'] ?? 'ORG' . str_pad($request['organisasi_id'] ?? 0, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $request['nama_organisasi'] ?? '–' }}</td>
                    <td>{{ $request['alamat'] ?? '–' }}</td>
                    <td>{{ $request['request_description'] ?? '–' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding:20px;"><em>Tidak ada data request donasi</em></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer-info">
        <p>Laporan ini digenerate otomatis pada {{ $data['generated_at'] ?? \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>ReuseMart</strong> – Marketplace Barang Bekas Berkualitas</p>
    </div>

    <!-- Auto-print opsional -->
    <!-- <script> window.onload = () => window.print(); </script> -->
</body>
</html>
