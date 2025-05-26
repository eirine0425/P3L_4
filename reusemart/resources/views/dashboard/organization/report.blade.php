@extends('layouts.dashboard')

@section('title', 'Laporan Donasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Laporan Donasi</h2>
            <p class="text-muted">Analisis dan statistik donasi yang diterima oleh organisasi Anda.</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Filter Laporan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.organization.reports') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-5">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Donasi Berdasarkan Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Donasi Berdasarkan Kategori</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tren Donasi Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ringkasan Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>Total Donasi (Periode Ini)</th>
                                    <td>{{ array_sum($statusData) }}</td>
                                </tr>
                                <tr>
                                    <th>Donasi Selesai</th>
                                    <td>{{ $statusData[array_search('Selesai', $statusLabels)] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Donasi Diproses</th>
                                    <td>{{ $statusData[array_search('Diproses', $statusLabels)] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Donasi Menunggu Pengambilan</th>
                                    <td>{{ $statusData[array_search('Menunggu Pengambilan', $statusLabels)] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Donasi Dibatalkan</th>
                                    <td>{{ $statusData[array_search('Dibatalkan', $statusLabels)] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori Paling Banyak</th>
                                    <td>{{ $categoryLabels[0] ?? 'Tidak ada data' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Unduh Laporan</h5>
                </div>
                <div class="card-body">
                    <p>Unduh laporan donasi dalam format berikut:</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard.organization.reports.export', ['format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Unduh PDF
                        </a>
                        <a href="{{ route('dashboard.organization.reports.export', ['format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Unduh Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($statusLabels) !!},
            datasets: [{
                data: {!! json_encode($statusData) !!},
                backgroundColor: [
                    '#28a745', // Success - Selesai
                    '#17a2b8', // Info - Diproses
                    '#ffc107', // Warning - Menunggu Pengambilan
                    '#dc3545'  // Danger - Dibatalkan
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
                label: 'Jumlah Donasi',
                data: {!! json_encode($categoryData) !!},
                backgroundColor: '#6f42c1',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
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
    
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [{
                label: 'Jumlah Donasi',
                data: {!! json_encode($monthData) !!},
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endsection
