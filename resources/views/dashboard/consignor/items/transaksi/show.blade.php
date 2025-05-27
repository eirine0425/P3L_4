@extends('layouts.dashboard')

@section('title', 'Detail Transaksi Penitipan')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Transaksi Penitipan</h2>
                    <p class="text-muted">{{ $transaction->kode_transaksi }}</p>
                </div>
                <a href="{{ route('consignor.transactions') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
    
    @php
        $batasDate = \Carbon\Carbon::parse($transaction->batas_penitipan);
        $today = \Carbon\Carbon::now();
        $sisaHari = $today->diffInDays($batasDate, false);
        $isExpired = $sisaHari < 0;
        $isNearExpiry = $sisaHari <= 7 && $sisaHari >= 0;
    @endphp
    
    <!-- Status Alert -->
    @if($isExpired)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Penitipan Expired!</strong> Masa penitipan telah berakhir {{ abs($sisaHari) }} hari yang lalu.
        </div>
    @elseif($isNearExpiry)
        <div class="alert alert-warning">
            <i class="fas fa-clock me-2"></i>
            <strong>Peringatan!</strong> Masa penitipan akan berakhir dalam {{ $sisaHari }} hari lagi.
        </div>
    @endif
    
    <div class="row">
        <!-- Transaction Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Kode Transaksi:</td>
                                    <td>{{ $transaction->kode_transaksi }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Penitipan:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->tanggal_penitipan)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Batas Penitipan:</td>
                                    <td class="{{ $isExpired ? 'text-danger' : ($isNearExpiry ? 'text-warning' : 'text-success') }} fw-bold">
                                        {{ $batasDate->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($transaction->status == 'aktif')
                                            @if($isExpired)
                                                <span class="badge bg-danger">Expired</span>
                                            @elseif($isNearExpiry)
                                                <span class="badge bg-warning">Akan Berakhir</span>
                                            @else
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Sisa Hari:</td>
                                    <td class="{{ $isExpired ? 'text-danger' : ($isNearExpiry ? 'text-warning' : 'text-success') }} fw-bold">
                                        @if($isExpired)
                                            Expired {{ abs($sisaHari) }} hari
                                        @else
                                            {{ $sisaHari }} hari lagi
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status Perpanjangan:</td>
                                    <td>
                                        @if($transaction->status_perpanjangan)
                                            <span class="badge bg-info">
                                                <i class="fas fa-check me-1"></i>Sudah Diperpanjang
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">Belum Diperpanjang</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($transaction->tanggal_perpanjangan)
                                <tr>
                                    <td class="fw-bold">Tanggal Perpanjangan:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->tanggal_perpanjangan)->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Dibuat:</td>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Barang yang Dititipkan -->
            @if($transaction->barangs && count($transaction->barangs) > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>Barang yang Dititipkan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->barangs as $barang)
                                <tr>
                                    <td>
                                        @if($barang->foto_barang)
                                            <img src="{{ asset('storage/' . $barang->foto_barang) }}" 
                                                 alt="{{ $barang->nama_barang }}" 
                                                 class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $barang->nama_barang }}</strong><br>
                                        <small class="text-muted">{{ $barang->kode_barang }}</small>
                                    </td>
                                    <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($barang->status == 'belum_terjual')
                                            <span class="badge bg-success">Belum Terjual</span>
                                        @elseif($barang->status == 'terjual')
                                            <span class="badge bg-primary">Terjual</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($barang->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('consignor.items.show', $barang->barang_id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
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
                    @if($transaction->status == 'aktif' && !$transaction->status_perpanjangan && ($isNearExpiry || $isExpired))
                        <div class="d-grid gap-2">
                            <button type="button" 
                                    class="btn btn-warning btn-lg"
                                    onclick="showExtendModal({{ $transaction->id }}, '{{ $transaction->kode_transaksi }}', '{{ $batasDate->format('d M Y') }}')">
                                <i class="fas fa-calendar-plus me-2"></i>Perpanjang Penitipan
                            </button>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                Perpanjangan akan menambah <strong>30 hari</strong> dari tanggal batas penitipan saat ini.
                            </small>
                        </div>
                    @elseif($transaction->status_perpanjangan)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Sudah Diperpanjang</strong><br>
                            <small>Transaksi ini telah diperpanjang pada {{ \Carbon\Carbon::parse($transaction->tanggal_perpanjangan)->format('d M Y') }}</small>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Perpanjangan hanya dapat dilakukan ketika masa penitipan akan berakhir (7 hari sebelum) atau sudah berakhir.</small>
                        </div>
                    @endif
                    
                    <!-- Additional Actions -->
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('consignor.items') }}" class="btn btn-outline-primary">
                            <i class="fas fa-boxes me-2"></i>Lihat Semua Barang
                        </a>
                        
                        <a href="{{ route('consignor.transactions') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Semua Transaksi
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Transaksi Dibuat</h6>
                                <p class="timeline-text">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($transaction->status_perpanjangan && $transaction->tanggal_perpanjangan)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Diperpanjang</h6>
                                <p class="timeline-text">{{ \Carbon\Carbon::parse($transaction->tanggal_perpanjangan)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $isExpired ? 'bg-danger' : 'bg-primary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $isExpired ? 'Expired' : 'Berakhir' }}</h6>
                                <p class="timeline-text">{{ $batasDate->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Perpanjang Penitipan (sama seperti di index) -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-labelledby="extendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Perpanjang Masa Penitipan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Perpanjangan akan menambah <strong>30 hari</strong> dari tanggal batas penitipan saat ini.
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Kode Transaksi:</label>
                    <p class="fw-bold" id="modalKodeTransaksi"></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Batas Penitipan Saat Ini:</label>
                    <p class="fw-bold text-danger" id="modalBatasLama"></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Batas Penitipan Setelah Diperpanjang:</label>
                    <p class="fw-bold text-success" id="modalBatasBaru"></p>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Setiap transaksi hanya dapat diperpanjang <strong>1 kali</strong> saja.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="confirmExtend()">
                    <i class="fas fa-calendar-plus me-1"></i>Perpanjang Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Memproses perpanjangan...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedTransactionId = null;

function showExtendModal(transactionId, kodeTransaksi, batasLama) {
    selectedTransactionId = transactionId;
    
    // Set data ke modal
    document.getElementById('modalKodeTransaksi').textContent = kodeTransaksi;
    document.getElementById('modalBatasLama').textContent = batasLama;
    
    // Hitung tanggal baru (+30 hari dari batas lama)
    const batasLamaDate = new Date(batasLama);
    const batasBaruDate = new Date(batasLamaDate);
    batasBaruDate.setDate(batasBaruDate.getDate() + 30);
    
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const batasBaru = batasBaruDate.toLocaleDateString('id-ID', options);
    document.getElementById('modalBatasBaru').textContent = batasBaru;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('extendModal'));
    modal.show();
}

function confirmExtend() {
    if (!selectedTransactionId) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        return;
    }
    
    // Hide extend modal
    const extendModal = bootstrap.Modal.getInstance(document.getElementById('extendModal'));
    extendModal.hide();
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Send AJAX request
    fetch('/api/penitip/extend-penitipan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
        },
        body: JSON.stringify({
            transaksi_penitipan_id: selectedTransactionId
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            // Show success message
            showAlert('success', 'Perpanjangan berhasil! Masa penitipan telah diperpanjang 30 hari.');
            
            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('danger', data.message || 'Terjadi kesalahan saat memproses perpanjangan.');
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        showAlert('danger', 'Terjadi kesalahan sistem. Silakan coba lagi.');
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insert alert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
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
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 0.8rem;
    color: #6c757d;
}

.table-warning {
    --bs-table-bg: #fff3cd;
}

.table-danger {
    --bs-table-bg: #f8d7da;
}
</style>
@endpush
