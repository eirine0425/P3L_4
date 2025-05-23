@extends('layouts.dashboard')

@section('title', 'Consignor Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Penitip</h2>
            <p class="text-muted">Selamat datang di panel kontrol Penitip ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-box"></i>
                <h3>{{ $totalItems }}</h3>
                <p>Total Barang</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-check-circle"></i>
                <h3>{{ $activeItems }}</h3>
                <p>Barang Aktif</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-shopping-cart"></i>
                <h3>{{ $soldItems }}</h3>
                <p>Barang Terjual</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h3>
                <p>Total Pendapatan</p>
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Pribadi</h6>
                            <table class="table">
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $consignor->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $consignor->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon</th>
                                    <td>{{ $consignor->no_telepon }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Bergabung</th>
                                    <td>{{ $consignor->created_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Bank</h6>
                            <table class="table">
                                <tr>
                                    <th>Nama Bank</th>
                                    <td>{{ $consignor->nama_bank }}</td>
                                </tr>
                                <tr>
                                    <th>No. Rekening</th>
                                    <td>{{ $consignor->no_rekening }}</td>
                                </tr>
                                <tr>
                                    <th>Atas Nama</th>
                                    <td>{{ $consignor->nama_pemilik_rekening }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary">Edit Profil</a>
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
</script>
@endpush
