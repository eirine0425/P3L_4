@extends('layouts.dashboard')

@section('title', 'Buat Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Buat Request Donasi Baru</h2>
            <p class="text-muted">Buat permintaan donasi untuk kebutuhan organisasi Anda.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Request Donasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.organization.requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Request <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan secara detail tentang kebutuhan donasi, termasuk spesifikasi barang yang dibutuhkan.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_kebutuhan" class="form-label">Jumlah Kebutuhan <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jumlah_kebutuhan') is-invalid @enderror" id="jumlah_kebutuhan" name="jumlah_kebutuhan" value="{{ old('jumlah_kebutuhan') }}" min="1" required>
                                    @error('jumlah_kebutuhan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_kebutuhan" class="form-label">Tanggal Kebutuhan <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_kebutuhan') is-invalid @enderror" id="tanggal_kebutuhan" name="tanggal_kebutuhan" value="{{ old('tanggal_kebutuhan') }}" required>
                                    @error('tanggal_kebutuhan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Tanggal kapan donasi dibutuhkan.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar (Opsional)</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload gambar untuk memperjelas kebutuhan donasi (format: JPG, PNG, maksimal 2MB).</small>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard.organization.requests') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Petunjuk Pengajuan Request</h6>
                        <hr>
                        <p>Berikut adalah beberapa tips untuk membuat request donasi yang efektif:</p>
                        <ul>
                            <li>Berikan judul yang jelas dan spesifik</li>
                            <li>Jelaskan secara detail kebutuhan dan alasan permintaan</li>
                            <li>Sertakan spesifikasi barang yang dibutuhkan</li>
                            <li>Upload gambar untuk memperjelas kebutuhan (jika ada)</li>
                            <li>Tentukan jumlah yang dibutuhkan dengan tepat</li>
                        </ul>
                        <p>Request donasi akan diverifikasi oleh admin sebelum dipublikasikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

