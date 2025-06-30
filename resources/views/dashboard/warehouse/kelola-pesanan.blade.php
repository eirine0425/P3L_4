@extends('layouts.dashboard')

@section('title', 'Kelola Pesanan')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Pesanan</h1>
            <p class="text-muted">Daftar pesanan yang perlu diproses untuk pengiriman atau pengambilan</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('dashboard.warehouse.pickup-scheduling') }}" class="btn btn-warning">
                <i class="fas fa-calendar-alt me-2"></i>Jadwalkan Pengambilan
            </a>
            <a href="{{ route('dashboard.warehouse.shipments') }}" class="btn btn-primary">
                <i class="fas fa-shipping-fast me-2"></i>Kelola Pengiriman
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">12</h3>
                            <p class="mb-0">Menunggu Proses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">8</h3>
                            <p class="mb-0">Menunggu Pengambilan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">15</h3>
                            <p class="mb-0">Dalam Pengiriman</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-hand-paper fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">5</h3>
                            <p class="mb-0">Perlu Pengambilan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Pesanan
            </h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-outline-success" onclick="bulkSchedulePickup()">
                    <i class="fas fa-calendar-plus"></i> Jadwal Massal
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>ID Pesanan</th>
                            <th>Pembeli</th>
                            <th>Barang</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Metode</th>
                            <th>Tanggal</th>
                            <th>Jadwalkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <input type="checkbox" name="order_ids[]" value="{{ $order['id'] }}" class="order-checkbox">
                            </td>
                            <td>
                                <strong class="text-primary">#{{ $order['id'] }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $order['buyer_name'] }}</strong>
                                        <br><small class="text-muted">{{ $order['buyer_email'] }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/placeholder.svg?height=40&width=40" alt="{{ $order['item_name'] }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <strong>{{ $order['item_name'] }}</strong>
                                        <br><small class="text-muted">{{ $order['item_color'] }}</small>
                                        <br><span class="badge bg-light text-dark">1 item</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>Rp {{ number_format($order['total'], 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($order['status'] == 'Menunggu Proses')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>{{ $order['status'] }}
                                </span>
                                @elseif($order['status'] == 'Menunggu Pengambilan')
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>{{ $order['status'] }}
                                </span>
                                @elseif($order['status'] == 'Dalam Pengiriman')
                                <span class="badge bg-info">
                                    <i class="fas fa-truck me-1"></i>{{ $order['status'] }}
                                </span>
                                @endif
                            </td>
                            <td>
                                @if($order['method'] == 'Diantar')
                                <span class="badge bg-info">
                                    <i class="fas fa-truck me-1"></i>{{ $order['method'] }}
                                </span>
                                @elseif($order['method'] == 'Ambil Sendiri')
                                <span class="badge bg-warning">
                                    <i class="fas fa-hand-paper me-1"></i>{{ $order['method'] }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ date('d M Y', strtotime($order['date'])) }}</strong>
                                    <br><small class="text-muted">{{ date('H:i', strtotime($order['date'])) }}</small>
                                </div>
                            </td>
                            <td>
                                @if($order['scheduled'])
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Sudah Dijadwalkan
                                </span>
                                @elseif($order['status'] == 'Dalam Pengiriman')
                                <span class="badge bg-info">
                                    <i class="fas fa-truck me-1"></i>Dalam Pengiriman
                                </span>
                                @else
                                <button class="btn btn-primary btn-sm" onclick="scheduleDelivery('{{ $order['id'] }}')">
                                    <i class="fas fa-calendar-alt me-1"></i>Jadwalkan
                                </button>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewOrder('{{ $order['id'] }}')" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- TOMBOL KONFIRMASI PENGAMBILAN BARANG - SELALU MUNCUL -->
                                    <button class="btn btn-success" onclick="confirmItemReceived('{{ $order['id'] }}')" title="Konfirmasi Barang Telah Diterima/Diambil">
                                        <i class="fas fa-check-circle"></i> Konfirmasi
                                    </button>
                                    
                                    @if($order['status'] == 'Menunggu Proses')
                                    <button class="btn btn-outline-warning" onclick="scheduleDelivery('{{ $order['id'] }}')" title="Jadwalkan Pengiriman">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                    @elseif($order['status'] == 'Menunggu Pengambilan')
                                    <button class="btn btn-outline-info" onclick="editPickupSchedule('{{ $order['id'] }}')" title="Edit Jadwal Pengambilan">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @elseif($order['status'] == 'Dalam Pengiriman')
                                    <button class="btn btn-outline-info" onclick="trackShipment('{{ $order['id'] }}')" title="Lacak Pengiriman">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
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
                    <small class="text-muted">Menampilkan 1-{{ count($orders) }} dari {{ count($orders) }} pesanan</small>
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                        <li class="page-item active">
                            <span class="page-link">1</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Quick Schedule Pickup Modal -->
<div class="modal fade" id="quickSchedulePickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>Jadwalkan Pengambilan Cepat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickScheduleForm">
                <div class="modal-body">
                    <input type="hidden" id="quickOrderId" name="order_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pengambilan <span class="text-danger">*</span></label>
                        <input type="date" name="scheduled_date" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Waktu Pengambilan <span class="text-danger">*</span></label>
                        <select name="scheduled_time" class="form-select" required>
                            <option value="">Pilih waktu...</option>
                            <option value="08:00">08:00 - 09:00</option>
                            <option value="09:00">09:00 - 10:00</option>
                            <option value="10:00">10:00 - 11:00</option>
                            <option value="11:00">11:00 - 12:00</option>
                            <option value="13:00">13:00 - 14:00</option>
                            <option value="14:00">14:00 - 15:00</option>
                            <option value="15:00">15:00 - 16:00</option>
                            <option value="16:00">16:00 - 17:00</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Metode Pengambilan <span class="text-danger">*</span></label>
                        <select name="pickup_method" class="form-select" required>
                            <option value="">Pilih metode...</option>
                            <option value="self_pickup">Ambil Sendiri</option>
                            <option value="courier_delivery">Kirim via Kurir</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nomor Kontak <span class="text-danger">*</span></label>
                        <input type="tel" name="contact_phone" class="form-control" required placeholder="08xxxxxxxxxx">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan untuk pengambilan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-plus me-1"></i>Jadwalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Schedule Modal -->
<div class="modal fade" id="bulkScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>Jadwalkan Pengambilan Massal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkScheduleForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="selectedOrdersCount">0</span> pesanan dipilih untuk dijadwalkan
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pengambilan <span class="text-danger">*</span></label>
                        <input type="date" name="bulk_scheduled_date" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Metode Pengambilan <span class="text-danger">*</span></label>
                        <select name="bulk_pickup_method" class="form-select" required>
                            <option value="">Pilih metode...</option>
                            <option value="self_pickup">Ambil Sendiri</option>
                            <option value="courier_delivery">Kirim via Kurir</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="bulk_notes" class="form-control" rows="3" placeholder="Catatan untuk semua jadwal pengambilan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-plus me-1"></i>Jadwalkan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Schedule Delivery Modal -->
<div class="modal fade" id="scheduleDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt me-2"></i>Jadwalkan Pengiriman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleDeliveryForm" action="{{ route('dashboard.warehouse.kelola-pesanan.schedule') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="deliveryOrderId" name="order_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pengiriman <span class="text-danger">*</span></label>
                            <input type="date" name="delivery_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimasi Waktu <span class="text-danger">*</span></label>
                            <select name="delivery_time" class="form-select" required>
                                <option value="">Pilih waktu...</option>
                                <option value="08:00-10:00">08:00 - 10:00</option>
                                <option value="10:00-12:00">10:00 - 12:00</option>
                                <option value="12:00-14:00">12:00 - 14:00</option>
                                <option value="14:00-16:00">14:00 - 16:00</option>
                                <option value="16:00-18:00">16:00 - 18:00</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kurir <span class="text-danger">*</span></label>
                            <select name="courier_id" class="form-select" required>
                                <option value="">Pilih kurir...</option>
                                <option value="1">Ahmad - Kurir Internal</option>
                                <option value="2">Budi - Kurir Internal</option>
                                <option value="3">Charlie - Kurir Internal</option>
                                <option value="4">JNE</option>
                                <option value="5">J&T Express</option>
                                <option value="6">SiCepat</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Resi</label>
                            <input type="text" name="tracking_number" class="form-control" placeholder="Opsional untuk kurir eksternal">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                        <textarea name="delivery_address" class="form-control" rows="3" required id="deliveryAddress"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Pengiriman</label>
                        <textarea name="delivery_notes" class="form-control" rows="2" placeholder="Catatan tambahan untuk pengiriman..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Pastikan barang sudah dikemas dengan baik sebelum dijadwalkan untuk pengiriman.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check me-1"></i>Jadwalkan Pengiriman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables
let selectedOrders = [];

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedOrders();
}

// Update selected orders array
function updateSelectedOrders() {
    selectedOrders = [];
    const checkboxes = document.querySelectorAll('.order-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        selectedOrders.push(checkbox.value);
    });
    
    document.getElementById('selectedOrdersCount').textContent = selectedOrders.length;
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedOrders);
    });
});

