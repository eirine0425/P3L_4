@extends('layouts.dashboard')

@section('title', 'Transaksi Siap Kirim')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Transaksi Siap Kirim</h2>
                    <p class="text-muted">Kelola pengiriman dan pengambilan barang</p>
                </div>
                <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $totalReady }}</h3>
                            <p>Total Siap Kirim</p>
                        </div>
                        <div>
                            <i class="fas fa-shipping-fast fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $readyForPickup }}</h3>
                            <p>Siap Diambil</p>
                        </div>
                        <div>
                            <i class="fas fa-hand-paper fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $readyForDelivery }}</h3>
                            <p>Siap Dikirim</p>
                        </div>
                        <div>
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-filter me-2"></i>Filter & Pencarian
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.shipments.ready') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Pencarian</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="ID Transaksi atau Nama Pembeli..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Metode</label>
                            <select name="metode" class="form-select">
                                <option value="">Semua Metode</option>
                                <option value="pickup" {{ request('metode') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                <option value="delivery" {{ request('metode') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Menunggu Pengiriman" {{ request('status') == 'Menunggu Pengiriman' ? 'selected' : '' }}>Menunggu Pengiriman</option>
                                <option value="Dijadwalkan" {{ request('status') == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="customer_asc" {{ request('sort') == 'customer_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="customer_desc" {{ request('sort') == 'customer_desc' ? 'selected' : '' }}>Nama Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                                <a href="{{ route('dashboard.warehouse.shipments.ready') }}" class="btn btn-secondary">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-list me-2"></i>Daftar Transaksi 
                        <span class="badge bg-primary">{{ $transactions->total() }} transaksi</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Pembeli</th>
                                        <th>Total Barang</th>
                                        <th>Total Harga</th>
                                        <th>Metode Pengiriman</th>
                                        <th>Status Pengiriman</th>
                                        <th>Tanggal Transaksi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <strong>{{ $transaction->transaksi_id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $transaction->pembeli->user->name ?? '-' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $transaction->pembeli->user->email ?? '-' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $transaction->detailTransaksi->count() }} item</span>
                                            </td>
                                            <td>
                                                <strong>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                @if($transaction->pengiriman)
                                                    @if($transaction->pengiriman->metode_pengiriman == 'Pickup')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-hand-paper me-1"></i>Pickup
                                                        </span>
                                                    @else
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-truck me-1"></i>{{ $transaction->pengiriman->metode_pengiriman }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Belum Ditentukan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->pengiriman)
                                                    @if($transaction->pengiriman->status_pengiriman == 'Menunggu Pengiriman')
                                                        <span class="badge bg-warning">Menunggu Pengiriman</span>
                                                    @elseif($transaction->pengiriman->status_pengiriman == 'Dijadwalkan')
                                                        <span class="badge bg-info">Dijadwalkan</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $transaction->pengiriman->status_pengiriman }}</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Belum Diproses</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $transaction->created_at->format('d M Y H:i') }}
                                                <br>
                                                <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dashboard.warehouse.shipment.detail', $transaction->transaksi_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Dijadwalkan')
                                                        <a href="{{ route('dashboard.warehouse.shipping.label', $transaction->transaksi_id) }}" 
                                                           class="btn btn-sm btn-outline-info" title="Print Label" target="_blank">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                    @endif
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle" 
                                                                data-bs-toggle="dropdown" title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <form action="{{ route('dashboard.warehouse.shipment.update-status', $transaction->transaksi_id) }}" 
                                                                      method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status_pengiriman" value="Dijadwalkan">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-calendar me-2"></i>Jadwalkan
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('dashboard.warehouse.shipment.update-status', $transaction->transaksi_id) }}" 
                                                                      method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status_pengiriman" value="Sedang Dikirim">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-truck me-2"></i>Sedang Dikirim
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('dashboard.warehouse.shipment.update-status', $transaction->transaksi_id) }}" 
                                                                      method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status_pengiriman" value="Terkirim">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-check me-2"></i>Terkirim
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
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
                                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} 
                                dari {{ $transactions->total() }} transaksi
                            </div>
                            <div>
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada transaksi siap kirim</h5>
                            <p class="text-muted">Belum ada transaksi yang siap untuk dikirim atau diambil.</p>
                            <a href="{{ route('dashboard.warehouse.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
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
    // Auto refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
    
    // Confirm status updates
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const statusInput = this.querySelector('input[name="status_pengiriman"]');
            if (statusInput) {
                const status = statusInput.value;
                if (!confirm(`Apakah Anda yakin ingin mengubah status menjadi "${status}"?`)) {
                    e.preventDefault();
                }
            }
        });
    });
</script>
@endpush
