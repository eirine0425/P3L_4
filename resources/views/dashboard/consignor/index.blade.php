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

    <!-- Duration Statistics Cards -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $expiringSoonItems }}</h3>
                            <p>Segera Berakhir</p>
                            <small class="opacity-75">≤ 7 hari tersisa</small>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('consignor.items', ['durasi' => 'segera_berakhir']) }}" class="text-white text-decoration-none">
                        <small><i class="fas fa-eye me-1"></i>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $expiredItems }}</h3>
                            <p>Kadaluarsa</p>
                            <small class="opacity-75">Lewat batas penitipan</small>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('consignor.items', ['durasi' => 'kadaluarsa']) }}" class="text-white text-decoration-none">
                        <small><i class="fas fa-eye me-1"></i>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Menu Utama</h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('consignor.items') }}" class="btn btn-success btn-lg w-100 text-decoration-none" style="min-height: 100px;">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <i class="fas fa-boxes fa-3x mb-3"></i>
                                    <h6 class="mb-1 fw-bold">Kelola Barang Saya</h6>
                                    <small class="opacity-75">Lihat dan kelola barang titipan</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('consignor.transactions') }}" class="btn btn-info btn-lg w-100 text-decoration-none" style="min-height: 100px;">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                                    <h6 class="mb-1 fw-bold">Lihat Transaksi</h6>
                                    <small class="opacity-75">Riwayat penjualan barang</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Items and Items Need Attention -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Barang Terbaru</h5>
                    <a href="{{ route('consignor.items') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if(count($recentItems) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Durasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($item->nama_barang, 20) }}</strong><br>
                                                <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                                            </td>
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
                                            <td>
                                                <span class="badge {{ $item->status_durasi_badge_class }}">
                                                    {{ $item->formatted_sisa_waktu }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada barang</h6>
                            <p class="text-muted small">Barang titipan Anda akan muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
                <div class="card-body">
                    @if(count($itemsNeedAttention) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Status Durasi</th>
                                        <th>Sisa Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemsNeedAttention as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($item->nama_barang, 20) }}</strong><br>
                                                <small class="text-muted">{{ $item->kategori->nama_kategori ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $item->status_durasi_badge_class }}">
                                                    {{ $item->status_durasi_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="{{ $item->is_expired ? 'text-danger' : 'text-warning' }}">
                                                    {{ $item->formatted_sisa_waktu }}
                                                </strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('consignor.items.show', $item->barang_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(count($itemsByStatus) > 0)
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
@endif

@if(count($itemsByDuration) > 0)
<script>
    // Duration Chart
    const durationCtx = document.getElementById('durationChart').getContext('2d');
    const durationChart = new Chart(durationCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($itemsByDuration as $duration)
                    @if($duration->status == 'safe') 'Aman'
                    @elseif($duration->status == 'caution') 'Perhatian'
                    @elseif($duration->status == 'warning') 'Segera Berakhir'
                    @elseif($duration->status == 'expired') 'Kadaluarsa'
                    @endif{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($itemsByDuration as $duration)
                        {{ $duration->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($itemsByDuration as $duration)
                        @if($duration->status == 'safe') '#28a745'
                        @elseif($duration->status == 'caution') '#17a2b8'
                        @elseif($duration->status == 'warning') '#ffc107'
                        @elseif($duration->status == 'expired') '#dc3545'
                        @endif{{ !$loop->last ? ',' : '' }}
                    @endforeach
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
@endif
@endpush
