@extends('layouts.dashboard')

@section('title', 'Manajemen Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Menunggu Konfirmasi</h6>
                            <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h6 class="card-title">Hari Ini</h6>
                            <h3 class="mb-0">{{ $stats['today'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
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
                            <h6 class="card-title">Selesai Bulan Ini</h6>
                            <h3 class="mb-0">{{ $stats['completed_this_month'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Item Diambil</h6>
                            <h3 class="mb-0">{{ $stats['total_items_picked'] ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pickup Schedules -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Jadwal Pengambilan Barang</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportSchedules()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Quick Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="confirmed">Dikonfirmasi</option>
                                <option value="completed">Selesai</option>
                                <option value="partially_completed">Sebagian Diambil</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="methodFilter">
                                <option value="">Semua Metode</option>
                                <option value="self_pickup">Ambil Sendiri</option>
                                <option value="courier_delivery">Kurir Antar</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" id="searchInput" 
                                   placeholder="Cari nama penitip atau nomor telepon...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-sm w-100" onclick="applyFilters()">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>

                    <!-- Schedules Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Jadwal</th>
                                    <th>Penitip</th>
                                    <th>Metode</th>
                                    <th>Jumlah Barang</th>
                                    <th>Kontak</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pickupSchedules as $schedule)
                                <tr>
                                    <td>
                                        <strong>{{ $schedule->scheduled_date->format('d/m/Y') }}</strong>
                                        @if($schedule->scheduled_time)
                                            <br><small class="text-muted">{{ $schedule->scheduled_time }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $schedule->penitip->user->name }}</strong>
                                        <br><small class="text-muted">ID: {{ $schedule->penitip->penitip_id }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $schedule->pickup_method_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $schedule->total_items }} item
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone"></i> {{ $schedule->contact_phone }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $schedule->status_badge_class }}">
                                            {{ $schedule->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('warehouse.pickup.detail', $schedule->id) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($schedule->status === 'confirmed')
                                                <button class="btn btn-outline-success" 
                                                        onclick="processPickup({{ $schedule->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada jadwal pengambilan saat ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($pickupSchedules->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $pickupSchedules->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Pickup Modal -->
<div class="modal fade" id="processPickupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Pengambilan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processPickupForm">
                <div class="modal-body">
                    <div id="scheduleInfo" class="alert alert-info">
                        <!-- Schedule information will be loaded here -->
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status Pengambilan <span class="text-danger">*</span></label>
                                <select class="form-select" name="pickup_status" required>
                                    <option value="">Pilih status...</option>
                                    <option value="completed">Selesai - Semua Diambil</option>
                                    <option value="partially_completed">Sebagian Diambil</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengambilan Aktual <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="actual_pickup_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Waktu Pengambilan <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="actual_pickup_time" 
                                       value="{{ date('H:i') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Tanda Terima</label>
                                <input type="text" class="form-control" name="pickup_receipt_number" 
                                       placeholder="Nomor tanda terima (opsional)">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="itemsSelectionDiv" style="display: none;">
                        <label class="form-label">Barang yang Diambil</label>
                        <div id="itemsList" class="border rounded p-3">
                            <!-- Items list will be loaded here -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Kondisi Barang</label>
                        <textarea class="form-control" name="condition_notes" rows="3" 
                                  placeholder="Catatan tentang kondisi barang saat diambil..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Staff Gudang</label>
                        <textarea class="form-control" name="warehouse_staff_notes" rows="3" 
                                  placeholder="Catatan internal untuk staff gudang..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Proses Pengambilan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentScheduleId = null;

function processPickup(scheduleId) {
    currentScheduleId = scheduleId;
    
    // Load schedule details
    $.get(`/api/warehouse/pickup-schedules/${scheduleId}`, function(response) {
        if (response.success) {
            const schedule = response.data;
            
            // Update schedule info
            $('#scheduleInfo').html(`
                <strong>Penitip:</strong> ${schedule.penitip.user.name}<br>
                <strong>Metode:</strong> ${schedule.pickup_method_text}<br>
                <strong>Tanggal Jadwal:</strong> ${schedule.formatted_scheduled_date_time}<br>
                <strong>Total Barang:</strong> ${schedule.total_items} item
            `);
            
            // Load items list
            let itemsHtml = '';
            schedule.items.forEach(item => {
                itemsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${item.barang_id}" 
                               id="item_${item.barang_id}" checked>
                        <label class="form-check-label" for="item_${item.barang_id}">
                            ${item.nama_barang} - ${item.kategori?.nama_kategori || 'Tanpa Kategori'}
                        </label>
                    </div>
                `;
            });
            $('#itemsList').html(itemsHtml);
            
            $('#processPickupModal').modal('show');
        }
    });
}

// Handle pickup status change
$('select[name="pickup_status"]').change(function() {
    const status = $(this).val();
    if (status === 'partially_completed') {
        $('#itemsSelectionDiv').show();
    } else {
        $('#itemsSelectionDiv').hide();
    }
});

// Handle form submission
$('#processPickupForm').submit(function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add selected items if partially completed
    const status = $('select[name="pickup_status"]').val();
    if (status === 'partially_completed') {
        const selectedItems = $('#itemsList input:checked').map(function() {
            return $(this).val();
        }).get();
        formData.append('picked_up_items', JSON.stringify(selectedItems));
    } else if (status === 'completed') {
        // All items for completed status
        const allItems = $('#itemsList input').map(function() {
            return $(this).val();
        }).get();
        formData.append('picked_up_items', JSON.stringify(allItems));
    }
    
    $.ajax({
        url: `/api/warehouse/pickup-schedules/${currentScheduleId}/complete`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Proses pengambilan berhasil disimpan!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Error: ' + (response?.message || 'Terjadi kesalahan sistem.'));
        }
    });
});

function applyFilters() {
    const status = $('#statusFilter').val();
    const method = $('#methodFilter').val();
    const search = $('#searchInput').val();
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (method) params.append('pickup_method', method);
    if (search) params.append('search', search);
    
    window.location.href = '{{ route("warehouse.pickup.index") }}?' + params.toString();
}

function exportSchedules() {
    const params = new URLSearchParams(window.location.search);
    window.open('{{ route("warehouse.pickup.export") }}?' + params.toString());
}

// Enter key search
$('#searchInput').keypress(function(e) {
    if (e.which === 13) {
        applyFilters();
    }
});
</script>
@endpush
