<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0 3px 0;
        }
        .subtitle {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }
        .period {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 15px 0;
            border-radius: 3px;
        }
        .summary-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            color: #495057;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 3px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .summary-label {
            font-weight: bold;
            width: 70%;
        }
        .summary-value {
            text-align: right;
            width: 30%;
            font-family: monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .currency { 
            font-family: monospace; 
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .dummy-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 8px;
            margin: 10px 0;
            border-radius: 3px;
            font-size: 9px;
            color: #856404;
        }
        .performance-good { color: #28a745; font-weight: bold; }
        .performance-average { color: #ffc107; font-weight: bold; }
        .performance-poor { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name }}</div>
        <div class="company-address">{{ $company_address }}</div>
        <div class="report-title">{{ $report_title }}</div>
        <div class="subtitle">{{ $subtitle }}</div>
        <div class="period">{{ $period }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-title">Summary</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Sales</div>
                <div class="summary-cell summary-value">{{ $total_sales }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Products</div>
                <div class="summary-cell summary-value">{{ $total_products }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Average Sales per Product</div>
                <div class="summary-cell summary-value">{{ $average_sales_per_product }}</div>
            </div>
        </div>
    </div>
    <div class="section-title">Sales Report by Category</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Product</th>
                <th>Quantity Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales_data as $data)
            <tr>
                <td>{{ $data->category_name }}</td>
                <td>{{ $data->product_name }}</td>
                <td class="text-right">{{ $data->quantity_sold }}</td>
                <td class="currency">{{ $data->total_sales }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Generated on {{ date('d M Y H:i:s') }}
    </div>
</body>
</html>