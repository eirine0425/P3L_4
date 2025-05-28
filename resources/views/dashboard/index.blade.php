@extends('layouts.dashboard')

@section('title', 'Dashboard Gudang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Gudang</h2>
            <p class="text-muted">Kelola inventaris dan barang titipan</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $totalItems }}</h3>
                            <p>Total Barang</p>
                        </div>
                        <div>
                            <i class="fas fa-box fa-2x"></i>
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
                            <h3>{{ $activeItems }}</h3>
                            <p>Belum Terjual</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $soldItems }}</h3>
                            <p>Terjual</p>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                            <h3>{{ $soldOutItems }}</h3>
                            <p>Sold Out</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Search Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-search me-2"></i>Pencarian Barang Titipan
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch">
                        <i class="fas fa-filter me-1"></i>Filter Lanjutan
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.index') }}" method="GET" id="searchForm">
                        <!-- Basic Search -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Cari nama barang, penitip, kategori, atau ID barang..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search me-1"></i>Cari
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group w-100">
                                    <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Reset
                                    </a>
                                    @if(request()->hasAny(['search', 'status', 'kategori', 'min_price', 'max_price', 'start_date', 'end_date', 'kondisi']))
                                        <button type="button" class="btn btn-info" onclick="exportResults()">
                                            <i class="fas fa-download me-1"></i>Export
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Filters -->
                        <div class="collapse {{ request()->hasAny(['status', 'kategori', 'min_price', 'max_price', 'start_date', 'end_date', 'kondisi', 'sort']) ? 'show' : '' }}" id="advancedSearch">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        @if(isset($statusOptions))
                                            @foreach($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        @if(isset($categories))
                                            @foreach($categories as $category)
                                                <option value="{{ $category->kategori_id }}" {{ request('kategori') == $category->kategori_id ? 'selected' : '' }}>
                                                    {{ $category->nama_kategori }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kondisi</label>
                                    <select name="kondisi" class="form-select">
                                        <option value="">Semua Kondisi</option>
                                        @if(isset($kondisiOptions))
                                            @foreach($kondisiOptions as $value => $label)
                                                <option value="{{ $value }}" {{ request('kondisi') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Urutkan</label>
                                    <select name="sort" class="form-select">
                                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">Harga Minimum</label>
                                    <input type="number" name="min_price" class="form-control" 
                                           placeholder="0" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Harga Maksimum</label>
                                    <input type="number" name="max_price" class="form-control" 
                                           placeholder="999999999" value="{{ request('max_price') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control" 
                                           value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control" 
                                           value="{{ request('end_date') }}">
                                </div>
                            </div>

                            <!-- Save Search -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" id="searchName" class="form-control" 
                                               placeholder="Nama pencarian untuk disimpan...">
                                        <button type="button" class="btn btn-outline-primary" onclick="saveCurrentSearch()">
                                            <i class="fas fa-save me-1"></i>Simpan Pencarian
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select id="savedSearches" class="form-select" onchange="loadSavedSearch()">
                                        <option value="">Pilih pencarian tersimpan...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    @if(isset($searchResults) && $searchResults)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="fas fa-list me-2"></i>Hasil Pencarian 
                            <span class="badge bg-primary">{{ $searchResults->total() }} barang</span>
                        </h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                                <i class="fas fa-square me-1"></i>Batal Pilih
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($searchResults) > 0)
                            <!-- Bulk Actions -->
                            <div class="row mb-3" id="bulkActions" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <form action="{{ route('dashboard.warehouse.bulk-update') }}" method="POST" id="bulkForm">
                                            @csrf
                                            <div class="row align-items-end">
                                                <div class="col-md-4">
                                                    <label class="form-label">Aksi untuk <span id="selectedCount">0</span> barang terpilih:</label>
                                                    <select name="bulk_status" class="form-select" required>
                                                        <option value="">Pilih status baru...</option>
                                                        <option value="belum_terjual">Belum Terjual</option>
                                                        <option value="terjual">Terjual</option>
                                                        <option value="sold out">Sold Out</option>
                                                        <option value="untuk_donasi">Untuk Donasi</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fas fa-edit me-1"></i>Update Status
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
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Penitip</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                            <th>Kondisi</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($searchResults as $item)
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
                                                    <strong>{{ $item->barang_id }}</strong>
                                                </td>
                                                <td>
                                                    <strong>{{ $item->nama_barang }}</strong>
                                                    @if(isset($item->stok))
                                                        <br><small class="text-muted">Stok: {{ $item->stok }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($item->status == 'belum_terjual')
                                                        <span class="badge bg-success">Belum Terjual</span>
                                                    @elseif($item->status == 'terjual')
                                                        <span class="badge bg-info">Terjual</span>
                                                    @elseif($item->status == 'sold out')
                                                        <span class="badge bg-secondary">Sold Out</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($item->kondisi))
                                                        @if($item->kondisi == 'baru')
                                                            <span class="badge bg-primary">Baru</span>
                                                        @elseif($item->kondisi == 'sangat_layak')
                                                            <span class="badge bg-success">Sangat Layak</span>
                                                        @else
                                                            <span class="badge bg-warning">Layak</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('dashboard.warehouse.item.edit', $item->barang_id) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit Barang">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('products.show', $item->barang_id) }}" 
                                                           class="btn btn-sm btn-outline-info" target="_blank" title="Lihat di Katalog">
                                                            <i class="fas fa-external-link-alt"></i>
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
                                    Menampilkan {{ $searchResults->firstItem() }} - {{ $searchResults->lastItem() }} 
                                    dari {{ $searchResults->total() }} hasil
                                </div>
                                <div>
                                    {{ $searchResults->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada hasil ditemukan</h5>
                                <p class="text-muted">Coba ubah kata kunci atau filter pencarian Anda.</p>
                                <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-primary">
                                    <i class="fas fa-refresh me-1"></i>Reset Pencarian
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Quick Actions -->
    @if(!isset($searchResults) || !$searchResults)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-plus me-2"></i>
                                Tambah Barang Titipan
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-boxes me-2"></i>Kelola Inventaris
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('dashboard.warehouse.shipments.ready') }}" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-shipping-fast me-2"></i>
                                Transaksi Siap Kirim
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('products.index') }}" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-store me-2"></i>Lihat Katalog
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="#" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-chart-bar me-2"></i>Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Items -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Barang Terbaru</h5>
                    <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if(isset($recentItems) && count($recentItems) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Penitip</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                            </td>
                                            <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if($item->status == 'belum_terjual')
                                                    <span class="badge bg-success">Belum Terjual</span>
                                                @elseif($item->status == 'terjual')
                                                    <span class="badge bg-info">Terjual</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada barang yang terdaftar.</p>
                            <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Barang Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Barang</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Distribusi Kategori Barang</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Charts (only show when not searching)
    @if(!isset($searchResults) || !$searchResults)
    // Status Chart
    @if(isset($itemsByStatus) && count($itemsByStatus) > 0)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($itemsByStatus as $status)
                    '{{ ucfirst(str_replace("_", " ", $status->status)) }}'{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($itemsByStatus as $status)
                        {{ $status->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: [
                    '#28a745', // Belum Terjual - Green
                    '#17a2b8', // Terjual - Blue
                    '#6c757d'  // Sold Out - Gray
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
    
    // Category Chart
    @if(isset($itemsByCategory) && count($itemsByCategory) > 0)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($itemsByCategory as $category)
                    '{{ $category->nama_kategori }}'{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Barang',
                data: [
                    @foreach($itemsByCategory as $category)
                        {{ $category->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: 'rgba(76, 175, 80, 0.8)',
                borderColor: 'rgba(76, 175, 80, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif
    @endif

    // Search functionality
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
                const bulkForm = document.getElementById('bulkForm');
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

    function exportResults() {
        const form = document.getElementById('searchForm');
        const originalAction = form.action;
        form.action = '{{ route("dashboard.warehouse.export") }}';
        form.submit();
        form.action = originalAction;
    }

    function saveCurrentSearch() {
        const searchName = document.getElementById('searchName').value;
        if (!searchName) {
            alert('Masukkan nama untuk pencarian ini');
            return;
        }

        const formData = new FormData(document.getElementById('searchForm'));
        const searchParams = {};
        for (let [key, value] of formData.entries()) {
            if (value) searchParams[key] = value;
        }

        fetch('{{ route("dashboard.warehouse.save-search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                search_name: searchName,
                search_params: searchParams
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pencarian berhasil disimpan');
                loadSavedSearches();
                document.getElementById('searchName').value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menyimpan pencarian');
        });
    }

    function loadSavedSearches() {
        fetch('{{ route("dashboard.warehouse.saved-searches") }}')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('savedSearches');
            if (select) {
                select.innerHTML = '<option value="">Pilih pencarian tersimpan...</option>';
                
                Object.keys(data).forEach(name => {
                    const option = document.createElement('option');
                    option.value = name;
                    option.textContent = name;
                    select.appendChild(option);
                });
            }
        });
    }

    function loadSavedSearch() {
        const select = document.getElementById('savedSearches');
        const searchName = select.value;
        
        if (!searchName) return;

        fetch('{{ route("dashboard.warehouse.saved-searches") }}')
        .then(response => response.json())
        .then(data => {
            const searchParams = data[searchName];
            if (searchParams) {
                // Fill form with saved parameters
                Object.keys(searchParams).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = searchParams[key];
                    }
                });
                
                // Submit form
                document.getElementById('searchForm').submit();
            }
        });
    }

    // Load saved searches on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadSavedSearches();
    });
</script>
@endpush
