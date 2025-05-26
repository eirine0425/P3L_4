@extends('layouts.dashboard')

@section('title', 'Tambah Barang Titipan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tambah Barang Titipan</h5>
                    <p class="text-muted">Input data barang yang dititipkan oleh penitip</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.consignment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="penitip_id" class="form-label">Penitip <span class="text-danger">*</span></label>
                                    <select class="form-select @error('penitip_id') is-invalid @enderror" id="penitip_id" name="penitip_id" required>
                                        <option value="">Pilih Penitip</option>
                                        @foreach($penitips as $penitip)
                                            <option value="{{ $penitip->penitip_id }}" {{ old('penitip_id') == $penitip->penitip_id ? 'selected' : '' }}>
                                                {{ $penitip->user->name ?? 'Nama tidak tersedia' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('penitip_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->kategori_id }}" {{ old('kategori_id') == $category->kategori_id ? 'selected' : '' }}>
                                                {{ $category->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                   id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                               id="harga" name="harga" value="{{ old('harga') }}" min="0" step="1000" required>
                                    </div>
                                    @error('harga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('kondisi') is-invalid @enderror" id="kondisi" name="kondisi" required>
                                        <option value="">Pilih Kondisi</option>
                                        <option value="baru" {{ old('kondisi') == 'baru' ? 'selected' : '' }}>Baru</option>
                                        <option value="layak" {{ old('kondisi') == 'layak' ? 'selected' : '' }}>Layak</option>
                                        <option value="sangat_layak" {{ old('kondisi') == 'sangat_layak' ? 'selected' : '' }}>Sangat Layak</option>
                                    </select>
                                    @error('kondisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto_barang" class="form-label">Foto Barang</label>
                            <input type="file" class="form-control @error('foto_barang') is-invalid @enderror" 
                                   id="foto_barang" name="foto_barang" accept="image/*">
                            <div class="form-text">Format yang didukung: JPEG, PNG, JPG. Maksimal 2MB.</div>
                            @error('foto_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview image when selected
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
