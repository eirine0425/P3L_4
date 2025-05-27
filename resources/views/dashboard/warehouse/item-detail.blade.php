@extends('layouts.dashboard')

@section('title', 'Item Detail')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2>Detail Barang: {{ $item->nama }}</h2>
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
                    @if($item->gambar)
                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="img-fluid mb-3" style="max-height: 300px;">
                    @else
                        <div class="no-image p-5 bg-light mb-3">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="mt-3">Tidak ada gambar</p>
                        </div>
                    @endif
                    
                    <h4>{{ $item->nama }}</h4>
                    <p class="text-muted">{{ $item->kategori->nama_kategori }}</p>
                    <h5 class="text-primary">Rp {{ number_format($item->harga, 0, ',', '.') }}</h5>
                    
                    <div class="mt-3">
                        @if($item->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                        @elseif($item->status == 'Tidak Aktif')
                            <span class="badge bg-danger">Tidak Aktif</span>
                        @else
                            <span class="badge bg-warning">Menunggu Verifikasi</span>
                        @endif
                    </div>
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
                                    <td>#{{ $item->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $item->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $item->kategori->nama_kategori }}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Stok</th>
                                    <td>{{ $item->stok }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if($item->status == 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @elseif($item->status == 'Tidak Aktif')
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @else
                                            <span class="badge bg-warning">Menunggu Verifikasi</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Ditambahkan</th>
                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td>{{ $item->updated_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Penitip</th>
                                    <td>{{ $item->penitip->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Garansi</th>
                                    <td>{{ $item->garansi ? 'Ya' : 'Tidak' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Deskripsi:</h6>
                        <p>{{ $item->deskripsi }}</p>
                    </div>
                    
                    @if($item->garansi)
                    <div class="mt-3">
                        <h6>Informasi Garansi:</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Serial Number</th>
                                <td>{{ $item->garansi->serial_number }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>{{ $item->garansi->tanggal_mulai->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Berakhir</th>
                                <td>{{ $item->garansi->tanggal_berakhir->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Ketentuan</th>
                                <td>{{ $item->garansi->ketentuan }}</td>
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
                    @if($item->diskusi->count() > 0)
                        <div class="list-group">
                            @foreach($item->diskusi as $diskusi)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $diskusi->user->name }}</h6>
                                        <small>{{ $diskusi->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $diskusi->isi }}</p>
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
@endsection
