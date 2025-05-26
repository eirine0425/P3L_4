@extends('layouts.dashboard')

@section('title', 'Owner Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Owner</h2>
            <p class="text-muted">Selamat datang di panel kontrol Owner ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                <p>Total Penjualan</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-box"></i>
                <h3>{{ $totalItems }}</h3>
                <p>Total Barang</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-users"></i>
                <h3>{{ $totalUsers }}</h3>
                <p>Total Pengguna</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>{{ $totalDonations }}</h3>
                <p>Total Donasi</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Penjualan Bulanan ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    @if(count($topProducts) > 0)
                        <ul class="list-group">
                            @foreach($topProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $product->nama_barang }}
                                    <span class="badge bg-primary rounded-pill">{{ $product->detail_transaksi_count }} terjual</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted">Belum ada data produk terlaris.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Komisi Bulanan ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyCommissionsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Penitip Terbaik</h5>
                </div>
                <div class="card-body">
                    @if(count($topConsignors) > 0)
                        <ul class="list-group">
                            @foreach($topConsignors as $consignor)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $consignor->user->name }}
                                    <div>
                                        <span class="badge bg-info me-1">{{ $consignor->barang_count }} barang</span>
                                        <span class="badge bg-success">Rp {{ number_format($consignor->transaksi_penitipan_sum_jumlah_penitipan, 0, ',', '.') }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted">Belum ada data penitip terbaik.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data untuk grafik penjualan bulanan
    var monthlySalesData = {
        labels: [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ],
        datasets: [{
            label: 'Penjualan (Rp)',
            backgroundColor: 'rgba(76, 175, 80, 0.2)',
            borderColor: 'rgba(76, 175, 80, 1)',
            borderWidth: 1,
            data: [
                @for($i = 1; $i <= 12; $i++)
                    {{ $monthlySales->where('month', $i)->first() ? $monthlySales->where('month', $i)->first()->total : 0 }}{{ $i < 12 ? ',' : '' }}
                @endfor
            ]
        }]
    };
    
    // Data untuk grafik komisi bulanan
    var monthlyCommissionsData = {
        labels: [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ],
        datasets: [{
            label: 'Komisi (Rp)',
            backgroundColor: 'rgba(33, 150, 243, 0.2)',
            borderColor: 'rgba(33, 150, 243, 1)',
            borderWidth: 1,
            data: [
                @for($i = 1; $i <= 12; $i++)
                    {{ $monthlyCommissions->where('month', $i)->first() ? $monthlyCommissions->where('month', $i)->first()->total : 0 }}{{ $i < 12 ? ',' : '' }}
                @endfor
            ]
        }]
    };
    
    // Render grafik penjualan bulanan
    var monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    var monthlySalesChart = new Chart(monthlySalesCtx, {
        type: 'bar',
        data: monthlySalesData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            }
        }
    });
    
    // Render grafik komisi bulanan
    var monthlyCommissionsCtx = document.getElementById('monthlyCommissionsChart').getContext('2d');
    var monthlyCommissionsChart = new Chart(monthlyCommissionsCtx, {
        type: 'line',
        data: monthlyCommissionsData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
