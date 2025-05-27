@extends('layouts.dashboard')

@section('title', 'Daftarkan Penitip Baru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftarkan Penitip Baru</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.admin.penitips') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('dashboard.admin.penitips.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Data Akun -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Akun</h5>
                                
                                <div class="form-group">
                                    <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number') }}" 
                                           placeholder="Contoh: 08123456789"
                                           required>
                                    <small class="form-text text-muted">Format: 08xxxxxxxxx atau +62xxxxxxxxx</small>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="dob">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('dob') is-invalid @enderror" 
                                           id="dob" 
                                           name="dob" 
                                           value="{{ old('dob') }}" 
                                           max="{{ date('Y-m-d', strtotime('-17 years')) }}"
                                           required>
                                    <small class="form-text text-muted">Minimal umur 17 tahun</small>
                                    @error('dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                

                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                </div>
                            </div>

                            <!-- Data Penitip -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Penitip</h5>
                                
                                <div class="form-group">
                                    <label for="nama_penitip">Nama Penitip <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_penitip') is-invalid @enderror" 
                                           id="nama_penitip" 
                                           name="nama_penitip" 
                                           value="{{ old('nama_penitip') }}" 
                                           required>
                                    @error('nama_penitip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="no_ktp">Nomor KTP <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('no_ktp') is-invalid @enderror" 
                                           id="no_ktp" 
                                           name="no_ktp" 
                                           value="{{ old('no_ktp') }}" 
                                           maxlength="16"
                                           pattern="[0-9]{16}"
                                           required>
                                    <small class="form-text text-muted">16 digit angka</small>
                                    @error('no_ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_registrasi">Tanggal Registrasi <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_registrasi') is-invalid @enderror" 
                                           id="tanggal_registrasi" 
                                           name="tanggal_registrasi" 
                                           value="{{ old('tanggal_registrasi', date('Y-m-d')) }}" 
                                           required>
                                    @error('tanggal_registrasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
    <label for="foto_ktp">Upload Foto KTP <span class="text-danger">*</span></label>
    <input type="file" 
           class="form-control @error('foto_ktp') is-invalid @enderror" 
           id="foto_ktp" 
           name="foto_ktp" 
           accept="image/*"
           required>
    <small class="form-text text-muted">Format gambar JPG, PNG maksimal 2MB.</small>
    @error('foto_ktp')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                                <div class="form-group">
                                    <label for="badge">Badge</label>
                                    <select class="form-control @error('badge') is-invalid @enderror" 
                                            id="badge" 
                                            name="badge">
                                        <option value="no" {{ old('badge') == 'no' ? 'selected' : '' }}>No Badge</option>
                                        <option value="bronze" {{ old('badge') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                        <option value="silver" {{ old('badge') == 'silver' ? 'selected' : '' }}>Silver</option>
                                        <option value="gold" {{ old('badge') == 'gold' ? 'selected' : '' }}>Gold</option>
                                        <option value="platinum" {{ old('badge') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                                    </select>
                                    @error('badge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
    <label for="foto_ktp">Upload Foto KTP <span class="text-danger">*</span></label>
    <input type="file" 
           class="form-control @error('foto_ktp') is-invalid @enderror" 
           id="foto_ktp" 
           name="foto_ktp" 
           accept="image/*"
           required>
    <small class="form-text text-muted">Format gambar JPG, PNG maksimal 2MB.</small>
    @error('foto_ktp')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                                <div class="form-group">
                                    <label for="periode">Periode</label>
                                    <input type="text" 
                                           class="form-control @error('periode') is-invalid @enderror" 
                                           id="periode" 
                                           name="periode" 
                                           value="{{ old('periode') }}" 
                                           placeholder="Contoh: 2024-Q1">
                                    @error('periode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Daftarkan Penitip
                        </button>
                        <a href="{{ route('dashboard.admin.penitips') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill nama penitip dari nama lengkap
    const nameInput = document.getElementById('name');
    const namaPenitipInput = document.getElementById('nama_penitip');
    
    nameInput.addEventListener('input', function() {
        if (!namaPenitipInput.value) {
            namaPenitipInput.value = this.value;
        }
    });
    
    // Validasi KTP hanya angka
    const ktpInput = document.getElementById('no_ktp');
    ktpInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validasi nomor telepon
    const phoneInput = document.getElementById('phone_number');
    phoneInput.addEventListener('input', function() {
        // Remove any non-numeric characters except +, -, space, and parentheses
        this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
    });
});
</script>
@endsection
