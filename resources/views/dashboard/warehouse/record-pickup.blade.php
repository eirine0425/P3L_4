@extends('layouts.dashboard')

@section('title', 'Catat Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Catat Pengambilan Barang</h1>
        <a href="{{ route('dashboard.warehouse.item-pickup') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Barang</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($item->foto_barang)
                            <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="{{ $item->nama_barang }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-box fa-3x text-gray-400"></i>
                                <p class="mt-2">Tidak ada foto</p>
                            </div>
                        @endif
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">ID Barang</th>
                            <td>{{ $item->barang_id }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $item->nama_barang }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Penitip</th>
                            <td>{{ $item->penitip->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Penitipan</th>
                            <td>{{ $item->tanggal_penitipan ? date('d/m/Y', strtotime($item->tanggal_penitipan)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Batas Penitipan</th>
                            <td>{{ $item->batas_penitipan ? date('d/m/Y', strtotime($item->batas_penitipan)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status Durasi</th>
                            <td>
                                <span class="badge {{ $item->status_durasi_badge_class }}">
                                    {{ $item->status_durasi_text }} ({{ $item->formatted_sisa_waktu }})
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Harga</th>
                            <td>{{ $item->formatted_price }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Pencatatan Pengambilan</h6>
                </div>
                <div class="card-body">
                    @if($item->status === 'diambil_kembali')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Barang ini sudah tercatat diambil kembali pada {{ $item->formatted_tanggal_pengambilan }}.
                        </div>
                    @elseif($item->sisa_hari >= 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Barang ini belum melewati batas waktu penitipan. Masih tersisa {{ $item->sisa_hari }} hari lagi.
                        </div>
                    @endif
                    
                    <form action="{{ route('dashboard.warehouse.record-pickup', $item->barang_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="pegawai_id">Pegawai yang Menangani <span class="text-danger">*</span></label>
                            <select class="form-control @error('pegawai_id') is-invalid @enderror" id="pegawai_id" name="pegawai_id" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($pegawaiList as $pegawai)
                                    <option value="{{ $pegawai->pegawai_id }}">{{ $pegawai->user->name }}</option>
                                @endforeach
                            </select>
                            @error('pegawai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_pengambil">Nama Pengambil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_pengambil') is-invalid @enderror" id="nama_pengambil" name="nama_pengambil" value="{{ old('nama_pengambil', $item->penitip->user->name ?? '') }}" required>
                            @error('nama_pengambil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="relasi_pengambil">Relasi dengan Penitip</label>
                            <select class="form-control @error('relasi_pengambil') is-invalid @enderror" id="relasi_pengambil" name="relasi_pengambil">
                                <option value="pemilik">Pemilik Langsung</option>
                                <option value="keluarga">Keluarga</option>
                                <option value="teman">Teman</option>
                                <option value="kurir">Kurir</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('relasi_pengambil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="nomor_identitas_pengambil">Nomor Identitas Pengambil</label>
                            <input type="text" class="form-control @error('nomor_identitas_pengambil') is-invalid @enderror" id="nomor_identitas_pengambil" name="nomor_identitas_pengambil" value="{{ old('nomor_identitas_pengambil') }}">
                            @error('nomor_identitas_pengambil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">KTP/SIM/Kartu Identitas lainnya</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="metode_pengambilan">Metode Pengambilan <span class="text-danger">*</span></label>
                            <select class="form-control @error('metode_pengambilan') is-invalid @enderror" id="metode_pengambilan" name="metode_pengambilan" required>
                                <option value="diambil_langsung">Diambil Langsung</option>
                                <option value="dikirim_kurir">Dikirim via Kurir</option>
                                <option value="dititipkan_pihak_lain">Dititipkan ke Pihak Lain</option>
                            </select>
                            @error('metode_pengambilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="bukti_pengambilan">Bukti Pengambilan (Foto)</label>
                            <input type="file" class="form-control-file @error('bukti_pengambilan') is-invalid @enderror" id="bukti_pengambilan" name="bukti_pengambilan">
                            @error('bukti_pengambilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="catatan_pengambilan">Catatan Pengambilan</label>
                            <textarea class="form-control @error('catatan_pengambilan') is-invalid @enderror" id="catatan_pengambilan" name="catatan_pengambilan" rows="3">{{ old('catatan_pengambilan') }}</textarea>
                            @error('catatan_pengambilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="konfirmasi" name="konfirmasi" required>
                                <label class="custom-control-label" for="konfirmasi">Saya konfirmasi bahwa barang ini telah diambil oleh pemilik atau perwakilan yang sah</label>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Catat Pengambilan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Tampilkan field tambahan jika relasi bukan pemilik langsung
        $('#relasi_pengambil').change(function() {
            if ($(this).val() !== 'pemilik') {
                $('#nomor_identitas_pengambil').attr('required', true);
                $('#bukti_pengambilan').attr('required', true);
            } else {
                $('#nomor_identitas_pengambil').attr('required', false);
                $('#bukti_pengambilan').attr('required', false);
            }
        });
    });
</script>
@endsection
