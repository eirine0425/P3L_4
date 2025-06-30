@extends('layouts.dashboard')

@section('title', 'Laporan Penjualan Bulanan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Laporan Penjualan Bulanan</h2>
            <p class="text-muted">Laporan penjualan bulanan ReuseMart</p>
            <div class="btn-group me-2">
                <button class="btn btn-primary" onclick="loadMonthlySalesReport()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
                <button class="btn btn-secondary" onclick="printMonthlySalesReport()">
                    <i class="fas fa-print me-1"></i> Print Laporan
                </button>
            </div>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="yearSelect">Pilih Tahun:</label>
                        <select id="yearSelect" class="form-select">
                            <option value="2023">2023</option>
                            <option value="2024" selected>2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading -->
    <div id="loadingIndicator" class="text-center py-5">
        <div class="spinner-border" role="status"></div>
        <p class="mt-2">Memuat data...</p>
    </div>
    
    <!-- Report Content -->
    <div id="reportContent" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Laporan Penjualan Bulanan <span id="reportYear">2024</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th class="text-center">Jumlah Barang Terjual</th>
                                        <th class="text-end">Jumlah Penjualan Kotor</th>
                                    </tr>
                                </thead>
                                <tbody id="monthlySalesTableBody">
                                    <!-- Data will be populated here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center" id="totalItems">0</th>
                                        <th class="text-end" id="totalSales">Rp 0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Grafik Penjualan Bulanan <span id="chartYear">2024</span></h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 400px;">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error -->
    <div id="errorDisplay" style="display: none;" class="alert alert-danger">
        <h5>Error</h5>
        <p id="errorMessage"></p>
        <button class="btn btn-danger" onclick="loadMonthlySalesReport()">Retry</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesChart;

$(document).ready(function() {
    loadMonthlySalesReport();
    
    $('#yearSelect').change(function() {
        loadMonthlySalesReport();
    });
});

function loadMonthlySalesReport() {
    const year = $('#yearSelect').val();
    
    $('#loadingIndicator').show();
    $('#reportContent').hide();
    $('#errorDisplay').hide();
    
    $.ajax({
        url: '/api/dashboard/owner/monthly-sales-report',
        method: 'GET',
        data: { year: year },
        success: function(response) {
            if (response.status === 'success') {
                displayMonthlySalesReport(response.data);
                $('#loadingIndicator').hide();
                $('#reportContent').show();
            } else {
                showError('Invalid response format');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            showError('Failed to load monthly sales report');
        }
    });
}

function displayMonthlySalesReport(data) {
    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    // Update year in titles
    $('#reportYear').text(data.year);
    $('#chartYear').text(data.year);
    
    // Update table
    let tableHtml = '';
    const chartLabels = [];
    const chartData = [];
    
    for (let i = 1; i <= 12; i++) {
        const monthData = data.monthly_sales[i];
        tableHtml += `
            <tr>
                <td>${monthNames[i-1]}</td>
                <td class="text-center">${numberFormat(monthData.total_items)}</td>
                <td class="text-end">${formatCurrency(monthData.total_sales)}</td>
            </tr>
        `;
        
        chartLabels.push(monthNames[i-1].substring(0, 3));
        chartData.push(monthData.total_sales);
    }
    
    $('#monthlySalesTableBody').html(tableHtml);
    $('#totalItems').text(numberFormat(data.total_items));
    $('#totalSales').text(formatCurrency(data.total_sales));
    
    // Update chart
    updateChart(chartLabels, chartData);
}

function updateChart(labels, data) {
    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    
    if (salesChart) {
        salesChart.destroy();
    }
    
    salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan Bulanan',
                data: data,
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
                            return value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

function printMonthlySalesReport() {
    const year = $('#yearSelect').val();
    window.open(`/api/dashboard/owner/monthly-sales-report?year=${year}&print=true`, '_blank');
}

function showError(message) {
    $('#loadingIndicator').hide();
    $('#reportContent').hide();
    $('#errorMessage').text(message);
    $('#errorDisplay').show();
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount || 0);
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number || 0);
}
</script>
@endpush