// View order details
function viewOrder(orderId) {
    // Redirect to order detail page or open modal
    window.location.href = `/dashboard/warehouse/orders/${orderId}`;
}

// Quick schedule pickup for single order
function quickSchedulePickup(orderId) {
    document.getElementById('quickOrderId').value = orderId;
    const modal = new bootstrap.Modal(document.getElementById('quickSchedulePickupModal'));
    modal.show();
}

// Handle quick schedule form submission
document.getElementById('quickScheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const orderId = formData.get('order_id');
    
    // Simulate API call
    setTimeout(() => {
        alert(`Jadwal pengambilan untuk pesanan ${orderId} berhasil dibuat!`);
        bootstrap.Modal.getInstance(document.getElementById('quickSchedulePickupModal')).hide();
        refreshOrders();
    }, 1000);
});

// Bulk schedule pickup
function bulkSchedulePickup() {
    if (selectedOrders.length === 0) {
        alert('Pilih minimal satu pesanan untuk dijadwalkan');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('bulkScheduleModal'));
    modal.show();
}

// Handle bulk schedule form submission
document.getElementById('bulkScheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Simulate API call
    setTimeout(() => {
        alert(`${selectedOrders.length} pesanan berhasil dijadwalkan untuk pengambilan!`);
        bootstrap.Modal.getInstance(document.getElementById('bulkScheduleModal')).hide();
        
        // Clear selections
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
        updateSelectedOrders();
        
        refreshOrders();
    }, 1000);
});

