@extends('layouts.dashboard')

@section('title', 'Tambah Alamat')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        <h4 class="card-title mb-0">Tambah Alamat Baru</h4>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('buyer.alamat.store') }}" method="POST" id="alamatForm">
                        @csrf
                        
                        <!-- Informasi Penerima -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Informasi Penerima
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama_penerima" class="form-label fw-bold">
                                        Nama Penerima <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('nama_penerima') is-invalid @enderror" 
                                           id="nama_penerima" 
                                           name="nama_penerima" 
                                           value="{{ old('nama_penerima') }}" 
                                           placeholder="Masukkan nama penerima"
                                           required>
                                    @error('nama_penerima')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="no_telepon" class="form-label fw-bold">
                                        No. Telepon <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('no_telepon') is-invalid @enderror" 
                                           id="no_telepon" 
                                           name="no_telepon" 
                                           value="{{ old('no_telepon') }}" 
                                           placeholder="Contoh: 08123456789"
                                           required>
                                    @error('no_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alamat Lengkap -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    Detail Alamat
                                </h5>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="alamat" class="form-label fw-bold">
                                        Alamat Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" 
                                              name="alamat" 
                                              rows="4" 
                                              placeholder="Masukkan alamat lengkap (nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan)"
                                              required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Wilayah -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="kota" class="form-label fw-bold">
                                        Kota/Kabupaten <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('kota') is-invalid @enderror" 
                                           id="kota" 
                                           name="kota" 
                                           value="{{ old('kota') }}" 
                                           placeholder="Contoh: Jakarta Selatan"
                                           required>
                                    @error('kota')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="provinsi" class="form-label fw-bold">
                                        Provinsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('provinsi') is-invalid @enderror" 
                                           id="provinsi" 
                                           name="provinsi" 
                                           value="{{ old('provinsi') }}" 
                                           placeholder="Contoh: DKI Jakarta"
                                           required>
                                    @error('provinsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="kode_pos" class="form-label fw-bold">
                                        Kode Pos <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('kode_pos') is-invalid @enderror" 
                                           id="kode_pos" 
                                           name="kode_pos" 
                                           value="{{ old('kode_pos') }}" 
                                           placeholder="Contoh: 12345"
                                           maxlength="5"
                                           required>
                                    @error('kode_pos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status Default -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-star text-primary me-2"></i>
                                        Pengaturan Alamat
                                    </h5>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               role="switch" 
                                               id="set_default" 
                                               name="set_default" 
                                               value="1"
                                               {{ old('set_default') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="set_default">
                                            <i class="fas fa-home text-warning me-2"></i>
                                            Jadikan sebagai alamat utama
                                        </label>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Alamat utama akan digunakan sebagai alamat pengiriman default
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('buyer.alamat.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Simpan Alamat
                                    </button>
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
document.getElementById('kode_pos').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

document.getElementById('no_telepon').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9+]/g, '');
});
</script>
@endsection
