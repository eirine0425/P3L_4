@extends('layouts.dashboard')

@section('title', 'Owner Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Owner</h2>
            <p class="text-muted">Panel kontrol ReuseMart</p>
            <button class="btn btn-primary me-2" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button class="btn btn-secondary" onclick="exportReport()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('dashboard.owner.donasi.index') }}" class="btn btn-warning me-2">
                <i class="fas fa-clipboard-list me-1"></i> Donasi
            </a>
            <a href="{{ route('dashboard.owner.request-donasi.index') }}" class="btn btn-warning me-2">
                <i class="fas fa-clipboard-list me-1"></i> Request Donasi
            </a>
            <a href="{{ route('dashboard.owner.transaksi-penitipan.index') }}" class="btn btn-warning me-2">
                <i class="fas fa-clipboard-list me-1"></i> Transaksi Penitipan
            </a>
            <a href="{{ route('dashboard.owner.donasi-hunter.index') }}" class="btn btn-warning me-2">
                <i class="fas fa-clipboard-list me-1"></i> Hunter
            </a>
        </div>
    </div>
    
    <!-- Loading -->
    <div id="loadingIndicator" class="text-center py-5">
        <div class="spinner-border" role="status"></div>
        <p class="mt-2">Memuat data...</p>
    </div>
    
    <!-- Dashboard Content -->
    <div id="dashboardContent" style="display: none;">
        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 id="totalRevenue">Rp 0</h4>
                        <p class="mb-0">Total Pendapatan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 id="totalProfit">Rp 0</h4>
                        <p class="mb-0">Total Keuntungan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 id="totalSales">0</h4>
                        <p class="mb-0">Total Penjualan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 id="totalUsers">0</h4>
                        <p class="mb-0">Total Pengguna</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Tren Pendapatan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tables -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Produk Terlaris</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Terjual</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody id="topProductsTable">
                                <tr><td colspan="3" class="text-center">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Penitip Terbaik</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Penitip</th>
                                    <th>Barang</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="topConsignorsTable">
                                <tr><td colspan="3" class="text-center">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Transaksi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pembeli</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactionsTable">
                                <tr><td colspan="5" class="text-center">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Inventory Status -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Status Inventaris</h5>
                    </div>
                    <div class="card-body">
                        <p>Barang Tersedia: <span id="availableItems">0</span></p>
                        <p>Barang Terjual: <span id="soldItems">0</span></p>
                        <p>Barang Dititipkan: <span id="consignedItems">0</span></p>
                        <hr>
                        <h5>Total Nilai: <span id="totalInventoryValue">Rp 0</span></h5>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Peringatan Batas Penitipan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Penitip</th>
                                    <th>Sisa Hari</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockTable">
                                <tr><td colspan="3" class="text-center">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error -->
    <div id="errorDisplay" style="display: none;" class="alert alert-danger">
        <h5>Error</h5>
        <p id="errorMessage"></p>
        <button class="btn btn-danger" onclick="loadDashboardData()">Retry</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart, categoryChart;

$(document).ready(function() {
    loadDashboardData();
    initializeCharts();
});

function loadDashboardData() {
    $('#loadingIndicator').show();
    $('#dashboardContent').hide();
    $('#errorDisplay').hide();
    
    $.ajax({
        url: '/api/dashboard/owner',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.status === 'success') {
                updateKPIs(response.data.kpis);
                updateCharts(response.data.monthly_data);
                updateTables(response.data);
                updateInventoryStatus(response.data.inventory);
                
                $('#loadingIndicator').hide();
                $('#dashboardContent').show();
            } else {
                showError('Invalid response format');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            showError('Failed to load dashboard data');
        }
    });
}

function showError(message) {
    $('#loadingIndicator').hide();
    $('#dashboardContent').hide();
    $('#errorMessage').text(message);
    $('#errorDisplay').show();
}

function updateKPIs(kpis) {
    $('#totalRevenue').text(formatCurrency(kpis.total_revenue || 0));
    $('#totalProfit').text(formatCurrency(kpis.total_profit || 0));
    $('#totalSales').text((kpis.total_sales || 0).toLocaleString());
    $('#totalUsers').text((kpis.total_users || 0).toLocaleString());
}

function updateCharts(monthlyData) {
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
    
    if (revenueChart) {
        revenueChart.data.datasets[0].data = revenueData;
        revenueChart.data.datasets[1].data = profitData;
        revenueChart.update();
    }
    
    if (categoryChart && monthlyData.categories) {
        const categoryLabels = monthlyData.categories.map(cat => cat.nama_kategori || 'Unknown');
        const categoryData = monthlyData.categories.map(cat => cat.sales_count || 0);
        
        categoryChart.data.labels = categoryLabels;
        categoryChart.data.datasets[0].data = categoryData;
        categoryChart.update();
    }
}

function updateTables(data) {
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
    $('#topProductsTable').html(topProductsHtml || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
    
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
    $('#topConsignorsTable').html(topConsignorsHtml || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
    
    // Recent transactions
    let recentTransactionsHtml = '';
    if (data.recent_activities && data.recent_activities.transactions) {
        data.recent_activities.transactions.forEach(transaction => {
            recentTransactionsHtml += `
                <tr>
                    <td>#${transaction.transaksi_id}</td>
                    <td>${transaction.pembeli?.user?.name || transaction.pembeli?.nama || '-'}</td>
                    <td>${formatCurrency(transaction.total_harga || 0)}</td>
                    <td><span class="badge bg-${getStatusColor(transaction.status_transaksi)}">${transaction.status_transaksi}</span></td>
                    <td>${formatDate(transaction.tanggal_pesan)}</td>
                </tr>
            `;
        });
    }
    $('#recentTransactionsTable').html(recentTransactionsHtml || '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
}

function updateInventoryStatus(inventory) {
    if (!inventory || !inventory.status) return;
    
    const status = inventory.status;
    $('#availableItems').text(status.available_items || 0);
    $('#soldItems').text(status.sold_items || 0);
    $('#consignedItems').text(status.consigned_items || 0);
    $('#totalInventoryValue').text(formatCurrency(status.total_value || 0));
    
    // Low stock
    let lowStockHtml = '';
    if (inventory.low_stock) {
        inventory.low_stock.forEach(item => {
            const daysLeft = Math.ceil((new Date(item.batas_penitipan) - new Date()) / (1000 * 60 * 60 * 24));
            lowStockHtml += `
                <tr>
                    <td>${item.nama_barang}</td>
                    <td>${item.penitip?.user?.name || item.penitip?.nama || '-'}</td>
                    <td><span class="badge bg-${daysLeft <= 3 ? 'danger' : 'warning'}">${daysLeft} hari</span></td>
                </tr>
            `;
        });
    }
    $('#lowStockTable').html(lowStockHtml || '<tr><td colspan="3" class="text-center">Tidak ada peringatan</td></tr>');
}

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
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
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function refreshDashboard() {
    loadDashboardData();
}

function exportReport() {
    window.open('/dashboard/owner/print-report', '_blank');
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

function getStatusColor(status) {
    const colors = {
        'selesai': 'success',
        'diproses': 'warning',
        'dibatalkan': 'danger',
        'pending': 'secondary'
    };
    return colors[status] || 'secondary';
}
</script>
@endpush
