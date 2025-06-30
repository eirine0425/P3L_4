@extends('layouts.dashboard')

@section('title', 'Detail Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Pengambilan Barang</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.warehouse.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.warehouse.item-pickup') }}">Pengambilan Barang</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('dashboard.warehouse.item-pickup') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if($item->status !== 'diambil_kembali')
                <button type="button" class="btn btn-success" onclick="confirmPickup({{ $item->barang_id }}, '{{ $item->nama_barang }}')">
                    <i class="fas fa-check"></i> Konfirmasi Pengambilan
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Item Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Barang</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ $item->photo_url }}" alt="{{ $item->nama_barang }}" 
                                 class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>ID Barang</strong></td>
                                    <td>: {{ $item->barang_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Barang</strong></td>
                                    <td>: {{ $item->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori</strong></td>
                                    <td>: {{ $item->kategori->nama_kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kondisi</strong></td>
                                    <td>: 
                                        <span class="badge {{ $item->condition_badge_class }}">
                                            {{ ucfirst($item->kondisi) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Harga</strong></td>
                                    <td>: {{ $item->formatted_price }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        <span class="badge {{ $item->status_badge_class }}">
                                            {{ $item->status_display_text }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi</strong></td>
                                    <td>: {{ $item->deskripsi }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consignment Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penitipan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Tanggal Penitipan</strong></td>
                                    <td>: {{ $item->tanggal_mulai_penitipan->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Batas Penitipan</strong></td>
                                    <td>: {{ $item->batas_penitipan->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Durasi Penitipan</strong></td>
                                    <td>: {{ $item->tanggal_mulai_penitipan->diffInDays($item->batas_penitipan) }} hari</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Status Durasi</strong></td>
                                    <td>: 
                                        <span class="badge {{ $item->status_durasi_badge_class }}">
                                            {{ $item->status_durasi_text }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Sisa Waktu</strong></td>
                                    <td>: 
                                        @if($item->sisa_hari < 0)
                                            <span class="text-danger fw-bold">
                                                Kadaluarsa {{ abs($item->sisa_hari) }} hari
                                            </span>
                                        @else
                                            <span class="text-success">
                                                {{ $item->sisa_hari }} hari lagi
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($item->transaksiPenitipan && $item->transaksiPenitipan->perpanjangan_count > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Riwayat Perpanjangan:</strong> 
                            Barang ini telah diperpanjang {{ $item->transaksiPenitipan->perpanjangan_count }} kali.
                            @if($item->transaksiPenitipan->tanggal_perpanjangan_terakhir)
                                Perpanjangan terakhir: {{ $item->transaksiPenitipan->tanggal_perpanjangan_terakhir->format('d/m/Y') }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($item->status === 'diambil_kembali')
                <!-- Pickup Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Informasi Pengambilan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Tanggal Pengambilan</strong></td>
                                <td>: {{ $item->tanggal_pengambilan ? $item->tanggal_pengambilan->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Metode Pengambilan</strong></td>
                                <td>: 
                                    @switch($item->metode_pengambilan)
                                        @case('penitip_pickup')
                                            <span class="badge bg-primary">Diambil Langsung oleh Penitip</span>
                                            @break
                                        @case('courier_delivery')
                                            <span class="badge bg-info">Dikirim via Kurir</span>
                                            @break
                                        @case('warehouse_storage')
                                            <span class="badge bg-warning">Disimpan di Gudang</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">-</span>
                                    @endswitch
                                </td>
                            </tr>
                            @if($item->catatan_pengambilan)
                                <tr>
                                    <td><strong>Catatan</strong></td>
                                    <td>: {{ $item->catatan_pengambilan }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Consignor Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penitip</h6>
                </div>
                <div class="card-body">
                    @if($item->penitip && $item->penitip->user)
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-primary text-white mx-auto mb-2" style="width: 60px; height: 60px; line-height: 60px; border-radius: 50%;">
                                {{ strtoupper(substr($item->penitip->user->name, 0, 2)) }}
                            </div>
                            <h6 class="mb-0">{{ $item->penitip->user->name }}</h6>
                            <small class="text-muted">{{ $item->penitip->user->email }}</small>
                        </div>
                        
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>ID Penitip</strong></td>
                                <td>: {{ $item->penitip->penitip_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. Telepon</strong></td>
                                <td>: {{ $item->penitip->user->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bergabung</strong></td>
                                <td>: {{ $item->penitip->created_at->format('d/m/Y') }}</td>
                            </tr>
                        </table>

                        <div class="mt-3">
                            <a href="{{ route('dashboard.admin.penitips.show', $item->penitip->penitip_id) }}" 
                               class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-user"></i> Lihat Profil Penitip
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Informasi penitip tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Barang Dititipkan</h6>
                                <p class="timeline-text">{{ $item->tanggal_mulai_penitipan->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($item->transaksiPenitipan && $item->transaksiPenitipan->perpanjangan_count > 0)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Perpanjangan</h6>
                                    <p class="timeline-text">
                                        {{ $item->transaksiPenitipan->perpanjangan_count }} kali perpanjangan
                                        @if($item->transaksiPenitipan->tanggal_perpanjangan_terakhir)
                                            <br><small>Terakhir: {{ $item->transaksiPenitipan->tanggal_perpanjangan_terakhir->format('d/m/Y') }}</small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Batas Penitipan</h6>
                                <p class="timeline-text">{{ $item->batas_penitipan->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        
                        @if($item->status === 'diambil_kembali')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Barang Diambil</h6>
                                    <p class="timeline-text">{{ $item->tanggal_pengambilan->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Kadaluarsa</h6>
                                    <p class="timeline-text">{{ abs($item->sisa_hari) }} hari yang lalu</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pickup Modal -->
<div class="modal fade" id="pickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengambilan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pickupForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Barang yang akan diambil:</label>
                        <div id="itemName" class="fw-bold">{{ $item->nama_barang }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pickup_method" class="form-label">Metode Pengambilan</label>
                        <select class="form-select" id="pickup_method" name="pickup_method" required>
                            <option value="">Pilih metode pengambilan</option>
                            <option value="penitip_pickup">Diambil Langsung oleh Penitip</option>
                            <option value="courier_delivery">Dikirim via Kurir</option>
                            <option value="warehouse_storage">Disimpan di Gudang</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pickup_notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="pickup_notes" name="pickup_notes" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Konfirmasi Pengambilan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
function confirmPickup(itemId, itemName) {
    document.getElementById('pickupForm').action = `/dashboard/warehouse/pickup/${itemId}/confirm`;
    new bootstrap.Modal(document.getElementById('pickupModal')).show();
}
</script>
@endpush