@extends('layouts.dashboard')

@section('title', 'Profil Organisasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Profil Organisasi</h2>
            <p class="text-muted">Kelola informasi profil organisasi Anda.</p>
        </div>
    </div>
    
    @if(session('success'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Profil Organisasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.organization.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nama_organisasi" class="form-label">Nama Organisasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_organisasi') is-invalid @enderror" id="nama_organisasi" name="nama_organisasi" value="{{ old('nama_organisasi', $organization->nama_organisasi) }}" required>
                            @error('nama_organisasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $organization->no_telepon) }}" required>
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $organization->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Organisasi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $organization->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan tentang organisasi Anda, termasuk visi, misi, dan kegiatan yang dilakukan.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dokumen" class="form-label">Dokumen Legalitas (Opsional)</label>
                            @if($organization->dokumen_path)
                                <div class="mb-2">
                                    <p class="small text-muted">
                                        <i class="fas fa-file-pdf"></i> Dokumen saat ini: 
                                        <a href="{{ asset($organization->dokumen_path) }}" target="_blank">Lihat Dokumen</a>
                                    </p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('dokumen') is-invalid @enderror" id="dokumen" name="dokumen">
                            @error('dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload dokumen legalitas organisasi (format: PDF, DOC, DOCX, maksimal 2MB).</small>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Ubah Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center mb-4">
                        <div class="avatar-placeholder">
                            <span>{{ substr($organization->nama_organisasi, 0, 1) }}</span>
                        </div>
                        <h5 class="mt-3">{{ $organization->nama_organisasi }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Status Akun</h6>
                        <p>
                            <span class="badge bg-success">Aktif</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Tanggal Bergabung</h6>
                        <p>{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Role</h6>
                        <p>{{ $user->role->nama_role }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Statistik Aktivitas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Total Donasi Diterima:</div>
                        <div class="fw-bold">{{ $totalDonations }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Request Donasi Dibuat:</div>
                        <div class="fw-bold">{{ $pendingRequests + $approvedRequests }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Request Disetujui:</div>
                        <div class="fw-bold">{{ $approvedRequests }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
