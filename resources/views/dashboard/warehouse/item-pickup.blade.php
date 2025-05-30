@extends('layouts.dashboard')

@section('title', 'Pengambilan Barang Tidak Laku')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Pengambilan Barang Tidak Laku</h2>
            <p class="text-muted">Kelola pengambilan barang yang sudah melewati batas penitipan</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $totalExpiredItems }}</h3>
                            <p>Total Kadaluarsa</p>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $itemsExpired30Plus }}</h3>
                            <p>Lewat 30+ Hari</p>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $itemsExpired60Plus }}</h3>
                            <p>Lewat 60+ Hari</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
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
                    <h5 class="card-title">Filter Barang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.item-pickup') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari nama barang atau penitip..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
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
                                <select name="days_expired" class="form-select">
                                    <option value="">Semua Durasi</option>
                                    <option value="1" {{ request('days_expired') == '1' ? 'selected' : '' }}>1+ Hari</option>
                                    <option value="7" {{ request('days_expired') == '7' ? 'selected' : '' }}>7+ Hari</option>
                                    <option value="30" {{ request('days_expired') == '30' ? 'selected' : '' }}>30+ Hari</option>
                                    <option value="60" {{ request('days_expired') == '60' ? 'selected' : '' }}>60+ Hari</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort" class="form-select">
                                    <option value="expired_longest" {{ request('sort') == 'expired_longest' ? 'selected' : '' }}>Terlama Kadaluarsa</option>
                                    <option value="expired_shortest" {{ request('sort') == 'expired_shortest' ? 'selected' : '' }}>Terbaru Kadaluarsa</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                </select>
                            </div>
                            <div class="col-md-1">
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
                    <h5 class="card-title">
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
                        <a href="{{ route('dashboard.warehouse.pickup-report') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download me-1"></i>Export
                        </a>
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
                                                    <option value="penitip_pickup">Penitip Ambil Sendiri</option>
                                                    <option value="courier_delivery">Kurir Antar</option>
                                                    <option value="warehouse_storage">Simpan di Gudang</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Catatan:</label>
                                                <input type="text" name="bulk_pickup_notes" class="form-control" 
                                                       placeholder="Catatan pengambilan...">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success">
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
                                <thead>
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
                                        <th>Aksi</th>
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
                                            <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>{{ $item->batas_penitipan->format('d M Y') }}</td>
                                            <td>
                                                @php
                                                    $daysExpired = abs($item->sisa_hari);
                                                @endphp
                                                <span class="badge bg-danger">{{ $daysExpired }} hari</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">Menunggu Pengambilan</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="confirmPickup({{ $item->barang_id }})" title="Konfirmasi Pengambilan">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <a href="{{ route('dashboard.warehouse.pickup-detail', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">Tidak ada barang yang menunggu pengambilan</h5>
                            <p class="text-muted">Semua barang masih dalam periode penitipan yang valid.</p>
                        </div>
                    @endif
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
            <form id="pickupForm" method="POST">
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
    function selectAll() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = true);
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

    function confirmPickup(itemId) {
        const modal = new bootstrap.Modal(document.getElementById('pickupModal'));
        const form = document.getElementById('pickupForm');
        form.action = `/dashboard/warehouse/item-pickup/${itemId}/confirm`;
        modal.show();
    }
</script>
@endpush
