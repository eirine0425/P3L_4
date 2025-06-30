<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Dashboard Owner - ReuseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { font-size: 12px; }
            .table { font-size: 11px; }
        }
        
        .header-info {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .kpi-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .chart-container {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header-info">
            <div class="row">
                <div class="col-6">
                    <h2>ReuseMart</h2>
                    <p>Laporan Dashboard Owner</p>
                </div>
                <div class="col-6 text-end">
                    <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
                    <p>Periode: {{ date('Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Print Button -->
        <div class="no-print mb-3">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Laporan
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
        
        <!-- KPI Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <h4>Ringkasan KPI</h4>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <h5 id="printTotalRevenue">Rp 0</h5>
                    <p>Total Pendapatan</p>
                </div>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <h5 id="printTotalProfit">Rp 0</h5>
                    <p>Total Keuntungan</p>
                </div>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <h5 id="printTotalSales">0</h5>
                    <p>Total Penjualan</p>
                </div>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <h5 id="printTotalUsers">0</h5>
                    <p>Total Pengguna</p>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-8">
                <div class="chart-container">
                    <h5>Tren Pendapatan Bulanan</h5>
                    <canvas id="printRevenueChart" width="600" height="300"></canvas>
                </div>
            </div>
            <div class="col-4">
                <div class="chart-container">
                    <h5>Kategori Terlaris</h5>
                    <canvas id="printCategoryChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tables -->
        <div class="page-break"></div>
        
        <div class="row mb-4">
            <div class="col-6">
                <h5>Produk Terlaris</h5>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Terjual</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="printTopProductsTable">
                        <tr><td colspan="3">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
            
            <div class="col-6">
                <h5>Penitip Terbaik</h5>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Penitip</th>
                            <th>Barang</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="printTopConsignorsTable">
                        <tr><td colspan="3">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="row mb-4">
            <div class="col-12">
                <h5>Transaksi Terbaru</h5>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pembeli</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody id="printRecentTransactionsTable">
                        <tr><td colspan="5">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Inventory Status -->
        <div class="row">
            <div class="col-6">
                <h5>Status Inventaris</h5>
                <table class="table table-bordered table-sm">
                    <tr>
                        <td>Barang Tersedia</td>
                        <td id="printAvailableItems">0</td>
                    </tr>
                    <tr>
                        <td>Barang Terjual</td>
                        <td id="printSoldItems">0</td>
                    </tr>
                    <tr>
                        <td>Barang Dititipkan</td>
                        <td id="printConsignedItems">0</td>
                    </tr>
                    <tr>
                        <td><strong>Total Nilai</strong></td>
                        <td><strong id="printTotalInventoryValue">Rp 0</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-6">
                <h5>Peringatan Batas Penitipan</h5>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Penitip</th>
                            <th>Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody id="printLowStockTable">
                        <tr><td colspan="3">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let printRevenueChart, printCategoryChart;
        
        $(document).ready(function() {
            loadPrintData();
            initializePrintCharts();
        });
        
        function loadPrintData() {
            $.ajax({
                url: '/api/dashboard/owner',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        updatePrintKPIs(response.data.kpis);
                        updatePrintCharts(response.data.monthly_data);
                        updatePrintTables(response.data);
                        updatePrintInventoryStatus(response.data.inventory);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading print data:', xhr.responseText);
                }
            });
        }
        
        function updatePrintKPIs(kpis) {
            $('#printTotalRevenue').text(formatCurrency(kpis.total_revenue || 0));
            $('#printTotalProfit').text(formatCurrency(kpis.total_profit || 0));
            $('#printTotalSales').text((kpis.total_sales || 0).toLocaleString());
            $('#printTotalUsers').text((kpis.total_users || 0).toLocaleString());
        }
        
        function updatePrintCharts(monthlyData) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const revenueData = new Array(12).fill(0);
            const profitData = new Array(12).fill(0);
            
            if (monthlyData.revenue) {
                monthlyData.revenue.forEach(item => {
                    revenueData[item.month - 1] = item.total || 0;
                });
            }
            
            if (monthlyData.profit) {
                monthlyData.profit.forEach(item => {
                    profitData[item.month - 1] = item.total || 0;
                });
            }
            
            if (printRevenueChart) {
                printRevenueChart.data.datasets[0].data = revenueData;
                printRevenueChart.data.datasets[1].data = profitData;
                printRevenueChart.update();
            }
            
            if (printCategoryChart && monthlyData.categories) {
                const categoryLabels = monthlyData.categories.map(cat => cat.nama_kategori || 'Unknown');
                const categoryData = monthlyData.categories.map(cat => cat.sales_count || 0);
                
                printCategoryChart.data.labels = categoryLabels;
                printCategoryChart.data.datasets[0].data = categoryData;
                printCategoryChart.update();
            }
        }
        
        function updatePrintTables(data) {
            // Top products
            let topProductsHtml = '';
            if (data.top_performers && data.top_performers.products) {
                data.top_performers.products.forEach(product => {
                    topProductsHtml += `
                        <tr>
                            <td>${product.nama_barang}</td>
                            <td>${product.sales_count || 0}</td>
                            <td>${formatCurrency((product.harga || 0) * (product.sales_count || 0))}</td>
                        </tr>
                    `;
                });
            }
            $('#printTopProductsTable').html(topProductsHtml || '<tr><td colspan="3">Tidak ada data</td></tr>');
            
            // Top consignors
            let topConsignorsHtml = '';
            if (data.top_performers && data.top_performers.consignors) {
                data.top_performers.consignors.forEach(consignor => {
                    topConsignorsHtml += `
                        <tr>
                            <td>${consignor.name || consignor.nama}</td>
                            <td>${consignor.barang_count || 0}</td>
                            <td>${formatCurrency(consignor.saldo || 0)}</td>
                        </tr>
                    `;
                });
            }
            $('#printTopConsignorsTable').html(topConsignorsHtml || '<tr><td colspan="3">Tidak ada data</td></tr>');
            
            // Recent transactions
            let recentTransactionsHtml = '';
            if (data.recent_activities && data.recent_activities.transactions) {
                data.recent_activities.transactions.forEach(transaction => {
                    recentTransactionsHtml += `
                        <tr>
                            <td>#${transaction.transaksi_id}</td>
                            <td>${transaction.pembeli?.user?.name || transaction.pembeli?.nama || '-'}</td>
                            <td>${formatCurrency(transaction.total_harga || 0)}</td>
                            <td>${transaction.status_transaksi}</td>
                            <td>${formatDate(transaction.tanggal_pesan)}</td>
                        </tr>
                    `;
                });
            }
            $('#printRecentTransactionsTable').html(recentTransactionsHtml || '<tr><td colspan="5">Tidak ada data</td></tr>');
        }
        
        function updatePrintInventoryStatus(inventory) {
            if (!inventory || !inventory.status) return;
            
            const status = inventory.status;
            $('#printAvailableItems').text(status.available_items || 0);
            $('#printSoldItems').text(status.sold_items || 0);
            $('#printConsignedItems').text(status.consigned_items || 0);
            $('#printTotalInventoryValue').text(formatCurrency(status.total_value || 0));
            
            // Low stock
            let lowStockHtml = '';
            if (inventory.low_stock) {
                inventory.low_stock.forEach(item => {
                    const daysLeft = Math.ceil((new Date(item.batas_penitipan) - new Date()) / (1000 * 60 * 60 * 24));
                    lowStockHtml += `
                        <tr>
                            <td>${item.nama_barang}</td>
                            <td>${item.penitip?.user?.name || item.penitip?.nama || '-'}</td>
                            <td>${daysLeft} hari</td>
                        </tr>
                    `;
                });
            }
            $('#printLowStockTable').html(lowStockHtml || '<tr><td colspan="3">Tidak ada peringatan</td></tr>');
        }
        
        function initializePrintCharts() {
            // Revenue Chart
            const revenueCtx = document.getElementById('printRevenueChart').getContext('2d');
            printRevenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: [],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Keuntungan',
                        data: [],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: false,
                    animation: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Category Chart
            const categoryCtx = document.getElementById('printCategoryChart').getContext('2d');
            printCategoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d']
                    }]
                },
                options: {
                    responsive: false,
                    animation: false
                }
            });
        }
        
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
        
        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID');
        }
    </script>
</body>
</html>