// Schedule shipping
function scheduleShipping(orderId) {
    if (confirm(`Jadwalkan pengiriman untuk pesanan ${orderId}?`)) {
        // Redirect to shipping schedule page
        window.location.href = `/dashboard/warehouse/shipments/create/${orderId}`;
    }
}

// Confirm pickup
function confirmPickup(orderId) {
    if (confirm(`Konfirmasi bahwa pesanan ${orderId} telah diambil?`)) {
        // Simulate API call
        setTimeout(() => {
            alert(`Pengambilan pesanan ${orderId} berhasil dikonfirmasi!`);
            refreshOrders();
        }, 500);
    }
}

// Edit pickup schedule
function editPickupSchedule(orderId) {
    // Redirect to edit schedule page
    window.location.href = `/dashboard/warehouse/pickup-scheduling/edit/${orderId}`;
}

// Track shipment
function trackShipment(orderId) {
    // Open tracking modal or redirect to tracking page
    window.open(`/track/${orderId}`, '_blank');
}

// Confirm delivery
function confirmDelivery(orderId) {
    if (confirm(`Konfirmasi bahwa pesanan ${orderId} telah terkirim?`)) {
        // Simulate API call
        setTimeout(() => {
            alert(`Pengiriman pesanan ${orderId} berhasil dikonfirmasi!`);
            refreshOrders();
        }, 500);
    }
}

// Refresh orders
function refreshOrders() {
    // Simulate page refresh or AJAX reload
    location.reload();
}

// Schedule delivery for single order
function scheduleDelivery(orderId) {
    document.getElementById('deliveryOrderId').value = orderId;
    
    // Populate address based on order ID (in real app, you'd fetch this from database)
    let address = '';
    if (orderId === 'TRX001') {
        address = 'Jl. Merdeka No. 123, Jakarta Selatan\nKelurahan Cilandak, Kecamatan Cilandak\nDKI Jakarta, 12345';
    } else if (orderId === 'TRX002') {
        address = 'Jl. Pahlawan No. 45, Bandung\nKelurahan Dago, Kecamatan Coblong\nJawa Barat, 40135';
    } else if (orderId === 'TRX003') {
        address = 'Jl. Diponegoro No. 78, Surabaya\nKelurahan Darmo, Kecamatan Wonokromo\nJawa Timur, 60241';
    }
    
    document.getElementById('deliveryAddress').value = address;
    
    const modal = new bootstrap.Modal(document.getElementById('scheduleDeliveryModal'));
    modal.show();
}

// Konfirmasi barang telah diterima/diambil
function confirmItemReceived(orderId) {
    Swal.fire({
        title: 'Konfirmasi Pengambilan Barang',
        text: `Apakah Anda yakin barang untuk pesanan ${orderId} telah diterima/diambil oleh penitip?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Sudah Diterima',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kirim request ke server
            fetch(`/dashboard/warehouse/confirm-received/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    confirmed_by: 'pegawai_gudang',
                    confirmation_time: new Date().toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Pengambilan barang untuk pesanan ${orderId} telah dikonfirmasi.`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Refresh halaman setelah 2 detik
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat mengkonfirmasi pengambilan.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem.',
                    icon: 'error'
                });
            });
        }
    });
}

// Auto-refresh every 5 minutes (300000 ms)
setInterval(refreshOrders, 300000);
</script>
@endpush
