@extends('layouts.dashboard')

@section('title', 'Transaksi Berakhir Hari Ini')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Transaksi Berakhir Hari Ini</h2>
                </div>
                <div>
                    <a href="{{ route('dashboard.consignor') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Transaksi Berakhir
                        <span class="badge bg-warning ms-2">{{ $expiringItems->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($expiringItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Barang</th>
                                        <th>Tanggal Penitipan</th>
                                        <th>Batas Penitipan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringItems as $item)
                                        <tr>
                                            <td>
                                                <strong>#{{ $item->barang_id }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->foto_barang)
                                                        <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                                                             alt="{{ $item->nama_barang }}" 
                                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->nama_barang }}</strong><br>
                                                        <small class="text-muted">
                                                            {{ $item->kategori->nama_kategori ?? 'Kategori Tidak Diketahui' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($item->tanggal_penitipan)->format('d M Y') }}</strong><br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal_penitipan)->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-danger">{{ \Carbon\Carbon::parse($item->batas_penitipan)->format('d M Y') }}</strong><br>
                                                <small class="text-danger">
                                                    <i class="fas fa-clock me-1"></i>Berakhir hari ini
                                                </small>
                                            </td>
                                            <td>
                                                @if($item->status == 'belum_terjual')
                                                    <span class="badge bg-success">Belum Terjual</span>
                                                @elseif($item->status == 'terjual')
                                                    <span class="badge bg-info">Terjual</span>
                                                @elseif($item->status == 'sold out')
                                                    <span class="badge bg-danger">Sold Out</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('consignor.transactions.expiring.show', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if(\Carbon\Carbon::parse($item->batas_penitipan)->lte(now()->endOfDay()))
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                onclick="confirmExtend({{ $item->barang_id }}, '{{ $item->nama_barang }}')"
                                                                title="Perpanjang Masa Penitipan">
                                                            <i class="fas fa-plus-circle"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-success" disabled title="Sudah Diperpanjang">
                                                            <i class="fas fa-check"></i>
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
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <small class="text-muted">
                                    Menampilkan {{ $expiringItems->firstItem() }} - {{ $expiringItems->lastItem() }} 
                                    dari {{ $expiringItems->total() }} barang
                                </small>
                            </div>
                            <div>
                                {{ $expiringItems->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Transaksi Berakhir Hari Ini</h5>
                            <p class="text-muted">Semua transaksi penitipan Anda masih dalam masa aktif.</p>
                            <a href="{{ route('consignor.items') }}" class="btn btn-primary">
                                <i class="fas fa-box me-2"></i>Lihat Semua Barang
                            </a>
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
                        <strong class="text-danger">{{ now()->format('d F Y') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Setelah diperpanjang:</small><br>
                        <strong class="text-success">{{ now()->addDays(30)->format('d F Y') }}</strong>
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
function confirmExtend(transactionId, itemName) {
    document.getElementById('itemName').textContent = itemName;
    document.getElementById('extendForm').action = '{{ route("consignor.transactions.extend", ":id") }}'.replace(':id', transactionId);
    
    var modal = new bootstrap.Modal(document.getElementById('extendModal'));
    modal.show();
}

// Auto refresh every 5 minutes to update expiring status
setInterval(function() {
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 300000); // 5 minutes
</script>
@endpush

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

.table-responsive {
    border-radius: 0.375rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush
