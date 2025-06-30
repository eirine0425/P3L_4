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

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('buyer.alamat.update', $alamat->alamat_id) }}" method="POST" id="editAlamatForm">
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
                                                       placeholder="Masukkan nama penerima" 
                                                       minlength="2" maxlength="100" required>
                                                @error('nama_penerima')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Minimal 2 karakter, maksimal 100 karakter</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="no_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control @error('no_telepon') is-invalid @enderror" 
                                                       id="no_telepon" name="no_telepon" 
                                                       value="{{ old('no_telepon', $alamat->no_telepon) }}" 
                                                       placeholder="Contoh: 08123456789" 
                                                       pattern="^0[0-9]{9,13}$" 
                                                       minlength="10" maxlength="14" required>
                                                @error('no_telepon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Format: 08xxxxxxxxx (10-14 digit)</div>
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
                                                      placeholder="Masukkan alamat lengkap (nama jalan, nomor rumah, RT/RW, kelurahan)" 
                                                      minlength="10" maxlength="500" required>{{ old('alamat', $alamat->alamat) }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <span id="alamatCounter">{{ strlen($alamat->alamat) }}</span>/500 karakter
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="kota" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('kota') is-invalid @enderror" 
                                                       id="kota" name="kota" 
                                                       value="{{ old('kota', $alamat->kota) }}" 
                                                       placeholder="Contoh: Jakarta Selatan" 
                                                       minlength="2" maxlength="100" required>
                                                @error('kota')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('provinsi') is-invalid @enderror" 
                                                       id="provinsi" name="provinsi" 
                                                       value="{{ old('provinsi', $alamat->provinsi) }}" 
                                                       placeholder="Contoh: DKI Jakarta" 
                                                       minlength="2" maxlength="100" required>
                                                @error('provinsi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" 
                                                       id="kode_pos" name="kode_pos" 
                                                       value="{{ old('kode_pos', $alamat->kode_pos) }}" 
                                                       placeholder="Contoh: 12345" 
                                                       pattern="[0-9]{5}" 
                                                       minlength="5" maxlength="5" required>
                                                @error('kode_pos')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">5 digit angka</div>
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
                                                <input class="form-check-input" type="checkbox" id="set_default" name="set_default" value="1" 
                                                       {{ old('set_default') ? 'checked' : '' }}>
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
                                @else
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-info-circle text-info me-2"></i>
                                                Informasi
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                Alamat ini adalah alamat utama Anda. Untuk mengubah alamat utama, 
                                                silakan pilih alamat lain dan jadikan sebagai alamat utama.
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('buyer.alamat.index') }}" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <i class="fas fa-save me-1"></i> Perbarui Alamat
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Alamat -->
                            <div class="col-lg-4">
                                <div class="card bg-light sticky-top" style="top: 20px;">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-2"></i>
                                            Preview Alamat
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="previewContent">
                                            @if($alamat->status_default == 'Y')
                                                <span class="badge bg-primary mb-2">
                                                    <i class="fas fa-star me-1"></i>Alamat Utama
                                                </span>
                                            @endif
                                            
                                            <h6 class="fw-bold" id="previewNama">{{ $alamat->nama_penerima }}</h6>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-phone text-success me-1"></i>
                                                <span id="previewTelepon">{{ $alamat->no_telepon }}</span>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <span id="previewAlamat">{{ $alamat->alamat }}</span>
                                            </p>
                                            <p class="text-muted mb-0">
                                                <span id="previewKota">{{ $alamat->kota }}</span>, 
                                                <span id="previewProvinsi">{{ $alamat->provinsi }}</span> 
                                                <span id="previewKodePos">{{ $alamat->kode_pos }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tips -->
                                <div class="card mt-3">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            Tips
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Pastikan nomor telepon aktif dan dapat dihubungi
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Tulis alamat dengan lengkap dan jelas
                                            </li>
                                            <li class="mb-0">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Kode pos harus sesuai dengan wilayah Anda
                                            </li>
                                        </ul>
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
document.addEventListener('DOMContentLoaded', function() {
    // Format nomor telepon
    const noTeleponInput = document.getElementById('no_telepon');
    noTeleponInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Pastikan dimulai dengan 0
        if (value.length > 0 && !value.startsWith('0')) {
            value = '0' + value;
        }
        
        // Batasi maksimal 14 digit
        if (value.length > 14) {
            value = value.substring(0, 14);
        }
        
        e.target.value = value;
        updatePreview();
    });

    // Format kode pos
    const kodePosInput = document.getElementById('kode_pos');
    kodePosInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Batasi maksimal 5 digit
        if (value.length > 5) {
            value = value.substring(0, 5);
        }
        
        e.target.value = value;
        updatePreview();
    });

    // Character counter untuk alamat
    const alamatTextarea = document.getElementById('alamat');
    const alamatCounter = document.getElementById('alamatCounter');
    
    alamatTextarea.addEventListener('input', function(e) {
        const length = e.target.value.length;
        alamatCounter.textContent = length;
        
        if (length > 500) {
            alamatCounter.style.color = 'red';
        } else if (length > 450) {
            alamatCounter.style.color = 'orange';
        } else {
            alamatCounter.style.color = 'inherit';
        }
        
        updatePreview();
    });

    // Real-time preview update
    function updatePreview() {
        const nama = document.getElementById('nama_penerima').value || 'Nama Penerima';
        const telepon = document.getElementById('no_telepon').value || 'Nomor Telepon';
        const alamat = document.getElementById('alamat').value || 'Alamat';
        const kota = document.getElementById('kota').value || 'Kota';
        const provinsi = document.getElementById('provinsi').value || 'Provinsi';
        const kodePos = document.getElementById('kode_pos').value || 'Kode Pos';

        document.getElementById('previewNama').textContent = nama;
        document.getElementById('previewTelepon').textContent = telepon;
        document.getElementById('previewAlamat').textContent = alamat;
        document.getElementById('previewKota').textContent = kota;
        document.getElementById('previewProvinsi').textContent = provinsi;
        document.getElementById('previewKodePos').textContent = kodePos;
    }

    // Add event listeners for all inputs
    ['nama_penerima', 'kota', 'provinsi'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    // Form validation
    const form = document.getElementById('editAlamatForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        submitBtn.disabled = true;
    });

    // Capitalize first letter for text inputs
    ['nama_penerima', 'kota', 'provinsi'].forEach(function(id) {
        const input = document.getElementById(id);
        input.addEventListener('blur', function(e) {
            const words = e.target.value.split(' ');
            const capitalizedWords = words.map(word => 
                word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
            );
            e.target.value = capitalizedWords.join(' ');
            updatePreview();
        });
    });
});
</script>

<style>
.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 1020;
}

.form-control:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

#alamatCounter {
    font-weight: 500;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #0dcaf0;
}

.alert-success {
    border-left-color: #198754;
}

.alert-danger {
    border-left-color: #dc3545;
}
</style>
@endsection
