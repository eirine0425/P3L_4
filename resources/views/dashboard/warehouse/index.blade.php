@extends('layouts.dashboard')

@section('title', 'Dashboard Gudang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Gudang</h2>
            <p class="text-muted">Kelola inventaris dan barang titipan</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $totalItems }}</h3>
                            <p>Total Barang</p>
                        </div>
                        <div>
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $activeItems }}</h3>
                            <p>Belum Terjual</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $soldItems }}</h3>
                            <p>Terjual</p>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $soldOutItems }}</h3>
                            <p>Sold Out</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-plus me-2"></i>
                                Tambah Barang Titipan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-boxes me-2"></i>Kelola Inventaris
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('products.index') }}" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-store me-2"></i>Lihat Katalog
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-chart-bar me-2"></i>Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Items -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Barang Terbaru</h5>
                    <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if(count($recentItems) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Penitip</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                            </td>
                                            <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if($item->status == 'belum_terjual')
                                                    <span class="badge bg-success">Belum Terjual</span>
                                                @elseif($item->status == 'terjual')
                                                    <span class="badge bg-info">Terjual</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada barang yang terdaftar.</p>
                            <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Barang Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Barang</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Distribusi Kategori Barang</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($itemsByStatus as $status)
                    '{{ ucfirst(str_replace("_", " ", $status->status)) }}'{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($itemsByStatus as $status)
                        {{ $status->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: [
                    '#28a745', // Belum Terjual - Green
                    '#17a2b8', // Terjual - Blue
                    '#6c757d'  // Sold Out - Gray
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($itemsByCategory as $category)
                    '{{ $category->nama_kategori }}'{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Barang',
                data: [
                    @foreach($itemsByCategory as $category)
                        {{ $category->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: 'rgba(76, 175, 80, 0.8)',
                borderColor: 'rgba(76, 175, 80, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
