@extends('layouts.dashboard')

@section('title', 'Manajemen Pengiriman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Manajemen Pengiriman</h2>
                    <p class="text-muted">Kelola penjadwalan pengiriman dan penugasan kurir</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkScheduleModal">
                        <i class="fas fa-calendar-plus"></i> Jadwalkan Massal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                    <p class="card-text">Total Transaksi</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $stats['need_shipping'] }}</h3>
                    <p class="card-text">Perlu Dijadwalkan</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $stats['scheduled'] }}</h3>
                    <p class="card-text">Dijadwalkan</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['in_progress'] }}</h3>
                    <p class="card-text">Dalam Proses</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $stats['completed'] }}</h3>
                    <p class="card-text">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.warehouse.shipments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="ID Transaksi atau Nama Pembeli" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="need_shipping" {{ request('status') == 'need_shipping' ? 'selected' : '' }}>
                                Perlu Dijadwalkan
                            </option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                                Dijadwalkan
                            </option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                Dalam Proses
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
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
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Daftar Transaksi</h5>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>ID Transaksi</th>
                                <th>Pembeli</th>
                                <th>Tanggal Pesan</th>
                                <th>Total</th>
                                <th>Status Pengiriman</th>
                                <th>Kurir</th>
                                <th>Tanggal Kirim</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        @if(!$transaction->pengiriman)
                                            <input type="checkbox" name="transaction_ids[]" 
                                                   value="{{ $transaction->transaksi_id }}" 
                                                   class="form-check-input transaction-checkbox">
                                        @endif
                                    </td>
                                    <td>
                                        <strong>#{{ $transaction->transaksi_id }}</strong>
                                    </td>
                                    <td>
                                        {{ $transaction->pembeli->user->name ?? '-' }}
                                        <br>
                                        <small class="text-muted">{{ $transaction->pembeli->user->email ?? '-' }}</small>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d/m/Y H:i') }}
                                        @if(\Carbon\Carbon::parse($transaction->tanggal_pesan)->hour >= 16)
                                            <br><small class="text-warning">
                                                <i class="fas fa-clock"></i> Setelah 16:00
                                            </small>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($transaction->pengiriman)
                                            @php
                                                $statusClass = match($transaction->pengiriman->status_pengiriman) {
                                                    'Dijadwalkan' => 'info',
                                                    'Menunggu Pengiriman' => 'warning',
                                                    'Dalam Perjalanan' => 'primary',
                                                    'Terkirim' => 'success',
                                                    'Dibatalkan' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ $transaction->pengiriman->status_pengiriman }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Perlu Dijadwalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->pengiriman && $transaction->pengiriman->pengirim)
                                            {{ $transaction->pengiriman->pengirim->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->pengiriman)
                                            {{ \Carbon\Carbon::parse($transaction->pengiriman->tanggal_kirim)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('dashboard.warehouse.shipments.show', $transaction->transaksi_id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$transaction->pengiriman)
                                                <a href="{{ route('dashboard.warehouse.shipments.create', $transaction->transaksi_id) }}" 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </a>
                                            @endif
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
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                    <h5>Tidak ada transaksi ditemukan</h5>
                    <p class="text-muted">Belum ada transaksi yang perlu dikelola pengirimannya.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Schedule Modal -->
<div class="modal fade" id="bulkScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.warehouse.shipments.bulk-schedule') }}" method="POST" id="bulkScheduleForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Jadwalkan Pengiriman Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Transaksi Terpilih</label>
                        <div id="selectedTransactions" class="border rounded p-2 bg-light">
                            <em class="text-muted">Pilih transaksi dari tabel di atas</em>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_pengirim_id" class="form-label">Kurir <span class="text-danger">*</span></label>
                        <select name="pengirim_id" id="bulk_pengirim_id" class="form-select" required>
                            <option value="">-- Pilih Kurir --</option>
                            @foreach(\App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'kurir'); })->get() as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_tanggal_kirim" class="form-label">Tanggal Pengiriman <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_kirim" id="bulk_tanggal_kirim" 
                               class="form-control" min="{{ date('Y-m-d') }}" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> 
                            Sistem akan memvalidasi aturan jam 4 sore untuk setiap transaksi
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="bulk_catatan" rows="3" 
                                  class="form-control" placeholder="Catatan untuk semua pengiriman (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Jadwalkan Pengiriman</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const transactionCheckboxes = document.querySelectorAll('.transaction-checkbox');
    const selectedTransactionsDiv = document.getElementById('selectedTransactions');
    const bulkScheduleForm = document.getElementById('bulkScheduleForm');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        transactionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedTransactions();
    });

    // Individual checkbox change
    transactionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedTransactions();
            
            // Update select all checkbox
            const checkedCount = document.querySelectorAll('.transaction-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === transactionCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < transactionCheckboxes.length;
        });
    });

    function updateSelectedTransactions() {
        const checkedBoxes = document.querySelectorAll('.transaction-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            selectedTransactionsDiv.innerHTML = '<em class="text-muted">Pilih transaksi dari tabel di atas</em>';
        } else {
            const transactionIds = Array.from(checkedBoxes).map(cb => cb.value);
            selectedTransactionsDiv.innerHTML = `
                <strong>${checkedBoxes.length} transaksi terpilih:</strong><br>
                ${transactionIds.map(id => `#${id}`).join(', ')}
            `;
        }
    }

    // Form submission
    bulkScheduleForm.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.transaction-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu transaksi untuk dijadwalkan.');
            return;
        }

        // Add hidden inputs for selected transaction IDs
        checkedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'transaction_ids[]';
            hiddenInput.value = checkbox.value;
            this.appendChild(hiddenInput);
        });
    });

    // Date validation for 4 PM rule
    const bulkTanggalKirimInput = document.getElementById('bulk_tanggal_kirim');
    bulkTanggalKirimInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        const currentHour = today.getHours();
        
        if (selectedDate.toDateString() === today.toDateString() && currentHour >= 16) {
            alert('Peringatan: Pengiriman tidak bisa dijadwalkan untuk hari ini setelah jam 4 sore. Silakan pilih tanggal besok atau setelahnya.');
            
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            this.value = tomorrow.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
