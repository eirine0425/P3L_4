<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Penitip - ReuseMart</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; margin: 0; padding: 20px; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .report-container {
            border: 2px solid #000;
            padding: 15px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .company-address {
            font-size: 12px;
            margin: 2px 0 0 0;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0;
        }
        .report-info p {
            margin: 3px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }
        table th {
            background-color: #f5f5f5;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .no-print {
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Tombol Print dan Tutup -->
    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Laporan</button>
        <button class="btn btn-secondary" onclick="window.close()">❌ Tutup</button>
    </div>

    <div class="report-container">
        <!-- Informasi Header -->
        <div class="company-name">ReUse Mart</div>
        <div class="company-address">Jl. Green Eco Park No. 456 Yogyakarta</div>
        <div class="report-title">LAPORAN TRANSAKSI PENITIP</div>

        <div class="report-info">
            <p>ID Penitip : {{ isset($data['penitip_id']) ? $data['penitip_id'] : '-' }}</p>
            <p>Nama Penitip : {{ isset($data['penitip_nama']) ? $data['penitip_nama'] : '-' }}</p>
            <p>Bulan : {{ isset($data['bulan']) ? $data['bulan'] : '-' }}</p>
            <p>Tahun : {{ isset($data['tahun']) ? $data['tahun'] : '-' }}</p>
            <p>Tanggal cetak: {{ isset($data['generated_at']) ? $data['generated_at'] : date('d/m/Y H:i:s') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Laku</th>
                    <th>Harga Jual Bersih<br>(sudah dipotong Komisi)</th>
                    <th>Bonus terjual cepat</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_bersih = 0;
                    $total_bonus = 0;
                    $total_pendapatan = 0;
                    $transaksi_data = isset($data['transaksi_penitipan']) ? $data['transaksi_penitipan'] : [];
                @endphp

                @if(count($transaksi_data) > 0)
                    @foreach ($transaksi_data as $item)
                        @php
                            // Handle different data structures
                            $bersih = isset($item->harga_bersih) ? $item->harga_bersih : 
                                     (isset($item['harga_bersih']) ? $item['harga_bersih'] : 0);
                            $bonus = isset($item->bonus) ? $item->bonus : 
                                    (isset($item['bonus']) ? $item['bonus'] : 0);
                            $pendapatan = $bersih + $bonus;

                            $total_bersih += $bersih;
                            $total_bonus += $bonus;
                            $total_pendapatan += $pendapatan;

                            // Handle kode_produk
                            $kode_produk = isset($item->kode_produk) ? $item->kode_produk : 
                                          (isset($item['kode_produk']) ? $item['kode_produk'] : 
                                          (isset($item->barang_id) ? 'BRG-' . $item->barang_id : 
                                          (isset($item['barang_id']) ? 'BRG-' . $item['barang_id'] : '-')));

                            // Handle nama_produk
                            $nama_produk = isset($item->nama_produk) ? $item->nama_produk : 
                                          (isset($item['nama_produk']) ? $item['nama_produk'] : 
                                          (isset($item->nama_barang) ? $item->nama_barang : 
                                          (isset($item['nama_barang']) ? $item['nama_barang'] : '-')));

                            // Handle tanggal_masuk
                            $tanggal_masuk = isset($item->tanggal_masuk) ? $item->tanggal_masuk : 
                                            (isset($item['tanggal_masuk']) ? $item['tanggal_masuk'] : 
                                            (isset($item->tanggal_penitipan) ? $item->tanggal_penitipan : 
                                            (isset($item['tanggal_penitipan']) ? $item['tanggal_penitipan'] : null)));

                            // Handle tanggal_laku
                            $tanggal_laku = isset($item->tanggal_laku) ? $item->tanggal_laku : 
                                           (isset($item['tanggal_laku']) ? $item['tanggal_laku'] : 
                                           (isset($item->tanggal_pelunasan) ? $item->tanggal_pelunasan : 
                                           (isset($item['tanggal_pelunasan']) ? $item['tanggal_pelunasan'] : null)));
                        @endphp
                        <tr>
                            <td class="text-center">{{ $kode_produk }}</td>
                            <td>{{ $nama_produk }}</td>
                            <td class="text-center">
                                @if($tanggal_masuk)
                                    {{ \Carbon\Carbon::parse($tanggal_masuk)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tanggal_laku)
                                    {{ \Carbon\Carbon::parse($tanggal_laku)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">Rp {{ number_format($bersih, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($bonus, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    <tr class="total-row">
                        <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($total_bersih, 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($total_bonus, 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</strong></td>
                    </tr>
                @else
                    <tr>
                        <td colspan="7" class="no-data">Tidak ada data transaksi penitipan untuk periode ini</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if(count($transaksi_data) > 0)
        <div style="margin-top: 20px; font-size: 11px;">
            <p><strong>Ringkasan:</strong></p>
            <p>• Jumlah item terjual: {{ count($transaksi_data) }} produk</p>
            <p>• Total pendapatan bersih: Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</p>
        </div>
        @endif
    </div>

    <script>
        // Debug: Log data to console for troubleshooting
        console.log('Report data:', @json($data ?? []));
        
        // Auto-print if URL parameter is set
        if (window.location.search.includes('auto_print=1')) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
