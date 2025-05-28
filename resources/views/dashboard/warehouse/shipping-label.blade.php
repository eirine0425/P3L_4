<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman #{{ $transaction->transaksi_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .label-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .label-title {
            font-size: 18px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            background: #f0f0f0;
            padding: 5px;
            margin: -10px -10px 10px -10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .barcode {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            letter-spacing: 2px;
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print Label
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="label-container">
        <div class="header">
            <div class="company-name">REUSEMART</div>
            <div class="label-title">LABEL PENGIRIMAN</div>
        </div>

        <div class="section">
            <div class="section-title">INFORMASI PENGIRIMAN</div>
            <div class="info-row">
                <div class="info-label">ID Transaksi:</div>
                <div class="info-value">#{{ $transaction->transaksi_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal:</div>
                <div class="info-value">{{ now()->format('d M Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Metode:</div>
                <div class="info-value">
                    @if($transaction->pengiriman && $transaction->pengiriman->metode_pengiriman == 'Pickup')
                        PICKUP - Diambil di Toko
                    @else
                        DELIVERY - Dikirim ke Alamat
                    @endif
                </div>
            </div>
            @if($transaction->pengiriman && $transaction->pengiriman->nomor_resi)
                <div class="info-row">
                    <div class="info-label">Nomor Resi:</div>
                    <div class="info-value">{{ $transaction->pengiriman->nomor_resi }}</div>
                </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">PENGIRIM</div>
            <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">ReuseMart Store</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value">Jl. Raya Sesetan No. 123, Denpasar, Bali</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telepon:</div>
                <div class="info-value">(0361) 123-4567</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">PENERIMA</div>
            <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">{{ $transaction->pembeli->user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $transaction->pembeli->user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telepon:</div>
                <div class="info-value">{{ $transaction->pembeli->no_telp ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value">
                    @if($transaction->pengiriman && $transaction->pengiriman->alamat_pengiriman)
                        {{ $transaction->pengiriman->alamat_pengiriman }}
                    @elseif($transaction->pembeli->user->alamat->first())
                        {{ $transaction->pembeli->user->alamat->first()->alamat_lengkap }}
                    @else
                        Alamat belum tersedia
                    @endif
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">DETAIL BARANG</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kondisi</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->detailTransaksi as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->barang->nama_barang }}</td>
                            <td>{{ ucfirst($detail->barang->kondisi) }}</td>
                            <td>{{ $detail->jumlah ?? 1 }}</td>
                            <td>Rp {{ number_format($detail->barang->harga, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;">
                        <td colspan="4">TOTAL</td>
                        <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="barcode">
            {{ $transaction->transaksi_id }}{{ str_pad($transaction->pembeli->pembeli_id, 4, '0', STR_PAD_LEFT) }}
        </div>

        @if($transaction->pengiriman && $transaction->pengiriman->catatan)
            <div class="section">
                <div class="section-title">CATATAN</div>
                <div>{{ $transaction->pengiriman->catatan }}</div>
            </div>
        @endif

        <div class="footer">
            <p>Label ini dicetak pada {{ now()->format('d M Y H:i:s') }}</p>
            <p>Terima kasih telah berbelanja di ReuseMart!</p>
        </div>
    </div>
</body>
</html>
