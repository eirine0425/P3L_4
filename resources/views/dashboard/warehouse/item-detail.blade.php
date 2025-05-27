@extends('layouts.dashboard')

@section('title', 'Item Detail')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2>Detail Barang: {{ $item->nama_barang }}</h2>
                <p class="text-muted">Informasi lengkap tentang barang.</p>
            </div>
            <div>
                <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('dashboard.warehouse.item.edit', $item->barang_id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Barang
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fas fa-check-circle"></i> Update Status
                </button>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($item->foto_barang && file_exists(storage_path('app/public/' . $item->foto_barang)))
                        <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="{{ $item->nama_barang }}" class="img-fluid mb-3" style="max-height: 300px;">
                    @else
                        <div class="no-image p-5 bg-light mb-3">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="mt-3">Tidak ada gambar</p>
                        </div>
                    @endif
                    
                    <h4>{{ $item->nama_barang }}</h4>
                    <p class="text-muted">{{ $item->kategori->nama_kategori ?? 'Kategori tidak tersedia' }}</p>
                    <h5 class="text-primary">Rp {{ number_format($item->harga, 0, ',', '.') }}</h5>
                    
                    <div class="mt-3">
                        @if($item->status == 'belum_terjual')
                            <span class="badge bg-success">Tersedia</span>
                        @elseif($item->status == 'terjual')
                            <span class="badge bg-info">Terjual</span>
                        @elseif($item->status == 'sold out')
                            <span class="badge bg-danger">Sold Out</span>
                        @else
                            <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Informasi Penitip -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Penitip</h5>
                </div>
                <div class="card-body">
                    @if($item->penitip)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle me-3">
                                <i class="fas fa-user fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $item->penitip->nama ?? $item->penitip->user->name ?? 'Nama tidak tersedia' }}</h6>
                                <small class="text-muted">Penitip</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="40%">Nama</th>
                                        <td>{{ $item->penitip->nama ?? $item->penitip->user->name ?? 'Tidak tersedia' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $item->penitip->user->email ?? 'Tidak tersedia' }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. KTP</th>
                                        <td>{{ $item->penitip->no_ktp ?? 'Tidak tersedia' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Point Donasi</th>
                                        <td>{{ $item->penitip->point_donasi ?? 0 }} poin</td>
                                    </tr>
                                    <tr>
                                        <th>Badge</th>
                                        <td>
                                            @if($item->penitip->badge)
                                                <span class="badge bg-warning">{{ $item->penitip->badge }}</span>
                                            @else
                                                <span class="text-muted">Belum ada badge</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Registrasi</th>
                                        <td>{{ $item->penitip->tanggal_registrasi ? \Carbon\Carbon::parse($item->penitip->tanggal_registrasi)->format('d M Y') : 'Tidak tersedia' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>Informasi penitip tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Barang</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">ID Barang</th>
                                    <td>#{{ $item->barang_id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $item->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $item->kategori->nama_kategori ?? 'Tidak tersedia' }}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Kondisi</th>
                                    <td>
                                        @switch($item->kondisi)
                                            @case('baru')
                                                <span class="badge bg-success">Baru</span>
                                                @break
                                            @case('layak')
                                                <span class="badge bg-primary">Layak</span>
                                                @break
                                            @case('sangat_layak')
                                                <span class="badge bg-info">Sangat Layak</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($item->kondisi) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if($item->status == 'belum_terjual')
                                            <span class="badge bg-success">Tersedia</span>
                                        @elseif($item->status == 'terjual')
                                            <span class="badge bg-info">Terjual</span>
                                        @elseif($item->status == 'sold out')
                                            <span class="badge bg-danger">Sold Out</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Penitipan</th>
                                    <td>{{ $item->tanggal_penitipan ? \Carbon\Carbon::parse($item->tanggal_penitipan)->format('d M Y') : 'Tidak tersedia' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Ditambahkan</th>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Rating</th>
                                    <td>
                                        @if($item->rating)
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ number_format($item->rating, 1) }}</span>
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $item->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        @else
                                            <span class="text-muted">Belum ada rating</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Deskripsi:</h6>
                        <p>{{ $item->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                    
                    @if($item->garansi)
                    <div class="mt-3">
                        <h6>Informasi Garansi:</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Serial Number</th>
                                <td>{{ $item->garansi->serial_number ?? 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>{{ $item->garansi->tanggal_mulai ? $item->garansi->tanggal_mulai->format('d M Y') : 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Berakhir</th>
                                <td>{{ $item->garansi->tanggal_berakhir ? $item->garansi->tanggal_berakhir->format('d M Y') : 'Tidak tersedia' }}</td>
                            </tr>
                            <tr>
                                <th>Ketentuan</th>
                                <td>{{ $item->garansi->ketentuan ?? 'Tidak tersedia' }}</td>
                            </tr>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Diskusi Produk</h5>
                </div>
                <div class="card-body">
                    @if($item->diskusi && $item->diskusi->count() > 0)
                        <div class="list-group">
                            @foreach($item->diskusi as $diskusi)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $diskusi->user->name ?? 'User tidak tersedia' }}</h6>
                                        <small>{{ $diskusi->created_at ? $diskusi->created_at->diffForHumans() : 'Waktu tidak tersedia' }}</small>
                                    </div>
                                    <p class="mb-1">{{ $diskusi->isi ?? 'Konten tidak tersedia' }}</p>
                                    @if($diskusi->balasan)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small class="text-muted">Balasan:</small>
                                            <p class="mb-0">{{ $diskusi->balasan }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada diskusi untuk produk ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Status Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.warehouse.item.update-status', $item->barang_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="belum_terjual" {{ $item->status == 'belum_terjual' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terjual" {{ $item->status == 'terjual' ? 'selected' : '' }}>Terjual</option>
                            <option value="sold out" {{ $item->status == 'sold out' ? 'selected' : '' }}>Sold Out</option>
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

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e9ecef;
}
</style>
@endsection
