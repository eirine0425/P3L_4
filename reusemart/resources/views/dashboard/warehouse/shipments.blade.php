@extends('layouts.dashboard')

@section('title', 'Warehouse Shipments')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Pengiriman</h2>
            <p class="text-muted">Kelola semua pengiriman barang ReuseMart.</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('dashboard.warehouse.shipments') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari ID atau nama pembeli..." value="{{ request('search') }}">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Pengiriman" {{ request('status') == 'Menunggu Pengiriman' ? 'selected' : '' }}>Menunggu Pengiriman</option>
                        <option value="Sedang Dikirim" {{ request('status') == 'Sedang Dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                        <option value="Terkirim" {{ request('status') == 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                        <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-sort"></i> Urutkan
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.shipments', array_merge(request()->except('sort'), ['sort' => 'id_asc'])) }}">ID (Terendah)</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.shipments', array_merge(request()->except('sort'), ['sort' => 'id_desc'])) }}">ID (Tertinggi)</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.shipments', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}">Terbaru</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.shipments', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}">Terlama</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pembeli</th>
                                    <th>Alamat</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Tanggal Pengiriman</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipments as $shipment)
                                    <tr>
                                        <td>#{{ $shipment->id }}</td>
                                        <td>{{ $shipment->transaksi->pembeli->user->name }}</td>
                                        <td>{{ Str::limit($shipment->alamat_pengiriman, 30) }}</td>
                                        <td>{{ $shipment->transaksi->created_at->format('d M Y') }}</td>
                                        <td>{{ $shipment->tanggal_pengiriman ? $shipment->tanggal_pengiriman->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($shipment->status == 'Menunggu Pengiriman')
                                                <span class="badge bg-warning">Menunggu Pengiriman</span>
                                            @elseif($shipment->status == 'Sedang Dikirim')
                                                <span class="badge bg-info">Sedang Dikirim</span>
                                            @elseif($shipment->status == 'Terkirim')
                                                <span class="badge bg-success">Terkirim</span>
                                            @else
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('dashboard.warehouse.shipment.show', $shipment->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#statusModal{{ $shipment->id }}">
                                                    <i class="fas fa-truck"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Status Modal -->
                                            <div class="modal fade" id="statusModal{{ $shipment->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $shipment->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="statusModalLabel{{ $shipment->id }}">Update Status Pengiriman</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('dashboard.warehouse.shipment.update-status', $shipment->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label">Status</label>
                                                                    <select name="status" id="status" class="form-select">
                                                                        <option value="Menunggu Pengiriman" {{ $shipment->status == 'Menunggu Pengiriman' ? 'selected' : '' }}>Menunggu Pengiriman</option>
                                                                        <option value="Sedang Dikirim" {{ $shipment->status == 'Sedang Dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                                                                        <option value="Terkirim" {{ $shipment->status == 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                                                                        <option value="Dibatalkan" {{ $shipment->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $shipments->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
