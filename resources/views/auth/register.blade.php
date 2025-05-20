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
                        <button class="nav-link" id="penitip-tab" data-bs-toggle="tab" data-bs-target="#penitip" type="button" role="tab" aria-controls="penitip" aria-selected="false">Penitip</button>
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
                            <input type="hidden" name="role_id" value="3"> <!-- Assuming 3 is the role_id for Pembeli -->
                            
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
                    
                    <!-- Penitip Registration Form -->
                    <div class="tab-pane fade" id="penitip" role="tabpanel" aria-labelledby="penitip-tab">
                        <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="role_id" value="4"> <!-- Assuming 4 is the role_id for Penitip -->
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="penitip_name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="penitip_name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="penitip_email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="penitip_email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="penitip_password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="penitip_password" name="password" required autocomplete="new-password">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="penitip_password-confirm" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="penitip_password-confirm" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="penitip_dob" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror" id="penitip_dob" name="dob" value="{{ old('dob') }}" required>
                                    @error('dob')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="penitip_phone_number" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="penitip_phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="no_ktp" class="form-label">Nomor KTP</label>
                                    <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" id="no_ktp" name="no_ktp" value="{{ old('no_ktp') }}" required>
                                    @error('no_ktp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="ktp_image" class="form-label">Foto KTP</label>
                                    <input type="file" class="form-control @error('ktp_image') is-invalid @enderror" id="ktp_image" name="ktp_image" required>
                                    @error('ktp_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <small class="form-text text-muted">Upload foto KTP yang jelas dan tidak buram.</small>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="penitip_terms" name="terms" required>
                                <label class="form-check-label" for="penitip_terms">Saya menyetujui <a href="#">syarat dan ketentuan</a> yang berlaku</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Daftar sebagai Penitip
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Organisasi Registration Form -->
                    <div class="tab-pane fade" id="organisasi" role="tabpanel" aria-labelledby="organisasi-tab">
                        <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="role_id" value="5"> <!-- Assuming 5 is the role_id for Organisasi -->
                            
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Daftar sebagai Organisasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <p>Sudah punya akun? <a href="{{ url('/login') }}">Login sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
