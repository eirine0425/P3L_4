<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengambilan Barang - {{ $item->barang_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .subtitle {
            font-size: 14px;
            color: #555;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .item-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            display: inline-block;
            width: 80%;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
        }
        .barcode img {
            max-width: 250px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
            <div class="title">BUKTI PENGAMBILAN BARANG TITIPAN</div>
            <div class="subtitle">Nomor: PKP-{{ date('Ymd') }}-{{ $item->barang_id }}</div>
        </div>
        
        <div class="info-section">
            <div class="info-title">Informasi Pengambilan</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Tanggal Pengambilan:</span>
                    <span>{{ $item->formatted_tanggal_pengambilan }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Barang:</span>
                    <span>{{ $item->barang_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Pengambil:</span>
                    <span>{{ $item->nama_pengambil ?? $item->penitip->user->name ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Metode Pengambilan:</span>
                    <span>
                        @switch($item->metode_pengambilan)
                            @case('diambil_langsung')
                                Diambil Langsung
                                @break
                            @case('dikirim_kurir')
                                Dikirim via Kurir
                                @break
                            @case('dititipkan_pihak_lain')
                                Dititipkan ke Pihak Lain
                                @break
                            @default
                                {{ $item->metode_pengambilan }}
                        @endswitch
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pegawai:</span>
                    <span>{{ $item->pegawaiPickup->user->name ?? '-' }}</span>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-title">Informasi Barang</div>
            <table>
                <tr>
                    <th width="30%">Nama Barang</th>
                    <td>{{ $item->nama_barang }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kondisi</th>
                    <td>{{ ucfirst($item->kondisi) }}</td>
                </tr>
                <tr>
                    <th>Tanggal Penitipan</th>
                    <td>{{ $item->tanggal_penitipan ? date('d/m/Y', strtotime($item->tanggal_penitipan)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Batas Penitipan</th>
                    <td>{{ $item->batas_penitipan ? date('d/m/Y', strtotime($item->batas_penitipan)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Durasi Penitipan</th>
                    <td>
                        @if($item->tanggal_penitipan && $item->tanggal_pengambilan)
                            {{ \Carbon\Carbon::parse($item->tanggal_penitipan)->diffInDays(\Carbon\Carbon::parse($item->tanggal_pengambilan)) }} hari
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="info-section">
            <div class="info-title">Catatan Pengambilan</div>
            <p>{{ $item->catatan_pengambilan ?? 'Tidak ada catatan' }}</p>
        </div>
        
        <div class="barcode">
            {!! QrCode::size(150)->generate(route('dashboard.warehouse.pickup-detail', $item->barang_id)) !!}
            <div style="margin-top: 5px;">Scan untuk verifikasi</div>
        </div>
        
        <div class="signatures">
            <div class="signature-box">
                <div>Diserahkan oleh,</div>
                <div class="signature-line"></div>
                <div>{{ $item->pegawaiPickup->user->name ?? 'Pegawai Gudang' }}</div>
            </div>
            <div class="signature-box">
                <div>Diterima oleh,</div>
                <div class="signature-line"></div>
                <div>{{ $item->nama_pengambil ?? $item->penitip->user->name ?? 'Penitip/Perwakilan' }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Dokumen ini adalah bukti resmi pengambilan barang titipan. Harap simpan sebagai bukti.</p>
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4e73df; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak Bukti Pengambilan
        </button>
    </div>
</body>
</html>
