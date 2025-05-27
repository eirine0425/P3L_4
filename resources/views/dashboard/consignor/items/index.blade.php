@extends('layouts.dashboard')

@section('title', 'Barang Saya')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Barang Saya</h2>
                    <p class="text-muted">Kelola semua barang titipan Anda</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('consignor.items') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Barang</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nama barang...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="Terjual" {{ request('status') == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('consignor.items') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(count($items) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
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
                                            <td>
                                                <a href="{{ route('consignor.items.show', $item->barang_id) }}" class="text-decoration-none">
                                                    <strong>{{ $item->nama_barang }}</strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $item->kode_barang }}</small>
                                            </td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $item->kondisi }}</span>
                                            </td>
                                            <td>
                                                @if($item->status_barang == 'Aktif')
                                                    <span class="badge bg-success">{{ $item->status_barang }}</span>
                                                @elseif($item->status_barang == 'Menunggu Verifikasi')
                                                    <span class="badge bg-warning">{{ $item->status_barang }}</span>
                                                @elseif($item->status_barang == 'Ditolak')
                                                    <span class="badge bg-danger">{{ $item->status_barang }}</span>
                                                @elseif($item->status_barang == 'Terjual')
                                                    <span class="badge bg-primary">{{ $item->status_barang }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $item->status_barang }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('consignor.items.show', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($item->status_barang != 'Terjual')
                                                        <a href="{{ route('consignor.items.edit', $item->barang_id) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('consignor.items.destroy', $item->barang_id) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum ada barang</h4>
                            <p class="text-muted">Mulai tambahkan barang titipan Anda sekarang!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection