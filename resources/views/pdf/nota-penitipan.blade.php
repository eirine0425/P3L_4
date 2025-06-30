<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penitipan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
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
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .item-details {
            border: 1px solid #000;
            margin: 20px 0;
        }
        .item-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-details th,
        .item-details td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .item-details th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .terms {
            margin-top: 30px;
            font-size: 10px;
        }
        .terms h4 {
            margin-bottom: 10px;
            font-size: 12px;
        }
        .terms ul {
            margin: 0;
            padding-left: 20px;
        }
        .terms li {
            margin-bottom: 5px;
        }
        .signature-section {
            margin-top: 40px;
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
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
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

    <div class="nota-title">NOTA PENITIPAN BARANG</div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No. Nota:</div>
            <div class="info-value">{{ $item->barang_id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Penitipan:</div>
            <div class="info-value">{{ $item->created_at->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Penitip:</div>
            <div class="info-value">{{ $item->penitip->user->name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">No. Telepon:</div>
            <div class="info-value">{{ $item->penitip->user->phone ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $item->penitip->user->email ?? '-' }}</div>
        </div>
    </div>

    <div class="item-details">
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Kondisi</th>
                    <th>Harga Jual</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->kondisi)) }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Deskripsi Barang:</div>
        </div>
        <div style="margin-top: 10px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
            {{ $item->deskripsi }}
        </div>
    </div>

    <div class="terms">
        <h4>SYARAT DAN KETENTUAN PENITIPAN:</h4>
        <ul>
            <li>Barang yang dititipkan akan dijual dengan harga yang telah disepakati.</li>
            <li>Komisi penjualan sebesar 20% dari harga jual akan dipotong dari hasil penjualan.</li>
            <li>Masa penitipan maksimal 3 bulan dari tanggal penitipan.</li>
            <li>Jika barang tidak terjual dalam masa penitipan, penitip wajib mengambil kembali barang.</li>
            <li>Barang yang tidak diambil setelah masa penitipan berakhir akan menjadi donasi.</li>
            <li>Reusemart tidak bertanggung jawab atas kerusakan barang akibat force majeure.</li>
            <li>Pembayaran hasil penjualan akan dilakukan maksimal 7 hari setelah barang terjual.</li>
        </ul>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Penitip</div>
            <div class="signature-line">
                {{ $item->penitip->user->name ?? '___________________' }}
            </div>
        </div>
        <div class="signature-box">
            <div>Petugas Gudang</div>
            <div class="signature-line">
                ___________________
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Nota ini dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Simpan nota ini sebagai bukti penitipan barang</p>
    </div>
</body>
</html>
