@extends('layouts.dashboard')

@section('title', 'Warehouse Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Pegawai Gudang</h2>
            <p class="text-muted">Selamat datang di panel kontrol Pegawai Gudang ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-boxes"></i>
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
                <i class="fas fa-times-circle"></i>
                <h3>{{ $inactiveItems }}</h3>
                <p>Barang Tidak Aktif</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-clock"></i>
                <h3>{{ $pendingItems }}</h3>
                <p>Menunggu Verifikasi</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Barang Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Penitip</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentItems as $item)
                                    <tr>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>{{ $item->kategori->nama_kategori }}</td>
                                        <td>{{ $item->penitip->user->name }}</td>
                                        <td>
                                            @if($item->status == 'Aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($item->status == 'Tidak Aktif')
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @else
                                                <span class="badge bg-warning">Menunggu Verifikasi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-primary">Lihat Semua Barang</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengiriman yang Perlu Diproses</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Pengiriman</th>
                                    <th>Pembeli</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingShipments as $shipment)
                                    <tr>
                                        <td>#{{ $shipment->id }}</td>
                                        <td>{{ $shipment->transaksi->pembeli->user->name }}</td>
                                        <td>{{ Str::limit($shipment->alamat_pengiriman, 30) }}</td>
                                        <td><span class="badge bg-warning">Menunggu Pengiriman</span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-success"><i class="fas fa-truck"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.warehouse.shipments') }}" class="btn btn-primary">Lihat Semua Pengiriman</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Inventaris</h5>
                </div>
                <div class="card-body">
                    <canvas id="inventoryStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data untuk grafik status inventaris
    var inventoryStatusCtx = document.getElementById('inventoryStatusChart').getContext('2d');
    var inventoryStatusChart = new Chart(inventoryStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Tidak Aktif', 'Menunggu Verifikasi'],
            datasets: [{
                data: [{{ $activeItems }}, {{ $inactiveItems }}, {{ $pendingItems }}],
                backgroundColor: ['#4CAF50', '#F44336', '#FFC107']
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
