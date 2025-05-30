@extends('layouts.dashboard')

@section('title', 'Detail Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.warehouse.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.warehouse.item-pickup') }}">Pengambilan Barang</a></li>
                    <li class="breadcrumb-item active">Detail Barang</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Detail Barang</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($item->foto_barang)
                                <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                                     alt="{{ $item->nama_barang }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $item->nama_barang }}</h4>
                            <p class="text-muted">ID: {{ $item->barang_id }}</p>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Penitip:</strong></td>
                                    <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori:</strong></td>
                                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga:</strong></td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kondisi:</strong></td>
                                    <td>
                                        @if($item->kondisi == 'baru')
                                            <span class="badge bg-primary">Baru</span>
                                        @elseif($item->kondisi == 'sangat_layak')
                                            <span class="badge bg-success">Sangat Layak</span>
                                        @else
                                            <span class="badge bg-warning">Layak</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($item->status == 'diambil_kembali')
                                            <span class="badge bg-secondary">Diambil Kembali</span>
                                        @else
                                            <span class="badge bg-warning">Menunggu Pengambilan</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            
                            <div class="mt-3">
                                <h6>Deskripsi:</h6>
                                <p>{{ $item->deskripsi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Pickup Status Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Penitipan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tanggal Penitipan:</strong></td>
                            <td>{{ $item->tanggal_penitipan ? \Carbon\Carbon::parse($item->tanggal_penitipan)->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Batas Penitipan:</strong></td>
                            <td>{{ $item->batas_penitipan->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Hari Lewat:</strong></td>
                            <td>
                                @php
                                    $daysExpired = abs($item->sisa_hari);
                                @endphp
                                <span class="badge bg-danger">{{ $daysExpired }} hari</span>
                            </td>
                        </tr>
                    </table>
                    
                    @if($item->status != 'diambil_kembali')
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-success" onclick="confirmPickup({{ $item->barang_id }})">
                                <i class="fas fa-check me-1"></i>Konfirmasi Pengambilan
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Pickup History Card -->
            @if($item->status == 'diambil_kembali')
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Riwayat Pengambilan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tanggal Pengambilan:</strong></td>
                            <td>{{ $item->tanggal_pengambilan ? \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Metode:</strong></td>
                            <td>
                                @if($item->metode_pengambilan == 'penitip_pickup')
                                    Penitip Ambil Sendiri
                                @elseif($item->metode_pengambilan == 'courier_delivery')
                                    Kurir Antar
                                @elseif($item->metode_pengambilan == 'warehouse_storage')
                                    Simpan di Gudang
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($item->catatan_pengambilan)
                        <tr>
                            <td><strong>Catatan:</strong></td>
                            <td>{{ $item->catatan_pengambilan }}</td>
                        </tr>
                        @endif
                        @if($item->pickup_receipt_number)
                        <tr>
                            <td><strong>No. Tanda Terima:</strong></td>
                            <td>{{ $item->pickup_receipt_number }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
            
            <!-- Actions Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard.warehouse.item-pickup') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                        </a>
                        <a href="{{ route('products.show', $item->barang_id) }}" class="btn btn-outline-info" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>Lihat di Katalog
                        </a>
                        @if($item->status == 'diambil_kembali' && $item->pickup_receipt_number)
                        <button type="button" class="btn btn-outline-success" onclick="printReceipt()">
                            <i class="fas fa-print me-1"></i>Cetak Tanda Terima
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pickup Confirmation Modal -->
<div class="modal fade" id="pickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengambilan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pickupForm" method="POST" action="{{ route('dashboard.warehouse.confirm-item-pickup', $item->barang_id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Metode Pengambilan</label>
                        <select name="pickup_method" class="form-select" required>
                            <option value="">Pilih metode pengambilan...</option>
                            <option value="penitip_pickup">Penitip Ambil Sendiri</option>
                            <option value="courier_delivery">Kurir Antar</option>
                            <option value="warehouse_storage">Simpan di Gudang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="pickup_notes" class="form-control" rows="3" 
                                  placeholder="Catatan pengambilan (opsional)"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Setelah dikonfirmasi, status barang akan berubah menjadi "Diambil Kembali" dan tidak dapat dibatalkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Konfirmasi Pengambilan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmPickup(itemId) {
        const modal = new bootstrap.Modal(document.getElementById('pickupModal'));
        modal.show();
    }

    function printReceipt() {
        // Implementation for printing pickup receipt
        window.print();
    }
</script>
@endpush
