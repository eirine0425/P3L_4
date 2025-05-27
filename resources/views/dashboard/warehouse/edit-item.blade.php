@extends('layouts.dashboard')

@section('title', 'Edit Barang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2>Edit Barang: {{ $item->nama_barang }}</h2>
                <p class="text-muted">Update informasi barang titipan</p>
            </div>
            <div>
                <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
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
                    <form action="{{ route('dashboard.warehouse.item.update', $item->barang_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                           id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang) }}" required>
                                    @error('nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                            id="kategori_id" name="kategori_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->kategori_id }}" 
                                                    {{ old('kategori_id', $item->kategori_id) == $category->kategori_id ? 'selected' : '' }}>
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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                               id="harga" name="harga" value="{{ old('harga', $item->harga) }}" min="0" required>
                                        @error('harga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('kondisi') is-invalid @enderror" 
                                            id="kondisi" name="kondisi" required>
                                        <option value="">Pilih Kondisi</option>
                                        <option value="baru" {{ old('kondisi', $item->kondisi) == 'baru' ? 'selected' : '' }}>Baru</option>
                                        <option value="sangat_layak" {{ old('kondisi', $item->kondisi) == 'sangat_layak' ? 'selected' : '' }}>Sangat Layak</option>
                                        <option value="layak" {{ old('kondisi', $item->kondisi) == 'layak' ? 'selected' : '' }}>Layak</option>
                                    </select>
                                    @error('kondisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="belum_terjual" {{ old('status', $item->status) == 'belum_terjual' ? 'selected' : '' }}>Belum Terjual</option>
                                <option value="terjual" {{ old('status', $item->status) == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                <option value="sold out" {{ old('status', $item->status) == 'sold out' ? 'selected' : '' }}>Sold Out</option>
                                <option value="untuk_donasi" {{ old('status', $item->status) == 'untuk_donasi' ? 'selected' : '' }}>Untuk Donasi</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi', $item->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto_barang" class="form-label">Foto Barang</label>
                            <input type="file" class="form-control @error('foto_barang') is-invalid @enderror" 
                                   id="foto_barang" name="foto_barang" accept="image/*">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG, JPEG. Maksimal 2MB.</div>
                            @error('foto_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
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
            <!-- Current Photo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Foto Saat Ini</h5>
                </div>
                <div class="card-body text-center">
                    @if($item->foto_barang && file_exists(storage_path('app/public/' . $item->foto_barang)))
                        <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                             alt="{{ $item->nama_barang }}" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 300px;">
                        <p class="text-muted small">Foto akan diganti jika Anda upload foto baru</p>
                    @else
                        <div class="no-image p-5 bg-light rounded mb-3">
                            <i class="fas fa-image fa-3x text-muted"></i>
                            <p class="mt-3 text-muted">Belum ada foto</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Item Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Penitip</h5>
                </div>
                <div class="card-body">
                    @if($item->penitip)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle me-3">
                                <i class="fas fa-user fa-lg text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $item->penitip->user->name ?? 'Nama tidak tersedia' }}</h6>
                                <small class="text-muted">{{ $item->penitip->user->email ?? 'Email tidak tersedia' }}</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="50%">No. KTP</th>
                                        <td>{{ $item->penitip->no_ktp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Point Donasi</th>
                                        <td>{{ $item->penitip->point_donasi ?? 0 }} poin</td>
                                    </tr>
                                    <tr>
                                        <th>Badge</th>
                                        <td>
                                            @if($item->penitip->badge)
                                                <span class="badge bg-warning">{{ $item->penitip->badge }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Informasi penitip tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e9ecef;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview image before upload
    const fotoInput = document.getElementById('foto_barang');
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add image preview functionality here if needed
                    console.log('Image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Format currency input
    const hargaInput = document.getElementById('harga');
    if (hargaInput) {
        hargaInput.addEventListener('input', function(e) {
            // Remove non-numeric characters except decimal point
            let value = e.target.value.replace(/[^\d]/g, '');
            e.target.value = value;
        });
    }
});
</script>
@endpush
