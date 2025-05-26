@extends('layouts.dashboard')

@section('title', 'Detail Donasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Detail Donasi</h2>
            <p class="text-muted">Informasi lengkap tentang donasi yang diterima.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Donasi</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">ID Donasi</div>
                        <div class="col-md-8">{{ $donation->donasi_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Donasi</div>
                        <div class="col-md-8">{{ $donation->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status</div>
                        <div class="col-md-8">
                            @if($donation->status == 'Menunggu Pengambilan')
                                <span class="badge bg-warning">Menunggu Pengambilan</span>
                            @elseif($donation->status == 'Diproses')
                                <span class="badge bg-info">Diproses</span>
                            @elseif($donation->status == 'Selesai')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Catatan</div>
                        <div class="col-md-8">{{ $donation->catatan ?? 'Tidak ada catatan' }}</div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="fw-bold mb-3">Informasi Barang</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nama Barang</div>
                        <div class="col-md-8">{{ $donation->barang->nama_barang }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Deskripsi</div>
                        <div class="col-md-8">{{ $donation->barang->deskripsi }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Kondisi</div>
                        <div class="col-md-8">{{ $donation->barang->kondisi }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Kategori</div>
                        <div class="col-md-8">{{ $donation->barang->kategori->nama_kategori }}</div>
                    </div>
                    
                    @if($donation->barang->gambar_path)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Gambar</div>
                        <div class="col-md-8">
                            <img src="{{ asset($donation->barang->gambar_path) }}" alt="{{ $donation->barang->nama_barang }}" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <h6 class="fw-bold mb-3">Informasi Donatur</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nama</div>
                        <div class="col-md-8">{{ $donation->barang->penitip->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email</div>
                        <div class="col-md-8">{{ $donation->barang->penitip->user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">No. Telepon</div>
                        <div class="col-md-8">{{ $donation->barang->penitip->no_telepon ?? 'Tidak tersedia' }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.organization.donations') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    
                    @if($donation->status == 'Menunggu Pengambilan')
                    <form action="{{ route('dashboard.organization.donations.update-status', $donation->donasi_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="Diproses">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Konfirmasi Pengambilan
                        </button>
                    </form>
                    @endif
                    
                    @if($donation->status == 'Diproses')
                    <form action="{{ route('dashboard.organization.donations.update-status', $donation->donasi_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="Selesai">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-double"></i> Selesaikan Donasi
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Riwayat Status</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Donasi Dibuat</h6>
                                <p class="timeline-date">{{ $donation->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </li>
                        
                        @if($donation->status != 'Menunggu Pengambilan')
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Pengambilan Dikonfirmasi</h6>
                                <p class="timeline-date">{{ $donation->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </li>
                        @endif
                        
                        @if($donation->status == 'Selesai')
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Donasi Selesai</h6>
                                <p class="timeline-date">{{ $donation->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </li>
                        @endif
                        
                        @if($donation->status == 'Dibatalkan')
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Donasi Dibatalkan</h6>
                                <p class="timeline-date">{{ $donation->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            @if($donation->request_donasi_id)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Terkait Dengan Request</h5>
                </div>
                <div class="card-body">
                    <p><strong>Judul Request:</strong> {{ $donation->requestDonasi->judul }}</p>
                    <p><strong>Status Request:</strong> {{ $donation->requestDonasi->status }}</p>
                    <a href="{{ route('dashboard.organization.requests.show', $donation->request_donasi_id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-external-link-alt"></i> Lihat Request
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
