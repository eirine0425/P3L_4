@extends('layouts.dashboard')

@section('title', 'Kelola Alamat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                Kelola Alamat
                            </h4>
                            <p class="text-muted mb-0">Atur alamat pengiriman Anda</p>
                        </div>
                        <a href="{{ route('buyer.alamat.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Alamat
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($alamats->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-map-marker-alt fa-4x text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-muted mb-3">Belum ada alamat tersimpan</h5>
                            <p class="text-muted mb-4">Tambahkan alamat pertama Anda untuk memudahkan proses pengiriman</p>
                            <a href="{{ route('buyer.alamat.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i> Tambah Alamat Pertama
                            </a>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($alamats as $alamat)
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card h-100 alamat-card {{ $alamat->status_default == 'Y' ? 'border-primary shadow-sm' : 'border' }}">
                                        <div class="card-body">
                                            <!-- Header dengan badge -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    @if($alamat->status_default == 'Y')
                                                        <span class="badge bg-primary mb-2">
                                                            <i class="fas fa-star me-1"></i>Alamat Utama
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('buyer.alamat.edit', ['id' => $alamat->alamat_id]) }}">
                                                                <i class="fas fa-edit text-info me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        @if($alamats->count() > 1)
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('buyer.alamat.destroy', ['id' => $alamat->alamat_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash me-2"></i>Hapus
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Informasi Penerima -->
                                            <div class="mb-3">
                                                <h6 class="fw-bold mb-1">{{ $alamat->nama_penerima }}</h6>
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-phone text-success me-1"></i>
                                                    {{ $alamat->no_telepon }}
                                                </p>
                                            </div>

                                            <!-- Alamat -->
                                            <div class="mb-3">
                                                <p class="mb-1">
                                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                    <small class="text-muted">Alamat:</small>
                                                </p>
                                                <p class="mb-1">{{ $alamat->alamat }}</p>
                                                <p class="text-muted mb-0">
                                                    {{ $alamat->kota }}, {{ $alamat->provinsi }} {{ $alamat->kode_pos }}
                                                </p>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                               
                                                
                                                <a href="{{ route('buyer.alamat.edit', ['id' => $alamat->alamat_id]) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.alamat-card {
    transition: all 0.3s ease;
}

.alamat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.alamat-card.border-primary {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
}
</style>
@endsection
