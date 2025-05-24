@extends('layouts.dashboard')

@section('title', 'Verifikasi Barang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Verifikasi Barang</h2>
                    <p class="text-muted">Verifikasi barang titipan yang masuk</p>
                </div>
                <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('warehouse.verification') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Barang/Penitip</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nama barang, kode, atau nama penitip...">
                            </div>
                            <div class="col-md-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->kategori_id }}" 
                                                {{ request('kategori') == $category->kategori_id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('warehouse.verification') }}" class="btn btn-outline-secondary">
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
                <div class="card-header">
                    <h5 class="card-title">Barang Menunggu Verifikasi ({{ $items->total() }})</h5>
                </div>
                <div class="card-body">
                    @if(count($items) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Informasi Barang</th>
                                        <th>Penitip</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Kondisi</th>
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
                                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong><br>
                                                <small class="text-muted">{{ $item->kode_barang }}</small><br>
                                                <small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $item->penitip->user->name ?? '-' }}</strong><br>
                                                <small class="text-muted">{{ $item->penitip->user->email ?? '-' }}</small>
                                            </td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $item->kondisi }}</span>
                                            </td>
                                            <td>
                                                {{ $item->created_at->format('d M Y') }}<br>
                                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <a href="{{ route('warehouse.verification.show', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-success mb-1" 
                                                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $item->barang_id }}">
                                                        <i class="fas fa-check me-1"></i>Setujui
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->barang_id }}">
                                                        <i class="fas fa-times me-1"></i>Tolak
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $item->barang_id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Setujui Barang</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('warehouse.verification.approve', $item->barang_id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menyetujui barang <strong>{{ $item->nama_barang }}</strong>?</p>
                                                            <div class="mb-3">
                                                                <label for="catatan{{ $item->barang_id }}" class="form-label">Catatan (Opsional)</label>
                                                                <textarea class="form-control" id="catatan{{ $item->barang_id }}" name="catatan" rows="3" 
                                                                          placeholder="Tambahkan catatan verifikasi..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check me-2"></i>Setujui
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $item->barang_id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Barang</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('warehouse.verification.reject', $item->barang_id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Berikan alasan penolakan untuk barang <strong>{{ $item->nama_barang }}</strong>:</p>
                                                            <div class="mb-3">
                                                                <label for="alasan{{ $item->barang_id }}" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="alasan{{ $item->barang_id }}" name="alasan_penolakan" rows="4" 
                                                                          placeholder="Jelaskan alasan penolakan..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-times me-2"></i>Tolak
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h4 class="text-muted">Tidak ada barang yang menunggu verifikasi</h4>
                            <p class="text-muted">Semua barang telah diverifikasi.</p>
                            <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
