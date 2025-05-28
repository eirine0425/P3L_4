@extends('layouts.dashboard')

@section('title', 'Inventaris Gudang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Inventaris Gudang</h2>
                    <p class="text-muted">Kelola semua barang titipan</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Barang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.inventory') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Cari nama barang atau penitip..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="belum_terjual" {{ request('status') == 'belum_terjual' ? 'selected' : '' }}>Belum Terjual</option>
                                    <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                    <option value="sold out" {{ request('status') == 'sold out' ? 'selected' : '' }}>Sold Out</option>
                                    <option value="untuk_donasi" {{ request('status') == 'untuk_donasi' ? 'selected' : '' }}>Untuk Donasi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->kategori_id }}" {{ request('kategori') == $category->kategori_id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="filter" class="form-select">
                                    <option value="">Semua Barang</option>
                                    <option value="needs_attention" {{ request('filter') == 'needs_attention' ? 'selected' : '' }}>Perlu Perhatian</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-success">Filter</button>
                                    <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-boxes me-2"></i>Daftar Barang
                        <span class="badge bg-primary">{{ $items->total() }} barang</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($items) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>ID Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Penitip</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Kondisi</th>
                                        <th>Durasi Penitipan</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                @if($item->foto_barang)
                                                    <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                                                         alt="{{ $item->nama_barang }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td><strong>{{ $item->barang_id }}</strong></td>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                <br><small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                            </td>
                                            <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if($item->status == 'belum_terjual')
                                                    <span class="badge bg-success">Belum Terjual</span>
                                                @elseif($item->status == 'terjual')
                                                    <span class="badge bg-info">Terjual</span>
                                                @elseif($item->status == 'sold out')
                                                    <span class="badge bg-secondary">Sold Out</span>
                                                @else
                                                    <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->kondisi == 'baru')
                                                    <span class="badge bg-primary">Baru</span>
                                                @elseif($item->kondisi == 'sangat_layak')
                                                    <span class="badge bg-success">Sangat Layak</span>
                                                @else
                                                    <span class="badge bg-warning">Layak</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->tanggal_penitipan)
                                                    <div class="d-flex flex-column">
                                                        <span class="badge {{ $item->status_durasi_badge_class }} mb-1">
                                                            {{ $item->status_durasi_text }}
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $item->formatted_sisa_waktu }}
                                                        </small>
                                                        @if($item->batas_penitipan)
                                                            <small class="text-muted">
                                                                Batas: {{ \Carbon\Carbon::parse($item->batas_penitipan)->format('d M Y') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.warehouse.item.edit', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit Barang">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('products.show', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-info" target="_blank" title="Lihat di Katalog">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }} 
                                dari {{ $items->total() }} barang
                            </div>
                            <div>
                                {{ $items->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada barang ditemukan</h5>
                            <p class="text-muted">Belum ada barang yang sesuai dengan kriteria pencarian.</p>
                            <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Barang Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
