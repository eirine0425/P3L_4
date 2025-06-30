<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
        }
        .header p {
            font-size: 12px;
            margin: 0;
            color: #666;
        }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
            width: 60%;
        }
        .summary-value {
            width: 40%;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }
        td {
            padding: 6px 8px;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            color: white;
        }
        .badge-primary {
            background-color: #007bff;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        .badge-success {
            background-color: #28a745;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .text-danger {
            color: #dc3545;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .category-summary, .penitip-summary {
            width: 48%;
            float: left;
            margin-right: 2%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Jl. Green Reuse Mart</p>
        <p>Tanggal Cetak: {{ $generated_at }}</p>
    </div>



      

    <div style="clear: both;"></div>

    <h3>Daftar Barang Masa Penitipan Habis</h3>
    <table>
        <thead>
            <tr>
               
                <th>Nama Barang</th>
                                    <th>Kode Produk</th>
                                    <th>ID Penitip</th>
                                    <th>Penitip</th>
                                    <th>Batas Penitipan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Batas Ambil</th>
                
            </tr>
        </thead>
        <tbody>
            
            <tr>
    <td>Meja</td>
    <td>P01</td>
    <td>1</td>
    <td>Penitip</td>
    <td>01 Jun 2025</td>
    <td>01 May 2025</td>
    <td>08 Jun 2025</td>
    
   
</tr>
<tr>
    <td>iPhone 13 Pro Max</td>
    <td>E01</td>
    <td>1</td>
    <td>Penitip</td>
    <td>01 Mei 2025</td>
    <td>01 April 2025</td>
    <td>08 Mei 2025</td>
    
    
</tr>
<tr>
    <td>MacBook Pro 14</td>
    <td>E02</td>
    <td>1</td>
    <td>Penitip</td>
    <td>03 Jun 2025</td>
    <td>03 May 2025</td>
   <td>10 Jun 2025</td>
    
</tr>
           
        </tbody>
    </table>

</body>
</html>
