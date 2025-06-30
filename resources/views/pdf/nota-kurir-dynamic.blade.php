<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Kurir - {{ $transaction->transaksi_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
        }
        
        .nota-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        
        .nota-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .nota-info div {
            flex: 1;
        }
        
        .nota-number {
            font-size: 14px;
            font-weight: bold;
        }
        
        .customer-info {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .customer-info h3 {
            font-size: 13px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .total-section {
            float: right;
            width: 300px;
            margin-bottom: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }
        
        .total-row.final {
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
        }
        
        .points-qc-section {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        
        .points-info,
        .qc-info {
            border: 1px solid #000;
            padding: 10px;
            width: 48%;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 5px;
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
        
        @media print {
            .print-button {
                display: none;
            }
            
            .nota-container {
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: auto;
            }
            
            body {
                font-size: 11px;
            }
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Nota
    </button>

    <div class="nota-container">
        <!-- Header -->
        <div class="header">
            <h1>ATMA KITCHEN</h1>
            <p>Jl. Centralpark No. 10 Yogyakarta</p>
            <p>Telp: (0274) 123456 | Email: info@atmakitchen.com</p>
        </div>

        <!-- Nota Info -->
        <div class="nota-info">
            <div>
                <div class="nota-number">
                    No. Nota: {{ sprintf('%02d.%02d.%03d', 
                        \Carbon\Carbon::parse($transaction->created_at)->day,
                        \Carbon\Carbon::parse($transaction->created_at)->month,
                        $transaction->transaksi_id % 1000
                    ) }}
                </div>
                <div>Tanggal: {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</div>
            </div>
            <div style="text-align: right;">
                <div><strong>NOTA KURIR</strong></div>
                <div>ID Transaksi: #{{ $transaction->transaksi_id }}</div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="customer-info">
            <h3>INFORMASI PELANGGAN</h3>
            <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">{{ $transaction->pembeli->user->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $transaction->pembeli->user->email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telepon:</div>
                <div class="info-value">{{ $transaction->pembeli->user->phone ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value">
                    @if(isset($transaction->alamat))
                        {{ $transaction->alamat->alamat_lengkap ?? 'Alamat tidak tersedia' }}<br>
                        {{ $transaction->alamat->kota ?? '' }}, {{ $transaction->alamat->provinsi ?? '' }} {{ $transaction->alamat->kode_pos ?? '' }}
                    @elseif(isset($transaction->pembeli->alamat) && count($transaction->pembeli->alamat) > 0)
                        {{ $transaction->pembeli->alamat[0]->alamat_lengkap ?? 'Alamat tidak tersedia' }}<br>
                        {{ $transaction->pembeli->alamat[0]->kota ?? '' }}, {{ $transaction->pembeli->alamat[0]->provinsi ?? '' }} {{ $transaction->pembeli->alamat[0]->kode_pos ?? '' }}
                    @else
                        Alamat tidak tersedia
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 40%;">Nama Barang</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 20%;">Harga Satuan</th>
                    <th style="width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($transaction->detailTransaksi) && count($transaction->detailTransaksi) > 0)
                    @foreach($transaction->detailTransaksi as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $detail->barang->nama_barang ?? 'N/A' }}
                                @if(isset($detail->barang->kategoriBarang))
                                    <br><small style="color: #666;">({{ $detail->barang->kategoriBarang->nama_kategori }})</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $detail->kuantitas ?? 1 }}</td>
                            <td class="text-right">Rp {{ number_format($detail->barang->harga ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format(($detail->barang->harga ?? 0) * ($detail->kuantitas ?? 1), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada detail barang</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            @php
                $subtotal = 0;
                if(isset($transaction->detailTransaksi)) {
                    foreach($transaction->detailTransaksi as $detail) {
                        $subtotal += ($detail->barang->harga ?? 0) * ($detail->kuantitas ?? 1);
                    }
                }
                $ongkir = $transaction->ongkir ?? 0;
                $total = $subtotal + $ongkir;
            @endphp
            
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Ongkos Kirim:</span>
                <span>Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
            </div>
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Points and QC Section -->
        <div class="points-qc-section">
            <div class="points-info">
                <h4>INFORMASI POIN</h4>
                <div class="info-row">
                    <div class="info-label">Poin Didapat:</div>
                    <div class="info-value">{{ floor($total / 10000) }} poin</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Poin:</div>
                    <div class="info-value">{{ ($transaction->pembeli->total_poin ?? 0) + floor($total / 10000) }} poin</div>
                </div>
            </div>
            
            <div class="qc-info">
                <h4>QUALITY CONTROL</h4>
                <div class="info-row">
                    <div class="info-label">Diperiksa oleh:</div>
                    <div class="info-value">_________________</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal QC:</div>
                    <div class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">☐ Lolos ☐ Tidak Lolos</div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Kurir</strong></div>
                <div>{{ $transaction->pengiriman->kurir ?? 'Nama Kurir' }}</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Penerima</strong></div>
                <div>{{ $transaction->pembeli->user->name ?? 'Nama Penerima' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
            <p>Terima kasih atas kepercayaan Anda berbelanja di Atma Kitchen</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kesalahan dari pihak kami</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        
        // Handle print button click
        function printNote() {
            window.print();
        }
        
        // Close window after printing (optional)
        window.onafterprint = function() {
            // Uncomment the line below if you want to auto-close after printing
            // window.close();
        };
    </script>
</body>
</html>
