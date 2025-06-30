@extends('layouts.dashboard')

@section('title', 'Kelola Klaim Merchandise')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Kelola Klaim Merchandise</h2>
                    <p class="text-muted">Kelola dan proses klaim merchandise dari pembeli</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                        <i class="fas fa-tasks"></i> Aksi Massal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('cs.merchandise.claims') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ready_pickup" {{ request('status') == 'ready_pickup' ? 'selected' : '' }}>Siap Diambil</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Nama pembeli atau merchandise..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ route('cs.merchandise.claims') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Claims Table -->
    <div class="card">
        <div class="card-body">
            <form id="bulkForm">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Pembeli</th>
                                <th>Merchandise</th>
                                <th>Tanggal Klaim</th>
                                <th>Tanggal Ambil</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($claims as $claim)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_claims[]" 
                                               value="{{ $claim->pembeli_id }}|{{ $claim->merch_id }}|{{ $claim->tanggal_penukaran }}" 
                                               class="form-check-input claim-checkbox">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ optional(optional($claim->pembeli)->user)->name ?? '-' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ optional(optional($claim->pembeli)->user)->email ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ optional($claim->merch)->nama_merch ?? '-' }}</strong>
                                            <br>
                                            <small class="text-muted">Poin: {{ optional($claim->merch)->poin_diperlukan ?? 0 }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $claim->tanggal_penukaran ? \Carbon\Carbon::parse($claim->tanggal_penukaran)->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        @if($claim->tanggal_ambil)
                                            <span class="badge bg-info">{{ \Carbon\Carbon::parse($claim->tanggal_ambil)->format('d M Y') }}</span>
                                        @else
                                            <span class="text-muted">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($claim->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-info">Disetujui</span>
                                                @break
                                            @case('ready_pickup')
                                                <span class="badge bg-primary">Siap Diambil</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Selesai</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $claim->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('cs.merchandise.claims.show', [$claim->pembeli_id, $claim->merch_id, $claim->tanggal_penukaran]) }}" 
                                               class="btn btn-sm btn-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($claim->status == 'pending')
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="quickApprove('{{ $claim->pembeli_id }}', '{{ $claim->merch_id }}', '{{ $claim->tanggal_penukaran }}')"
                                                        title="Setujui Cepat">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada klaim merchandise ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        
        @if($claims->hasPages())
            <div class="card-footer">
                {{ $claims->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cs.merchandise.claims.bulk-update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aksi Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_action" class="form-label">Pilih Aksi</label>
                        <select name="bulk_action" id="bulk_action" class="form-select" required>
                            <option value="">Pilih aksi...</option>
                            <option value="approve">Setujui</option>
                            <option value="ready_pickup">Siap Diambil</option>
                            <option value="complete">Selesaikan</option>
                            <option value="cancel">Batalkan</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tanggal_ambil_group" style="display: none;">
                        <label for="tanggal_ambil" class="form-label">Tanggal Ambil</label>
                        <input type="date" name="tanggal_ambil" id="tanggal_ambil" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <input type="hidden" name="claim_ids" id="selected_claim_ids">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Approve Modal -->
<div class="modal fade" id="quickApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="quickApproveForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Klaim Merchandise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_tanggal_ambil" class="form-label">Tanggal Ambil</label>
                        <input type="date" name="tanggal_ambil" id="quick_tanggal_ambil" class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick_catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="quick_catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    <input type="hidden" name="status" value="approved">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const claimCheckboxes = document.querySelectorAll('.claim-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        claimCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Show/hide tanggal ambil based on bulk action
    const bulkActionSelect = document.getElementById('bulk_action');
    const tanggalAmbilGroup = document.getElementById('tanggal_ambil_group');
    
    bulkActionSelect.addEventListener('change', function() {
        if (this.value === 'approve' || this.value === 'ready_pickup') {
            tanggalAmbilGroup.style.display = 'block';
        } else {
            tanggalAmbilGroup.style.display = 'none';
        }
    });
    
    // Handle bulk action modal
    const bulkActionModal = document.getElementById('bulkActionModal');
    bulkActionModal.addEventListener('show.bs.modal', function() {
        const selectedClaims = Array.from(document.querySelectorAll('.claim-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedClaims.length === 0) {
            alert('Pilih minimal satu klaim untuk diproses');
            return false;
        }
        
        document.getElementById('selected_claim_ids').value = JSON.stringify(selectedClaims);
    });
});

function quickApprove(pembeliId, merchId, tanggalPenukaran) {
    const form = document.getElementById('quickApproveForm');
    form.action = `/dashboard/cs/merchandise-claims/${pembeliId}/${merchId}/${tanggalPenukaran}`;
    
    const modal = new bootstrap.Modal(document.getElementById('quickApproveModal'));
    modal.show();
}
</script>
@endpush
