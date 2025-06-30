<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Bulanan - ReUse Mart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
        }
        .report-header {
            padding: 10px 15px;
            border-bottom: 1px solid #000;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .report-meta {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .chart-container {
            margin-top: 30px;
            height: 300px;
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
    <div class="report-container">
        <div class="report-header">
            <h2 style="margin: 0;">ReUse Mart</h2>
            <p style="margin: 5px 0;">Jl. Green Eco Park No. 456 Yogyakarta</p>
            
            <div class="report-title">LAPORAN PENJUALAN BULANAN</div>
            <div class="report-meta">Tahun : {{ $year }}</div>
            <div class="report-meta">Tanggal cetak: {{ $print_date }}</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Barang Terjual</th>
                    <th>Jumlah Penjualan Kotor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $month => $monthData)
                <tr>
                    <td>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</td>
                    <td class="text-center">{{ number_format($monthData['total_items']) }}</td>
                    <td class="text-right">{{ number_format($monthData['total_sales']) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-center"><strong>{{ number_format($total_items) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($total_sales) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()">Print Laporan</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            const monthNames = [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
            
            const salesData = [
                @foreach($data as $month => $monthData)
                    {{ $monthData['total_sales'] }},
                @endforeach
            ];
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Penjualan Bulanan',
                        data: salesData,
                        backgroundColor: 'rgba(153, 153, 255, 0.7)',
                        borderColor: 'rgba(153, 153, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
