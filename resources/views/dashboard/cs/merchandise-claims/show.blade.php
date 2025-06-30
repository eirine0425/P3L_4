@extends('layouts.dashboard')

@section('title', 'Detail Klaim Merchandise')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Klaim Merchandise</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.cs') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('cs.merchandise.claims') }}">Klaim Merchandise</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('cs.merchandise.claims') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Claim Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Klaim</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Klaim:</strong></td>
                                    <td>{{ $claim->pembeli_id }}-{{ $claim->merch_id }}-{{ $claim->tanggal_penukaran }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Klaim:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($claim->tanggal_penukaran)->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
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
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Ambil:</strong></td>
                                    <td>
                                        @if($claim->tanggal_ambil)
                                            <span class="badge bg-info">{{ \Carbon\Carbon::parse($claim->tanggal_ambil)->format('d M Y') }}</span>
                                        @else
                                            <span class="text-muted">Belum ditentukan</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($claim->catatan)
                                <div class="alert alert-info">
                                    <strong>Catatan:</strong><br>
                                    {{ $claim->catatan }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buyer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Pembeli</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td>{{ optional(optional($claim->pembeli)->user)->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ optional(optional($claim->pembeli)->user)->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. Telepon:</strong></td>
                                    <td>{{ optional(optional($claim->pembeli)->user)->phone_number ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Poin Loyalitas:</strong></td>
                                    <td>{{ optional($claim->pembeli)->poin_loyalitas ?? 0 }} poin</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Registrasi:</strong></td>
                                    <td>{{ optional($claim->pembeli)->tanggal_registrasi ? \Carbon\Carbon::parse($claim->pembeli->tanggal_registrasi)->format('d M Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Merchandise Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Merchandise</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if(optional($claim->merch)->foto_merch)
                                <img src="{{ asset('storage/' . $claim->merch->foto_merch) }}" 
                                     alt="{{ optional($claim->merch)->nama }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Merchandise:</strong></td>
                                    <td>{{ optional($claim->merch)->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi:</strong></td>
                                    <td>{{ optional($claim->merch)->nama ? 'Merchandise ' . $claim->merch->nama . ' dengan poin ' . $claim->merch->jumlah_poin : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Poin Diperlukan:</strong></td>
                                    <td>{{ optional($claim->merch)->jumlah_poin ?? 0 }} poin</td>
                                </tr>
                                <tr>
                                    <td><strong>Stok:</strong></td>
                                    <td>{{ optional($claim->merch)->stock_merch ?? 0 }} unit</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Action Panel -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cs.merchandise.claims.update', [$claim->pembeli_id, $claim->merch_id, $claim->tanggal_penukaran]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $claim->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ $claim->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ready_pickup" {{ $claim->status == 'ready_pickup' ? 'selected' : '' }}>Siap Diambil</option>
                                <option value="completed" {{ $claim->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ $claim->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="mb-3" id="tanggal_ambil_group">
                            <label for="tanggal_ambil" class="form-label">Tanggal Ambil</label>
                            <input type="date" name="tanggal_ambil" id="tanggal_ambil" class="form-control" 
                                   min="{{ date('Y-m-d') }}" 
                                   value="{{ $claim->tanggal_ambil ? \Carbon\Carbon::parse($claim->tanggal_ambil)->format('Y-m-d') : '' }}">
                            <small class="form-text text-muted">Kosongkan jika belum ditentukan</small>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3" 
                                      placeholder="Tambahkan catatan jika diperlukan...">{{ $claim->catatan ?? '' }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($claim->status == 'pending')
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="quickAction('approved')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="quickAction('cancelled')">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($claim->status == 'approved')
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-sm" onclick="quickAction('ready_pickup')">
                                <i class="fas fa-box"></i> Siap Diambil
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($claim->status == 'ready_pickup')
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid">
                            <button type="button" class="btn btn-success btn-sm" onclick="quickAction('completed')">
                                <i class="fas fa-check-circle"></i> Selesaikan
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const tanggalAmbilGroup = document.getElementById('tanggal_ambil_group');
    
    function toggleTanggalAmbil() {
        const status = statusSelect.value;
        if (status === 'approved' || status === 'ready_pickup') {
            tanggalAmbilGroup.style.display = 'block';
        } else {
            tanggalAmbilGroup.style.display = 'none';
        }
    }
    
    statusSelect.addEventListener('change', toggleTanggalAmbil);
    toggleTanggalAmbil(); // Initial call
});

function quickAction(status) {
    document.getElementById('status').value = status;
    
    if (status === 'approved' || status === 'ready_pickup') {
        // Set tanggal ambil to tomorrow if not set
        const tanggalAmbilInput = document.getElementById('tanggal_ambil');
        if (!tanggalAmbilInput.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tanggalAmbilInput.value = tomorrow.toISOString().split('T')[0];
        }
    }
    
    // Submit form
    document.querySelector('form').submit();
}
</script>
@endpush
