<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pengiriman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .delivery-info {
            margin-bottom: 30px;
        }
        .delivery-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .delivery-info table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .delivery-info table td:first-child {
            font-weight: bold;
            width: 200px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 70px;
            border-top: 1px solid #000;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKTI PENGIRIMAN</h1>
    </div>
    
    <div class="company-info">
        <p>PT. Nama Perusahaan<br>
        Alamat Perusahaan<br>
        Telepon: (021) 1234567<br>
        Email: info@perusahaan.com</p>
    </div>
    
    <div class="delivery-info">
        <table>
            <tr>
                <td>No. Pengiriman</td>
                <td>: {{ $pengiriman->pengiriman_id }}</td>
            </tr>
            <tr>
                <td>No. Transaksi</td>
                <td>: {{ $pengiriman->transaksi_id }}</td>
            </tr>
            <tr>
                <td>Tanggal Kirim</td>
                <td>: {{ $pengiriman->tanggal_kirim ? $pengiriman->tanggal_kirim->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td>Nama Penerima</td>
                <td>: {{ $pengiriman->nama_penerima }}</td>
            </tr>
            <tr>
                <td>Alamat Pengiriman</td>
                <td>: {{ $pengiriman->alamat ? $pengiriman->alamat->alamat_lengkap : '-' }}</td>
            </tr>
            <tr>
                <td>Status Pengiriman</td>
                <td>: {{ $pengiriman->status_pengiriman }}</td>
            </tr>
            <tr>
                <td>Kurir</td>
                <td>: {{ $pengiriman->kurir ? $pengiriman->kurir->nama : '-' }}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>: {{ $pengiriman->keterangan ?? '-' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Pengirim</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Penerima</p>
        </div>
    </div>
    
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i:s') }} dan merupakan bukti sah pengiriman barang.</p>
    </div>
</body>
</html>
