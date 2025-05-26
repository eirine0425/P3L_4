@extends('layouts.dashboard')

@section('title', 'Edit Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Edit Request Donasi</h2>
            <p class="text-muted">Perbarui informasi permintaan donasi Anda.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Edit Request Donasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.organization.requests.update', $request->request_donasi_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Request <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $request->judul) }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $request->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan secara detail tentang kebutuhan donasi, termasuk spesifikasi barang yang dibutuhkan.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_kebutuhan" class="form-label">Jumlah Kebutuhan <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jumlah_kebutuhan') is-invalid @enderror" id="jumlah_kebutuhan" name="jumlah_kebutuhan" value="{{ old('jumlah_kebutuhan', $request->jumlah_kebutuhan) }}" min="1" required>
                                    @error('jumlah_kebutuhan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_kebutuhan" class="form-label">Tanggal Kebutuhan <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_kebutuhan') is-invalid @enderror" id="tanggal_kebutuhan" name="tanggal_kebutuhan" value="{{ old('tanggal_kebutuhan', \Carbon\Carbon::parse($request->tanggal_kebutuhan)->format('Y-m-d')) }}" required>
                                    @error('tanggal_kebutuhan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Tanggal kapan donasi dibutuhkan.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar (Opsional)</label>
                            @if($request->gambar_path)
                                <div class="mb-2">
                                    <img src="{{ asset($request->gambar_path) }}" alt="{{ $request->judul }}" class="img-thumbnail" style="max-height: 150px;">
                                    <p class="small text-muted">Gambar saat ini. Upload gambar baru untuk mengganti.</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload gambar untuk memperjelas kebutuhan donasi (format: JPG, PNG, maksimal 2MB).</small>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard.organization.requests.show', $request->request_donasi_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
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
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Perhatian</h6>
                        <hr>
                        <p>Request donasi hanya dapat diedit selama masih berstatus "Menunggu Persetujuan".</p>
                        <p>Setelah disetujui atau ditolak, request tidak dapat diubah lagi.</p>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Status Request</h6>
                        <hr>
                        <p>Status saat ini: 
                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                        </p>
                        <p>Tanggal dibuat: {{ $request->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
