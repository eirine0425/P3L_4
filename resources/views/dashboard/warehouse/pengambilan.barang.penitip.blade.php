@extends('layouts.dashboard')

@section('title', 'Pengambilan Barang Penitip')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Pengambilan Barang Penitip</h1>
            <p class="text-muted">Kelola pengambilan barang yang telah melewati batas waktu penitipan</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reportModal">
                <i class="fas fa-download"></i> Unduh Laporan
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Barang Kadaluarsa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalExpiredItems }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Kadaluarsa 30+ Hari
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $itemsExpired30Plus }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Kadaluarsa 60+ Hari
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $itemsExpired60Plus }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Siap Diambil
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $items->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.warehouse.item-pickup') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nama barang, ID, atau penitip">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="penitip" class="form-label">Penitip</label>
                        <select class="form-select" id="penitip" name="penitip">
                            <option value="">Semua Penitip</option>
                            @foreach($penitips as $penitip)
                                <option value="{{ $penitip->penitip_id }}" 
                                        {{ request('penitip') == $penitip->penitip_id ? 'selected' : '' }}>
                                    {{ $penitip->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->kategori_id }}" 
                                        {{ request('kategori') == $category->kategori_id ? 'selected' : '' }}>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="days_expired" class="form-label">Hari Kadaluarsa</label>
                        <select class="form-select" id="days_expired" name="days_expired">
                            <option value="">Semua</option>
                            <option value="1" {{ request('days_expired') == '1' ? 'selected' : '' }}>1+ Hari</option>
                            <option value="7" {{ request('days_expired') == '7' ? 'selected' : '' }}>7+ Hari</option>
                            <option value="30" {{ request('days_expired') == '30' ? 'selected' : '' }}>30+ Hari</option>
                            <option value="60" {{ request('days_expired') == '60' ? 'selected' : '' }}>60+ Hari</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="sort" class="form-label">Urutkan</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="expired_longest" {{ request('sort') == 'expired_longest' ? 'selected' : '' }}>Terlama Kadaluarsa</option>
                            <option value="expired_shortest" {{ request('sort') == 'expired_shortest' ? 'selected' : '' }}>Terbaru Kadaluarsa</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Barang Siap Diambil</h6>
            @if($items->count() > 0)
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkPickupModal">
                    <i class="fas fa-check-double"></i> Konfirmasi Massal
                </button>
            @endif
        </div>
        <div class="card-body">
            @if($items->count() > 0)
                <form id="bulkForm">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Foto</th>
                                    <th>Barang</th>
                                    <th>Penitip</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Batas Penitipan</th>
                                    <th>Hari Kadaluarsa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->barang_id }}" class="item-checkbox">
                                        </td>
                                        <td>
                                            <img src="{{ $item->photo_url }}" alt="{{ $item->nama_barang }}" 
                                                 class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                <br>
                                                <small class="text-muted">ID: {{ $item->barang_id }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                        <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                        <td>{{ $item->formatted_price }}</td>
                                        <td>{{ $item->batas_penitipan->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $daysExpired = abs($item->sisa_hari);
                                            @endphp
                                            <span class="badge 
                                                @if($daysExpired >= 60) bg-dark
                                                @elseif($daysExpired >= 30) bg-danger
                                                @elseif($daysExpired >= 7) bg-warning
                                                @else bg-info
                                                @endif">
                                                {{ $daysExpired }} hari
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dashboard.warehouse.pickup.detail', $item->barang_id) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="confirmPickup({{ $item->barang_id }}, '{{ $item->nama_barang }}')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }} dari {{ $items->total() }} barang
                    </div>
                    {{ $items->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Tidak ada barang yang siap diambil</h5>
                    <p class="text-muted">Semua barang masih dalam masa penitipan atau sudah diambil.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Single Pickup Modal -->
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
                        <div id="itemName" class="fw-bold"></div>
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

<!-- Bulk Pickup Modal -->
<div class="modal fade" id="bulkPickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengambilan Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.warehouse.pickup.bulk-confirm') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah barang yang dipilih:</label>
                        <div id="selectedCount" class="fw-bold">0 barang</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_pickup_method" class="form-label">Metode Pengambilan</label>
                        <select class="form-select" id="bulk_pickup_method" name="bulk_pickup_method" required>
                            <option value="">Pilih metode pengambilan</option>
                            <option value="penitip_pickup">Diambil Langsung oleh Penitip</option>
                            <option value="courier_delivery">Dikirim via Kurir</option>
                            <option value="warehouse_storage">Disimpan di Gudang</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_pickup_notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="bulk_pickup_notes" name="bulk_pickup_notes" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                    
                    <input type="hidden" id="bulkItemIds" name="item_ids">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Konfirmasi Pengambilan Massal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unduh Laporan Pengambilan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.warehouse.pickup.report') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Unduh Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

// Update selected count
function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    const count = selectedCheckboxes.length;
    document.getElementById('selectedCount').textContent = count + ' barang';
    
    // Update bulk form data
    const itemIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    document.getElementById('bulkItemIds').value = JSON.stringify(itemIds);
}

// Listen to individual checkbox changes
document.querySelectorAll('.item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

// Single pickup confirmation
function confirmPickup(itemId, itemName) {
    document.getElementById('itemName').textContent = itemName;
    document.getElementById('pickupForm').action = `/dashboard/warehouse/pickup/${itemId}/confirm`;
    new bootstrap.Modal(document.getElementById('pickupModal')).show();
}

// Initialize
updateSelectedCount();
</script>
@endpush