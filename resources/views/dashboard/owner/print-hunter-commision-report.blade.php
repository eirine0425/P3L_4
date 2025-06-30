<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Komisi Hunter Bulanan - ReuseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .table { font-size: 11px; }
            @page { margin: 1cm; }
            .page-break { page-break-before: always; }
        }
        
        .header-info {
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-header {
            font-weight: bold;
            font-size: 16px;
        }
        
        .report-title {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin: 20px 0;
        }
        
        .summary-box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 12px;
            background-color: #f8f9fa;
        }
        
        .hunter-section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        
        .hunter-header {
            background-color: #e9ecef;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }
        
        .table-bordered th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        
        .performance-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-excellent { background-color: #d4edda; color: #155724; }
        .badge-good { background-color: #d1ecf1; color: #0c5460; }
        .badge-average { background-color: #fff3cd; color: #856404; }
        .badge-poor { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Print Button -->
        <div class="no-print mb-3">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Laporan
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
        
        <!-- Header -->
        <div class="header-info">
            <div class="company-header">
                ReUse Mart<br>
                Jl. Green Eco Park No. 456 Yogyakarta
            </div>
        </div>
        
        <!-- Report Title -->
        <div class="report-title">
            <strong>LAPORAN KOMISI HUNTER BULANAN</strong><br>
            Bulan: {{ $summary['month_name'] }} {{ $summary['year'] }}<br>
            Tanggal cetak: {{ $summary['generated_at'] }}
        </div>
        
        <!-- Summary Box -->
        <div class="summary-box">
            <div class="row">
                <div class="col-md-6">
                    <strong>Ringkasan Kinerja Hunter:</strong><br>
                    • Total Hunter: {{ $summary['total_hunters'] }} orang<br>
                    • Hunter Aktif: {{ $summary['active_hunters'] }} orang<br>
                    • Total Barang Terjual: {{ $summary['total_items_sold'] }} item<br>
                </div>
                <div class="col-md-6">
                    • Total Nilai Penjualan: Rp {{ number_format($summary['total_sales_value'], 0, ',', '.') }}<br>
                    • Total Komisi Dibayar: Rp {{ number_format($summary['total_commission_paid'], 0, ',', '.') }}<br>
                    • Rata-rata Komisi per Hunter: Rp {{ number_format($summary['avg_commission_per_hunter'], 0, ',', '.') }}<br>
                </div>
            </div>
        </div>
        
        <!-- Hunter Performance Summary Table -->
        <table class="table table-bordered table-sm mb-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Hunter</th>
                    <th>Email</th>
                    <th>Tanggal Bergabung</th>
                    <th>Total Item</th>
                    <th>Total Penjualan</th>
                    <th>Total Komisi</th>
                    <th>Rata-rata Harga</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hunterCommissions as $index => $hunter)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $hunter->hunter_name }}</strong><br>
                        <small>{{ $hunter->hunter_user_name }}</small>
                    </td>
                    <td class="text-left">{{ $hunter->hunter_email }}</td>
                    <td>{{ \Carbon\Carbon::parse($hunter->tanggal_bergabung)->format('d/m/Y') }}</td>
                    <td>{{ $hunter->total_items }}</td>
                    <td class="text-right">{{ number_format($hunter->total_sales_value, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($hunter->total_commission, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($hunter->avg_item_price, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $performance = 'poor';
                            $performanceText = 'Perlu Peningkatan';
                            if ($hunter->total_commission >= 500000) {
                                $performance = 'excellent';
                                $performanceText = 'Sangat Baik';
                            } elseif ($hunter->total_commission >= 200000) {
                                $performance = 'good';
                                $performanceText = 'Baik';
                            } elseif ($hunter->total_commission >= 50000) {
                                $performance = 'average';
                                $performanceText = 'Cukup';
                            }
                        @endphp
                        <span class="performance-badge badge-{{ $performance }}">{{ $performanceText }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data hunter untuk periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Detailed Hunter Reports -->
        @foreach($hunterCommissions as $hunter)
            @if($hunter->total_items > 0)
            <div class="hunter-section page-break">
                <div class="hunter-header">
                    Detail Penjualan - {{ $hunter->hunter_name }} ({{ $hunter->hunter_user_name }})
                    <div class="float-end">
                        Periode: {{ $hunter->first_sale_date ? \Carbon\Carbon::parse($hunter->first_sale_date)->format('d/m/Y') : '-' }} - 
                        {{ $hunter->last_sale_date ? \Carbon\Carbon::parse($hunter->last_sale_date)->format('d/m/Y') : '-' }}
                    </div>
                </div>
                
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Penitip</th>
                            <th>Harga Jual</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Laku</th>
                            <th>Hari Jual</th>
                            <th>Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hunterDetails[$hunter->pegawai_id] as $item)
                        <tr>
                            <td>{{ $item->kode_produk }}</td>
                            <td class="text-left">{{ $item->nama_barang }}</td>
                            <td class="text-left">{{ $item->nama_kategori ?? '-' }}</td>
                            <td class="text-left">{{ $item->penitip_nama ?? '-' }}</td>
                            <td class="text-right">{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_laku)->format('d/m/Y') }}</td>
                            <td>{{ $item->days_to_sell }} hari</td>
                            <td class="text-right">{{ number_format($item->komisi_hunter, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada penjualan</td>
                        </tr>
                        @endforelse
                        
                        @if($hunterDetails[$hunter->pegawai_id]->count() > 0)
                        <tr style="font-weight: bold; background-color: #f8f9fa;">
                            <td colspan="4" class="text-right"><strong>Total</strong></td>
                            <td class="text-right">{{ number_format($hunterDetails[$hunter->pegawai_id]->sum('harga_jual'), 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                            <td class="text-right">{{ number_format($hunterDetails[$hunter->pegawai_id]->sum('komisi_hunter'), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                
                <!-- Hunter Performance Metrics -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <small>
                            <strong>Metrik Kinerja:</strong>
                            Rata-rata waktu jual: {{ $hunterDetails[$hunter->pegawai_id]->avg('days_to_sell') ? number_format($hunterDetails[$hunter->pegawai_id]->avg('days_to_sell'), 1) : '0' }} hari |
                            Item tercepat terjual: {{ $hunterDetails[$hunter->pegawai_id]->min('days_to_sell') ?? '0' }} hari |
                            Item terlama terjual: {{ $hunterDetails[$hunter->pegawai_id]->max('days_to_sell') ?? '0' }} hari
                        </small>
                    </div>
                </div>
            </div>
            @else
            <div class="hunter-section">
                <div class="hunter-header">
                    {{ $hunter->hunter_name }} ({{ $hunter->hunter_user_name }})
                </div>
                <div class="text-center py-3">
                    <em>Tidak ada penjualan pada periode ini</em><br>
                    <small>Bergabung sejak: {{ \Carbon\Carbon::parse($hunter->tanggal_bergabung)->format('d F Y') }}</small>
                </div>
            </div>
            @endif
        @endforeach
        
        <!-- Footer Notes -->
        <div class="mt-4" style="font-size: 10px; color: #666;">
            <strong>Catatan:</strong><br>
            • Komisi hunter dihitung 5% dari harga jual barang<br>
            • Performance rating berdasarkan total komisi: Sangat Baik (≥500rb), Baik (≥200rb), Cukup (≥50rb), Perlu Peningkatan (<50rb)<br>
            • Laporan ini menampilkan minimal 3 hunter teratas berdasarkan komisi
        </div>
    </div>
</body>
</html>
