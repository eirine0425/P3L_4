<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan (dibawa oleh kurir) #{{ $transaction->transaksi_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            line-height: 1.3;
        }
        .receipt-container {
            border: 2px solid #000;
            padding: 15px;
            max-width: 400px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        .company-info {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .transaction-info {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 3px;
        }
        .info-label {
            width: 100px;
            flex-shrink: 0;
        }
        .info-separator {
            margin: 0 5px;
        }
        .customer-info {
            margin-bottom: 15px;
        }
        .items-section {
            margin-bottom: 15px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-section {
            margin-bottom: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-row.final {
            font-weight: bold;
        }
        .points-section {
            margin-bottom: 15px;
        }
        .qc-section {
            margin-bottom: 20px;
        }
        .signature-section {
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .signature-line {
            border-bottom: 1px dotted #000;
            height: 40px;
            margin-bottom: 5px;
        }
        .text-right {
            text-align: right;
        }
        .underline {
            text-decoration: underline;
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

    <div class="receipt-container">
        <div class="header">
            Nota Penjualan (dibawa oleh kurir)
        </div>

        <div class="company-info">
            <strong>Reuse Mart</strong><br>
            Jl. Green Eco Park No. 456 Yogyakarta
        </div>

        <div class="transaction-info">
            <div class="info-row">
                <span class="info-label">No Nota</span>
                <span class="info-separator">:</span>
                <span>{{ date('d.m.') }}{{ str_pad($transaction->transaksi_id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal pesan</span>
                <span class="info-separator">:</span>
                <span>{{ $transaction->tanggal_pesan ? \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d/m/Y H:i') : \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lunas pada</span>
                <span class="info-separator">:</span>
                <span>{{ $transaction->tanggal_pelunasan ? \Carbon\Carbon::parse($transaction->tanggal_pelunasan)->format('d/m/Y H:i') : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal kirim</span>
                <span class="info-separator">:</span>
                <span>{{ $transaction->pengiriman && $transaction->pengiriman->tanggal_kirim ? \Carbon\Carbon::parse($transaction->pengiriman->tanggal_kirim)->format('d/m/Y') : date('d/m/Y') }}</span>
            </div>
        </div>

        <div class="customer-info">
            <strong>Pembeli</strong> : {{ $transaction->pembeli->user->email }} / {{ $transaction->pembeli->user->name }}<br>
            @if($transaction->pengiriman && $transaction->pengiriman->alamat_pengiriman)
                {{ $transaction->pengiriman->alamat_pengiriman }}<br>
            @endif
            <strong>Delivery:</strong> Kurir ReUseMart ({{ $transaction->pengiriman && $transaction->pengiriman->pengirim ? $transaction->pengiriman->pengirim->name : 'Kurir' }})
        </div>

        <div class="items-section">
            @foreach($transaction->detailTransaksi as $detail)
                <div class="item-row">
                    <span>{{ $detail->barang->nama_barang }}</span>
                    <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Total</span>
                <span>{{ number_format($transaction->detailTransaksi->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Ongkos Kirim</span>
                <span>0</span>
            </div>
            <div class="total-row">
                <span>Total</span>
                <span>{{ number_format($transaction->detailTransaksi->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            @if($transaction->point_digunakan > 0)
                <div class="total-row">
                    <span>Potongan {{ $transaction->point_digunakan }} poin</span>
                    <span>- {{ number_format($transaction->point_digunakan, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row final">
                <span><strong>Total</strong></span>
                <span><strong>{{ number_format($transaction->total_harga, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <div class="points-section">
            @php
                $pointsEarned = $transaction->point_diperoleh ?? 0;
                $totalCustomerPoints = ($transaction->pembeli->total_poin ?? 0) + $pointsEarned;
                $bonusPoints = 0;
                if($transaction->total_harga > 500000) {
                    $bonusPoints = floor($transaction->total_harga / 500000) * floor($transaction->total_harga * 0.0002);
                }
            @endphp
            
            <div>Poin dari pesanan ini: {{ $pointsEarned }}</div>
            <div>Total poin customer: {{ $totalCustomerPoints }}</div>
            <br>
            <div>QC oleh: {{ $transaction->customerService ? $transaction->customerService->user->name : 'Farida' }} (P18)</div>
        </div>

        <div class="signature-section">
            <div style="margin-bottom: 10px;"><strong>Diterima oleh:</strong></div>
            <div class="signature-line"></div>
            <div class="info-row">
                <span>Tanggal: ...........................</span>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
