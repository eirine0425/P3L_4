@extends('layouts.dashboard')

@section('title', 'Detail Barang Berakhir')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Barang Berakhir Hari Ini</h2>
                    <p class="text-muted">ID Barang: #{{ $item->barang_id }}</p>
                </div>
                <div>
                    <a href="{{ route('consignor.transactions.expiring') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Item Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Barang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">ID Barang:</td>
                                    <td>#{{ $item->barang_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Penitipan:</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_penitipan)->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Batas Penitipan:</td>
                                    <td class="text-danger fw-bold">{{ \Carbon\Carbon::parse($item->batas_penitipan)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Harga:</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($item->status == 'belum_terjual')
                                            <span class="badge bg-success">Belum Terjual</span>
                                        @elseif($item->status == 'terjual')
                                            <span class="badge bg-info">Terjual</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kondisi:</td>
                                    <td>{{ ucfirst($item->kondisi) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sisa Waktu:</td>
                                    <td>
                                        @php
                                            $now = now();
                                            $batas = \Carbon\Carbon::parse($item->batas_penitipan);
                                            $diff = $now->diffInHours($batas, false);
                                        @endphp
                                        @if($diff > 0)
                                            <span class="text-warning fw-bold">{{ $diff }} jam lagi</span>
                                        @else
                                            <span class="text-danger fw-bold">Sudah berakhir</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kategori:</td>
                                    <td>{{ $item->kategori->nama_kategori ?? 'Tidak Diketahui' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>Detail Produk
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($item->foto_barang)
                                <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                                     alt="{{ $item->nama_barang }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $item->nama_barang }}</h4>
                            <p class="text-muted mb-3">{{ $item->deskripsi }}</p>
                            
                            <div class="row">
                                <div class="col-6">
                                    <strong>Kategori:</strong><br>
                                    <span class="badge bg-primary">{{ $item->kategori->nama_kategori ?? 'Tidak Diketahui' }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Harga:</strong><br>
                                    <span class="h5 text-success">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-6">
                                    <strong>Kondisi:</strong><br>
                                    {{ ucfirst($item->kondisi) }}
                                </div>
                                <div class="col-6">
                                    <strong>Status Barang:</strong><br>
                                    @if($item->status == 'belum_terjual')
                                        <span class="badge bg-success">Belum Terjual</span>
                                    @elseif($item->status == 'terjual')
                                        <span class="badge bg-info">Terjual</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Aksi
                    </h5>
                </div>
                <div class="card-body">
                    @if(\Carbon\Carbon::parse($item->batas_penitipan)->lte(now()->endOfDay()))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong><br>
                            Barang ini akan berakhir hari ini. Perpanjang sekarang untuk melanjutkan penitipan.
                        </div>
                        
                        <button type="button" class="btn btn-warning w-100 mb-3" 
                                onclick="confirmExtend({{ $item->barang_id }}, '{{ $item->nama_barang }}')">
                            <i class="fas fa-plus-circle me-2"></i>Perpanjang Masa Penitipan
                        </button>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Sudah Diperpanjang!</strong><br>
                            Masa penitipan telah diperpanjang hingga {{ \Carbon\Carbon::parse($item->batas_penitipan)->format('d F Y') }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Confirmation Modal -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-labelledby="extendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Perpanjang Masa Penitipan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Masa penitipan akan diperpanjang selama <strong>30 hari</strong> dari tanggal berakhir saat ini.
                </div>
                <p>Apakah Anda yakin ingin memperpanjang masa penitipan untuk barang <strong id="itemName"></strong>?</p>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Berakhir saat ini:</small><br>
                        <strong class="text-danger">{{ \Carbon\Carbon::parse($item->batas_penitipan)->format('d F Y') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Setelah diperpanjang:</small><br>
                        <strong class="text-success">{{ \Carbon\Carbon::parse($item->batas_penitipan)->addDays(30)->format('d F Y') }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="extendForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-plus-circle me-2"></i>Perpanjang Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmExtend(itemId, itemName) {
    document.getElementById('itemName').textContent = itemName;
    document.getElementById('extendForm').action = '{{ route("consignor.transactions.extend", ":id") }}'.replace(':id', itemId);
    
    var modal = new bootstrap.Modal(document.getElementById('extendModal'));
    modal.show();
}
</script>
@endpush

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
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table-borderless td {
    padding: 0.5rem 0;
    border: none;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
