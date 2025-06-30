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
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 10px;
            color: #666;
        }
        .nota-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-decoration: underline;
        }
        .summary-info {
            margin-bottom: 20px;
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .summary-row {
            display: flex;
            margin-bottom: 5px;
        }
        .summary-label {
            width: 150px;
            font-weight: bold;
        }
        .summary-value {
            flex: 1;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .terms {
            margin-top: 30px;
            font-size: 9px;
            page-break-inside: avoid;
        }
        .terms h4 {
            margin-bottom: 10px;
            font-size: 11px;
        }
        .terms ul {
            margin: 0;
            padding-left: 15px;
        }
        .terms li {
            margin-bottom: 3px;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">REUSEMART</div>
        <div class="company-info">
            Jl. Contoh No. 123, Kota Contoh<br>
            Telp: (021) 1234-5678 | Email: info@reusemart.com
        </div>
    </div>

    <div class="nota-title">NOTA PENITIPAN BARANG - BULK</div>

    <div class="summary-info">
        <div class="summary-row">
            <div class="summary-label">Tanggal Cetak:</div>
            <div class="summary-value">{{ $tanggalCetak->format('d F Y H:i:s') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Petugas:</div>
            <div class="summary-value">{{ $petugas }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Barang:</div>
            <div class="summary-value">{{ $totalItems }} item</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Nilai:</div>
            <div class="summary-value">Rp {{ number_format($items->sum('harga'), 0, ',', '.') }}</div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">ID Barang</th>
                <th width="25%">Nama Barang</th>
                <th width="20%">Penitip</th>
                <th width="15%">Kategori</th>
                <th width="10%">Kondisi</th>
                <th width="10%">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->barang_id }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->penitip->user->name ?? '-' }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->kondisi)) }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div>Total Nilai Barang: Rp {{ number_format($items->sum('harga'), 0, ',', '.') }}</div>
        <div>Total Item: {{ $totalItems }} barang</div>
    </div>

    <div class="terms">
        <h4>SYARAT DAN KETENTUAN PENITIPAN:</h4>
        <ul>
            <li>Semua barang yang tercantum dalam daftar ini telah diterima dalam kondisi sesuai yang tertera.</li>
            <li>Komisi penjualan sebesar 20% dari harga jual akan dipotong dari hasil penjualan.</li>
            <li>Masa penitipan maksimal 3 bulan dari tanggal penitipan masing-masing barang.</li>
            <li>Jika barang tidak terjual dalam masa penitipan, penitip wajib mengambil kembali barang.</li>
            <li>Barang yang tidak diambil setelah masa penitipan berakhir akan menjadi donasi.</li>
            <li>Reusemart tidak bertanggung jawab atas kerusakan barang akibat force majeure.</li>
            <li>Pembayaran hasil penjualan akan dilakukan maksimal 7 hari setelah barang terjual.</li>
            <li>Nota ini berlaku sebagai bukti penerimaan barang titipan.</li>
        </ul>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Petugas Gudang</div>
            <div class="signature-line">
                {{ $petugas }}
            </div>
        </div>
        <div class="signature-box">
            <div>Supervisor</div>
            <div class="signature-line">
                ___________________
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Nota ini dicetak pada {{ $tanggalCetak->format('d F Y H:i:s') }}</p>
        <p>Dokumen ini adalah bukti resmi penerimaan barang titipan</p>
    </div>
</body>
</html>
