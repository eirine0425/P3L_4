@extends('layouts.dashboard')

@section('title', 'Edit Alamat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">
                                <i class="fas fa-edit text-warning me-2"></i>
                                Edit Alamat
                            </h4>
                            <p class="text-muted mb-0">Perbarui informasi alamat pengiriman</p>
                        </div>
                        <a href="{{ route('buyer.alamat.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('buyer.alamat.update', $alamat->alamat_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Status Badge -->
                                @if($alamat->status_default == 'Y')
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        <strong>Alamat Utama</strong> - Alamat ini saat ini digunakan sebagai alamat utama Anda
                                    </div>
                                @endif

                                <!-- Informasi Penerima -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            Informasi Penerima
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nama_penerima" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nama_penerima') is-invalid @enderror" 
                                                       id="nama_penerima" name="nama_penerima" 
                                                       value="{{ old('nama_penerima', $alamat->nama_penerima) }}" 
                                                       placeholder="Masukkan nama penerima" required>
                                                @error('nama_penerima')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="no_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control @error('no_telepon') is-invalid @enderror" 
                                                       id="no_telepon" name="no_telepon" 
                                                       value="{{ old('no_telepon', $alamat->no_telepon) }}" 
                                                       placeholder="Contoh: 08123456789" required>
                                                @error('no_telepon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat Lengkap -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            Alamat Lengkap
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                                      id="alamat" name="alamat" rows="3" 
                                                      placeholder="Masukkan alamat lengkap" required>{{ old('alamat', $alamat->alamat) }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="kota" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('kota') is-invalid @enderror" 
                                                       id="kota" name="kota" 
                                                       value="{{ old('kota', $alamat->kota) }}" 
                                                       placeholder="Contoh: Jakarta Selatan" required>
                                                @error('kota')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('provinsi') is-invalid @enderror" 
                                                       id="provinsi" name="provinsi" 
                                                       value="{{ old('provinsi', $alamat->provinsi) }}" 
                                                       placeholder="Contoh: DKI Jakarta" required>
                                                @error('provinsi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" 
                                                       id="kode_pos" name="kode_pos" 
                                                       value="{{ old('kode_pos', $alamat->kode_pos) }}" 
                                                       placeholder="Contoh: 12345" maxlength="5" required>
                                                @error('kode_pos')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pengaturan -->
                                @if($alamat->status_default != 'Y')
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cog text-secondary me-2"></i>
                                                Pengaturan
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="set_default" name="set_default" value="1">
                                                <label class="form-check-label" for="set_default">
                                                    <i class="fas fa-star text-warning me-1"></i>
                                                    Jadikan sebagai alamat utama
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    Alamat utama akan digunakan sebagai alamat default untuk pengiriman
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('buyer.alamat.index') }}" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Perbarui Alamat
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Alamat Saat Ini -->
                            <div class="col-lg-4">
                                <div class="card bg-light">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-2"></i>
                                            Alamat Saat Ini
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($alamat->status_default == 'Y')
                                            <span class="badge bg-primary mb-2">
                                                <i class="fas fa-star me-1"></i>Alamat Utama
                                            </span>
                                        @endif
                                        
                                        <h6 class="fw-bold">{{ $alamat->nama_penerima }}</h6>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-phone text-success me-1"></i>
                                            {{ $alamat->no_telepon }}
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ $alamat->alamat }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            {{ $alamat->kota }}, {{ $alamat->provinsi }} {{ $alamat->kode_pos }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Format nomor telepon
document.getElementById('no_telepon').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0 && !value.startsWith('0')) {
        value = '0' + value;
    }
    e.target.value = value;
});

// Format kode pos
document.getElementById('kode_pos').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
</script>
@endsection
