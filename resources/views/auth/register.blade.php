@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Register</h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-4" id="registerTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pembeli-tab" data-bs-toggle="tab" data-bs-target="#pembeli" type="button" role="tab" aria-controls="pembeli" aria-selected="true">Pembeli</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pegawai-tab" data-bs-toggle="tab" data-bs-target="#pegawai" type="button" role="tab" aria-controls="pegawai" aria-selected="false">Pegawai</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="organisasi-tab" data-bs-toggle="tab" data-bs-target="#organisasi" type="button" role="tab" aria-controls="organisasi" aria-selected="false">Organisasi</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="registerTabContent">
                    <!-- Pembeli Registration Form -->
                    <div class="tab-pane fade show active" id="pembeli" role="tabpanel" aria-labelledby="pembeli-tab">
                        <form method="POST" action="{{ url('/register') }}">
                            @csrf
                            <input type="hidden" name="role_id" value="4"> <!-- role_id 4 untuk Pembeli -->
                            <input type="hidden" name="role" value="pembeli">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="password-confirm" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="dob" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob" value="{{ old('dob') }}" required>
                                    @error('dob')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">Saya menyetujui <a href="#">syarat dan ketentuan</a> yang berlaku</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Daftar sebagai Pembeli
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Pegawai Registration Form -->
                    <div class="tab-pane fade" id="pegawai" role="tabpanel" aria-labelledby="pegawai-tab">
                        <form method="POST" action="{{ url('/register') }}">
                            @csrf
                            <input type="hidden" name="role_id" value="5"> <!-- role_id 5 untuk Pegawai -->
                            <input type="hidden" name="role" value="pegawai">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="pegawai_name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="pegawai_name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="pegawai_email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="pegawai_email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="pegawai_password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="pegawai_password" name="password" required autocomplete="new-password">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="pegawai_password-confirm" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="pegawai_password-confirm" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="pegawai_dob" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror" id="pegawai_dob" name="dob" value="{{ old('dob') }}" required>
                                    @error('dob')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="pegawai_phone_number" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="pegawai_phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                    @error('phone_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="gaji" class="form-label">Gaji yang Diharapkan</label>
                                    <input type="number" class="form-control @error('gaji') is-invalid @enderror" id="gaji" name="gaji" value="{{ old('gaji') }}" min="0" step="1000">
                                    @error('gaji')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <small class="form-text text-muted">Opsional - akan ditentukan oleh manajemen</small>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="pegawai_terms" name="terms" required>
                                <label class="form-check-label" for="pegawai_terms">Saya menyetujui <a href="#">syarat dan ketentuan</a> yang berlaku</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus me-2"></i>Daftar sebagai Pegawai
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Organisasi Registration Form -->
                    <div class="tab-pane fade" id="organisasi" role="tabpanel" aria-labelledby="organisasi-tab">
                        <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="role_id" value="7"> <!-- role_id 7 untuk Organisasi -->
                            <input type="hidden" name="role" value="organisasi">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="organisasi_name" class="form-label">Nama Organisasi</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="organisasi_name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="organisasi_email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="organisasi_email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="organisasi_password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="organisasi_password" name="password" required autocomplete="new-password">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="organisasi_password-confirm" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="organisasi_password-confirm" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="organisasi_phone_number" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="organisasi_phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                    @error('phone_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="organisasi_address" class="form-label">Alamat Organisasi</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="organisasi_address" name="address" value="{{ old('address') }}" required>
                                    @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="organisasi_description" class="form-label">Deskripsi Organisasi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="organisasi_description" name="description" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="organisasi_document" class="form-label">Dokumen Legalitas</label>
                                <input type="file" class="form-control @error('document') is-invalid @enderror" id="organisasi_document" name="document" required>
                                @error('document')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                                <small class="form-text text-muted">Upload dokumen legalitas organisasi (PDF, maksimal 2MB).</small>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="organisasi_terms" name="terms" required>
                                <label class="form-check-label" for="organisasi_terms">Saya menyetujui <a href="#">syarat dan ketentuan</a> yang berlaku</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-user-plus me-2"></i>Daftar sebagai Organisasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <p>Sudah punya akun? <a href="{{ url('/login') }}">Login sekarang</a></p>
                    <p class="text-muted small">
                        <strong>Catatan:</strong> Penitip/Penjual harus didaftarkan oleh admin. 
                        Silakan hubungi admin untuk mendaftar sebagai penitip.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script untuk memastikan role_id terkirim dengan benar
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('#registerTab button[data-bs-toggle="tab"]');
    
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(event) {
            var activeTab = event.target.getAttribute('data-bs-target');
            
            if (activeTab === '#pembeli') {
                document.querySelector('#pembeli input[name="role_id"]').value = '4';
                document.querySelector('#pembeli input[name="role"]').value = 'pembeli';
            } else if (activeTab === '#pegawai') {
                document.querySelector('#pegawai input[name="role_id"]').value = '5';
                document.querySelector('#pegawai input[name="role"]').value = 'pegawai';
            } else if (activeTab === '#organisasi') {
                document.querySelector('#organisasi input[name="role_id"]').value = '7';
                document.querySelector('#organisasi input[name="role"]').value = 'organisasi';
            }
        });
    });
});
</script>
@endsection
