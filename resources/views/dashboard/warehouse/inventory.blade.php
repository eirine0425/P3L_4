@extends('layouts.dashboard')

@section('title', 'Warehouse Inventory')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Inventaris Gudang</h2>
            <p class="text-muted">Kelola semua barang di gudang ReuseMart.</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('dashboard.warehouse.inventory') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="{{ request('search') }}">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
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
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.inventory', array_merge(request()->except('sort'), ['sort' => 'name_asc'])) }}">Nama (A-Z)</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.inventory', array_merge(request()->except('sort'), ['sort' => 'name_desc'])) }}">Nama (Z-A)</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.inventory', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}">Harga (Terendah)</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.inventory', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}">Harga (Tertinggi)</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.inventory', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}">Terbaru</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.warehouse.inventory', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}">Terlama</a></li>
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
                                    <th>Gambar</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Penitip</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @if($item->gambar)
                                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" width="50" height="50" class="img-thumbnail">
                                            @else
                                                <div class="no-image">No Image</div>
                                            @endif
                                        </td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->kategori->nama_kategori }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->stok }}</td>
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
                                            <div class="btn-group">
                                                <a href="{{ route('dashboard.warehouse.item.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#statusModal{{ $item->id }}">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Status Modal -->
                                            <div class="modal fade" id="statusModal{{ $item->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="statusModalLabel{{ $item->id }}">Update Status Barang</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('dashboard.warehouse.item.update-status', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label">Status</label>
                                                                    <select name="status" id="status" class="form-select">
                                                                        <option value="Aktif" {{ $item->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                                        <option value="Tidak Aktif" {{ $item->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                                                        <option value="Menunggu Verifikasi" {{ $item->status == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
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
                    {{ $items->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
