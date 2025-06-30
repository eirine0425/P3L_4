<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang Titipan - Print</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
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
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 12px;
            color: #666;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .print-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .summary {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-around;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            color: #007bff;
        }
        
        .filters {
            background-color: #e8f4f8;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        
        .filters h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-belum-terjual {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-terjual {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-sold-out {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print</button>
    
    <div class="header">
        <div class="company-name">{{ $company_name }}</div>
        <div class="company-info">
            {{ $company_address }}<br>
            Telp: {{ $company_phone }}
        </div>
        <div class="report-title">DAFTAR BARANG TITIPAN</div>
    </div>

    <div class="print-info">
        Dicetak pada: {{ $print_date }}
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total Barang</span>
            <div class="summary-value">{{ number_format($total_items) }}</div>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Nilai</span>
            <div class="summary-value">Rp {{ number_format($total_value, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <span class="summary-label">Rata-rata Harga</span>
            <div class="summary-value">Rp {{ $total_items > 0 ? number_format($total_value / $total_items, 0, ',', '.') : 0 }}</div>
        </div>
    </div>

    @if(!empty($filters) && array_filter($filters))
    <div class="filters">
        <h4>Filter yang Diterapkan:</h4>
        @if(!empty($filters['status']))
            <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}<br>
        @endif
        @if(!empty($filters['kategori']))
            <strong>Kategori ID:</strong> {{ $filters['kategori'] }}<br>
        @endif
        @if(!empty($filters['penitip']))
            <strong>Penitip ID:</strong> {{ $filters['penitip'] }}<br>
        @endif
        @if(!empty($filters['start_date']))
            <strong>Tanggal Mulai:</strong> {{ $filters['start_date'] }}<br>
        @endif
        @if(!empty($filters['end_date']))
            <strong>Tanggal Akhir:</strong> {{ $filters['end_date'] }}<br>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th width="20%">Nama Barang</th>
                <th width="15%">Penitip</th>
                <th width="12%">Kategori</th>
                <th width="10%">Harga</th>
                <th width="8%">Status</th>
                <th width="8%">Kondisi</th>
                <th width="12%">Tanggal Titip</th>
                <th width="7%">Durasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td class="text-center">{{ $item->barang_id }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->penitip->user->name ?? 'N/A' }}</td>
                <td>{{ $item->kategori->nama_kategori ?? 'N/A' }}</td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="status status-{{ str_replace(' ', '-', strtolower($item->status)) }}">
                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                    </span>
                </td>
                <td class="text-center">{{ ucfirst($item->kondisi) }}</td>
                <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->created_at->diffInDays(now()) }} hari</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data barang titipan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem {{ $company_name }}</p>
        <p>Halaman ini berisi {{ $items->count() }} dari {{ $total_items }} total barang titipan</p>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Close window after printing (optional)
        window.onafterprint = function() {
            // window.close();
        }
    </script>
</body>
</html>
