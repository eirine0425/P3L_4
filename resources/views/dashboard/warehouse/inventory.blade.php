@extends('layouts.dashboard')

@section('title', 'Inventaris Gudang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Inventaris Gudang</h2>
            <p class="text-muted">Kelola semua barang titipan</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.inventory') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari nama barang atau penitip..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="belum_terjual" {{ request('status') == 'belum_terjual' ? 'selected' : '' }}>Belum Terjual</option>
                                <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                <option value="sold out" {{ request('status') == 'sold out' ? 'selected' : '' }}>Sold Out</option>
                                <option value="untuk_donasi" {{ request('status') == 'untuk_donasi' ? 'selected' : '' }}>Untuk Donasi</option>
                            </select>
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
                            <select name="sort" class="form-select">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row mb-3" id="bulkActions" style="display: none;">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.print-bulk-notes') }}" method="POST" id="bulkPrintForm" class="d-inline">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <span class="fw-bold">Aksi untuk <span id="selectedCount">0</span> barang terpilih:</span>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-info me-2">
                                    <i class="fas fa-print me-1"></i>Cetak Nota PDF
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
    </div>

    <!-- Items Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Daftar Barang
                        <span class="badge bg-primary ms-2">{{ $items->total() }}</span>
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
                    @if($items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Foto</th>
                                        <th>ID</th>
                                        <th>Nama Barang</th>
                                        <th>Penitip</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Durasi</th>
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
                                            <td><strong>{{ $item->barang_id }}</strong></td>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                @if($item->deskripsi)
                                                    <br><small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
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
                                                @try
                                                    @if($item->sisa_hari > 7)
                                                        <span class="badge bg-success">{{ $item->sisa_hari }} hari</span>
                                                    @elseif($item->sisa_hari > 0)
                                                        <span class="badge bg-warning">{{ $item->sisa_hari }} hari</span>
                                                    @elseif($item->sisa_hari == 0)
                                                        <span class="badge bg-danger">Hari ini</span>
                                                    @else
                                                        <span class="badge bg-danger">Kadaluarsa</span>
                                                    @endif
                                                @catch(\Exception $e)
                                                    <span class="badge bg-secondary">-</span>
                                                @endtry
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dashboard.warehouse.item.show', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.warehouse.item.edit', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.warehouse.print-note', $item->barang_id) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Cetak Nota PDF">
                                                        <i class="fas fa-print"></i>
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
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada barang ditemukan</h5>
                            <p class="text-muted">Coba ubah filter pencarian atau tambah barang baru.</p>
                            <a href="{{ route('dashboard.warehouse.consignment.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Tambah Barang Titipan
                            </a>
                        </div>
                    @endif
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
                const bulkForm = document.getElementById('bulkPrintForm');
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
</script>
@endpush
