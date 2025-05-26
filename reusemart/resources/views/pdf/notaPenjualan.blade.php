<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan #{{ $transaction->transaksi_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .company-info {
            margin-top: 10px;
            color: #666;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info div {
            width: 48%;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .total-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-row.final {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
            height: 60px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak Nota
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <div class="company-name">REUSEMART</div>
        <div class="company-info">
            Jl. Contoh No. 123, Kota Contoh<br>
            Telp: (021) 1234-5678 | Email: info@reusemart.com
        </div>
    </div>

    <div class="invoice-info">
        <div>
            <div class="invoice-title">NOTA PENJUALAN</div>
            <div><span class="info-label">No. Transaksi:</span> #{{ $transaction->transaksi_id }}</div>
            <div><span class="info-label">Tanggal:</span> {{ $transaction->tanggal_pesan ? \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d M Y') : \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') }}</div>
            <div><span class="info-label">Status:</span> {{ $transaction->status_transaksi }}</div>
        </div>
        <div>
            <div class="invoice-title">PEMBELI</div>
            <div><span class="info-label">Nama:</span> {{ $transaction->pembeli->user->name }}</div>
            <div><span class="info-label">Email:</span> {{ $transaction->pembeli->user->email }}</div>
            <div><span class="info-label">Telepon:</span> {{ $transaction->pembeli->no_telp ?? '-' }}</div>
            @if($transaction->pengiriman)
                <div><span class="info-label">Alamat:</span> {{ $transaction->pengiriman->alamat_pengiriman ?? '-' }}</div>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->detailTransaksi as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $detail->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->detailTransaksi->sum('subtotal'), 0, ',', '.') }}</span>
        </div>
        @if($transaction->point_digunakan > 0)
            <div class="total-row">
                <span>Potongan Poin:</span>
                <span>- Rp {{ number_format($transaction->point_digunakan, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="total-row final">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
        </div>
        @if($transaction->point_diperoleh > 0)
            <div class="total-row" style="color: green; font-size: 11px;">
                <span>Poin Diperoleh:</span>
                <span>{{ $transaction->point_diperoleh }} poin</span>
            </div>
        @endif
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Penerima</div>
            <div class="signature-line"></div>
            <div>{{ $transaction->pembeli->user->name }}</div>
        </div>
        <div class="signature-box">
            <div>Petugas Gudang</div>
            <div class="signature-line"></div>
            <div>(_________________)</div>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda berbelanja di ReuseMart!</p>
        <p style="font-size: 10px;">Nota ini dicetak pada {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>
</html>