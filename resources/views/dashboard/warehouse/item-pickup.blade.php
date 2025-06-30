@extends('layouts.dashboard')

@section('title', 'Pengambilan Barang Tidak Laku')

@section('content')
<div class="container-fluid">
    <!-- Header dengan tombol riwayat pengambilan -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h2>Pengambilan Barang Tidak Laku</h2>
            <p class="text-muted">Kelola pengambilan barang yang sudah melewati batas penitipan</p>
        </div>
        <div>
            <a href="{{ route('dashboard.warehouse.pickup-history') }}" class="btn btn-info me-2">
                <i class="fas fa-history"></i> Riwayat Pengambilan 
                @if(isset($pickedUpCount))
                    <span class="badge bg-light text-dark">{{ $pickedUpCount }}</span>
                @endif
            </a>
            <a href="{{ route('dashboard.warehouse.pickup-report') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Data
            </a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $totalExpiredItems }}</h3>
                            <p class="mb-0">Total Kadaluarsa</p>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $itemsExpired30Plus }}</h3>
                            <p class="mb-0">Lewat 30+ Hari</p>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $itemsExpired60Plus }}</h3>
                            <p class="mb-0">Lewat 60+ Hari</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $pickedUpCount ?? 0 }}</h3>
                            <p class="mb-0">Sudah Diambil</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Barang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.item-pickup') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari nama barang atau penitip..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->kategori_id }}" 
                                                {{ request('kategori') == $category->kategori_id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Penitip</label>
                                <select name="penitip" class="form-select">
                                    <option value="">Semua Penitip</option>
                                    @foreach($penitips as $penitip)
                                        <option value="{{ $penitip->penitip_id }}" 
                                                {{ request('penitip') == $penitip->penitip_id ? 'selected' : '' }}>
                                            {{ $penitip->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Durasi Kadaluarsa</label>
                                <select name="days_expired" class="form-select">
                                    <option value="">Semua Durasi</option>
                                    <option value="1" {{ request('days_expired') == '1' ? 'selected' : '' }}>1+ Hari</option>
                                    <option value="7" {{ request('days_expired') == '7' ? 'selected' : '' }}>7+ Hari</option>
                                    <option value="30" {{ request('days_expired') == '30' ? 'selected' : '' }}>30+ Hari</option>
                                    <option value="60" {{ request('days_expired') == '60' ? 'selected' : '' }}>60+ Hari</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Urutkan</label>
                                <select name="sort" class="form-select">
                                    <option value="expired_longest" {{ request('sort') == 'expired_longest' ? 'selected' : '' }}>Terlama Kadaluarsa</option>
                                    <option value="expired_shortest" {{ request('sort') == 'expired_shortest' ? 'selected' : '' }}>Terbaru Kadaluarsa</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Barang Menunggu Pengambilan 
                        <span class="badge bg-warning">{{ $items->total() }} barang</span>
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="fas fa-check-square me-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                            <i class="fas fa-square me-1"></i>Batal Pilih
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="refreshPage()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($items) > 0)
                        <!-- Bulk Actions -->
                        <div class="row mb-3" id="bulkActions" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <form action="{{ route('dashboard.warehouse.bulk-confirm-pickup') }}" method="POST" id="bulkPickupForm">
                                        @csrf
                                        <div class="row align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label">Konfirmasi pengambilan <span id="selectedCount">0</span> barang:</label>
                                                <select name="bulk_pickup_method" class="form-select" required>
                                                    <option value="">Pilih metode pengambilan...</option>
                                                    <option value="diambil_langsung">Diambil Langsung</option>
                                                    <option value="dikirim_kurir">Dikirim Kurir</option>
                                                    <option value="dititipkan_pihak_lain">Dititipkan Pihak Lain</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Catatan:</label>
                                                <input type="text" name="bulk_pickup_notes" class="form-control" 
                                                       placeholder="Catatan pengambilan...">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success me-2">
                                                    <i class="fas fa-check me-1"></i>Konfirmasi Pengambilan
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="clearSelection()">
                                                    <i class="fas fa-times me-1"></i>Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Foto</th>
                                        <th>Nama Barang</th>
                                        <th>Penitip</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Batas Penitipan</th>
                                        <th>Hari Lewat</th>
                                        <th>Status</th>
                                        <th width="200">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="item_ids[]" value="{{ $item->barang_id }}" 
                                                       class="item-checkbox" onchange="updateBulkActions()">
                                            </td>
                                            <td>
                                                @if($item->foto_barang)
                                                    <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                                                         alt="{{ $item->nama_barang }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                <br><small class="text-muted">ID: {{ $item->barang_id }}</small>
                                            </td>
                                            <td>
                                                {{ $item->penitip->user->name ?? '-' }}
                                                @if($item->penitip && $item->penitip->user && $item->penitip->user->phone)
                                                    <br><small class="text-muted">{{ $item->penitip->user->phone }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                {{ $item->batas_penitipan ? $item->batas_penitipan->format('d M Y') : '-' }}
                                            </td>
                                            <td>
                                                @php
                                                    $daysExpired = $item->batas_penitipan ? 
                                                        now()->diffInDays($item->batas_penitipan, false) : 0;
                                                    $daysExpired = abs($daysExpired);
                                                @endphp
                                                <span class="badge {{ $daysExpired > 60 ? 'bg-danger' : ($daysExpired > 30 ? 'bg-warning' : 'bg-secondary') }}">
                                                    {{ $daysExpired }} hari
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">Menunggu Pengambilan</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dashboard.warehouse.show-pickup-form', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-primary" title="Catat Pengambilan">
                                                        <i class="fas fa-clipboard-check"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="quickPickup({{ $item->barang_id }})" title="Konfirmasi Cepat">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <a href="{{ route('dashboard.warehouse.pickup-detail', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                            onclick="showItemDetail({{ $item->barang_id }})" title="Info Lengkap">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }} 
                                dari {{ $items->total() }} barang
                            </div>
                            <div>
                                {{ $items->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">Tidak ada barang yang menunggu pengambilan</h5>
                            <p class="text-muted">Semua barang masih dalam periode penitipan yang valid.</p>
                            <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Pickup Modal -->
<div class="modal fade" id="quickPickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengambilan Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickPickupForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Metode Pengambilan</label>
                        <select name="pickup_method" class="form-select" required>
                            <option value="">Pilih metode pengambilan...</option>
                            <option value="diambil_langsung">Diambil Langsung oleh Penitip</option>
                            <option value="dikirim_kurir">Dikirim via Kurir</option>
                            <option value="dititipkan_pihak_lain">Dititipkan ke Pihak Lain</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="pickup_notes" class="form-control" rows="3" 
                                  placeholder="Catatan pengambilan (opsional)"></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Setelah dikonfirmasi, status barang akan berubah menjadi "Diambil Kembali" dan tidak dapat dibatalkan.
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

<!-- Item Detail Modal -->
<div class="modal fade" id="itemDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="itemDetailContent">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectAll() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = true);
        document.getElementById('selectAllCheckbox').checked = true;
        updateBulkActions();
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkActions();
    }

    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (bulkActions && selectedCount) {
            if (checkedBoxes.length > 0) {
                bulkActions.style.display = 'block';
                selectedCount.textContent = checkedBoxes.length;
                
                // Update hidden inputs for bulk form
                const bulkForm = document.getElementById('bulkPickupForm');
                if (bulkForm) {
                    const existingInputs = bulkForm.querySelectorAll('input[name="item_ids[]"]');
                    existingInputs.forEach(input => input.remove());
                    
                    checkedBoxes.forEach(checkbox => {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'item_ids[]';
                        hiddenInput.value = checkbox.value;
                        bulkForm.appendChild(hiddenInput);
                    });
                }
            } else {
                bulkActions.style.display = 'none';
            }
        }
    }

    function quickPickup(itemId) {
        const modal = new bootstrap.Modal(document.getElementById('quickPickupModal'));
        const form = document.getElementById('quickPickupForm');
        form.action = `/dashboard/warehouse/item-pickup/${itemId}/confirm`;
        modal.show();
    }

    function showItemDetail(itemId) {
        const modal = new bootstrap.Modal(document.getElementById('itemDetailModal'));
        const content = document.getElementById('itemDetailContent');
        
        // Show loading
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail barang...</p>
            </div>
        `;
        
        modal.show();
        
        // Load content via AJAX
        fetch(`/dashboard/warehouse/items/${itemId}`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Gagal memuat detail barang. Silakan coba lagi.
                    </div>
                `;
            });
    }

    function refreshPage() {
        window.location.reload();
    }

    // Auto-refresh every 5 minutes
    setInterval(function() {
        if (document.querySelectorAll('.item-checkbox:checked').length === 0) {
            window.location.reload();
        }
    }, 300000); // 5 minutes

    // Show success message if any
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
</script>
@endpush
