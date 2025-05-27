@extends('layouts.dashboard')

@section('title', 'Dashboard Penitip')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Penitip</h2>
            <p class="text-muted">Kelola barang titipan Anda</p>
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
                            <i class="fas fa-clock fa-2x"></i>
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
                            <i class="fas fa-check-circle fa-2x"></i>
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
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Aksi Cepat</h5>
                </div>
                <div class="card-body">
<<<<<<< Updated upstream
=======
                    <div class="row">
>>>>>>> Stashed changes
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('consignor.items') }}" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-boxes me-2"></i>Kelola Barang Saya
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('consignor.transactions') }}" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-chart-line me-2"></i>Lihat Transaksi
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
                    <a href="{{ route('consignor.items') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if(count($recentItems) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
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
                                                <strong>{{ $item->nama_barang }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($item->deskripsi, 30) }}</small>
                                            </td>
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
                                                <a href="{{ route('consignor.items.show', $item->barang_id) }}" 
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
</script>
@endpush
