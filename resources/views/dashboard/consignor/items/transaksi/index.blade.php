@extends('layouts.dashboard')

@section('title', 'Transaksi Penitipan Saya')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Transaksi Penitipan Saya</h2>
                    <p class="text-muted">Kelola transaksi penitipan barang Anda</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('consignor.transactions') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Transaksi</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Kode transaksi...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('consignor.transactions') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transactions List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Batas Penitipan</th>
                                        <th>Status</th>
                                        <th>Perpanjangan</th>
                                        <th>Sisa Hari</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        @php
                                            $batasDate = \Carbon\Carbon::parse($transaction->batas_penitipan);
                                            $today = \Carbon\Carbon::now();
                                            $sisaHari = $today->diffInDays($batasDate, false);
                                            $isExpired = $sisaHari < 0;
                                            $isNearExpiry = $sisaHari <= 7 && $sisaHari >= 0;
                                        @endphp
                                        <tr class="{{ $isExpired ? 'table-danger' : ($isNearExpiry ? 'table-warning' : '') }}">
                                            <td>
                                                <strong>{{ $transaction->kode_transaksi }}</strong>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->tanggal_penitipan)->format('d M Y') }}</td>
                                            <td>{{ $batasDate->format('d M Y') }}</td>
                                            <td>
                                                @if($transaction->status == 'aktif')
                                                    @if($isExpired)
                                                        <span class="badge bg-danger">Expired</span>
                                                    @elseif($isNearExpiry)
                                                        <span class="badge bg-warning">Akan Berakhir</span>
                                                    @else
                                                        <span class="badge bg-success">Aktif</span>
                                                    @endif
                                                @elseif($transaction->status == 'selesai')
                                                    <span class="badge bg-primary">Selesai</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->status_perpanjangan)
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-check me-1"></i>Sudah Diperpanjang
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark">Belum Diperpanjang</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isExpired)
                                                    <span class="text-danger fw-bold">Expired {{ abs($sisaHari) }} hari</span>
                                                @elseif($isNearExpiry)
                                                    <span class="text-warning fw-bold">{{ $sisaHari }} hari lagi</span>
                                                @else
                                                    <span class="text-success">{{ $sisaHari }} hari lagi</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('consignor.transactions.show', $transaction->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($transaction->status == 'aktif' && !$transaction->status_perpanjangan && ($isNearExpiry || $isExpired))
                                                        <button type="button" 
                                                                class="btn btn-sm btn-warning" 
                                                                title="Perpanjang Penitipan"
                                                                onclick="showExtendModal({{ $transaction->id }}, '{{ $transaction->kode_transaksi }}', '{{ $batasDate->format('d M Y') }}')">
                                                            <i class="fas fa-calendar-plus"></i> Perpanjang
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum ada transaksi penitipan</h4>
                            <p class="text-muted">Transaksi penitipan Anda akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Perpanjang Penitipan -->
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
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token') // Jika menggunakan token
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

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
.table-warning {
    --bs-table-bg: #fff3cd;
}

.table-danger {
    --bs-table-bg: #f8d7da;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.modal-content {
    border-radius: 10px;
}

.alert {
    border-radius: 8px;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
