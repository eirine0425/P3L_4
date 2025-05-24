@extends('layouts.dashboard')

@section('title', 'Tambah Barang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Tambah Barang Baru</h2>
                    <p class="text-muted">Isi form di bawah untuk menambahkan barang titipan</p>
                </div>
                <a href="{{ route('consignor.items') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Barang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('consignor.items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                       id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" 
                                       placeholder="Masukkan nama barang" required>
                                @error('nama_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                        id="kategori_id" name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->kategori_id }}" 
                                                {{ old('kategori_id') == $category->kategori_id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select class="form-select @error('kondisi') is-invalid @enderror" 
                                        id="kondisi" name="kondisi" required>
                                    <option value="">Pilih Kondisi</option>
                                    <option value="Baru" {{ old('kondisi') == 'Baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="Sangat Layak" {{ old('kondisi') == 'Sangat Layak' ? 'selected' : '' }}>Sangat Layak</option>
                                    <option value="Layak" {{ old('kondisi') == 'Layak' ? 'selected' : '' }}>Layak</option>
                                </select>
                                @error('kondisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                           id="harga" name="harga" value="{{ old('harga') }}" 
                                           placeholder="0" min="0" step="1000" required>
                                </div>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                          id="deskripsi" name="deskripsi" rows="4" 
                                          placeholder="Jelaskan detail barang, kondisi, dan informasi penting lainnya" required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="foto_barang" class="form-label">Foto Barang</label>
                                <input type="file" class="form-control @error('foto_barang') is-invalid @enderror" 
                                       id="foto_barang" name="foto_barang" accept="image/*">
                                <div class="form-text">Format: JPG, PNG. Maksimal 2MB.</div>
                                @error('foto_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Barang
                                </button>
                                <a href="{{ route('consignor.items') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Penting</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Proses Verifikasi</h6>
                        <p class="mb-0">Setelah Anda menambahkan barang, tim gudang akan melakukan verifikasi dalam 1-2 hari kerja. Anda akan mendapat notifikasi melalui email.</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Tips Foto</h6>
                        <ul class="mb-0">
                            <li>Gunakan pencahayaan yang baik</li>
                            <li>Foto dari berbagai sudut</li>
                            <li>Tunjukkan kondisi barang dengan jelas</li>
                            <li>Hindari foto yang buram</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle me-2"></i>Deskripsi yang Baik</h6>
                        <ul class="mb-0">
                            <li>Jelaskan kondisi detail</li>
                            <li>Sebutkan kelengkapan</li>
                            <li>Cantumkan spesifikasi</li>
                            <li>Jujur tentang kekurangan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('foto_barang').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview if doesn't exist
                let preview = document.getElementById('image-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'image-preview';
                    preview.className = 'img-thumbnail mt-2';
                    preview.style.maxWidth = '200px';
                    preview.style.maxHeight = '200px';
                    document.getElementById('foto_barang').parentNode.appendChild(preview);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
