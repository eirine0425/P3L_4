@extends('layouts.dashboard')

@section('title', 'Penjadwalan Pengiriman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Penjadwalan Pengiriman</h2>
                    <p class="text-muted">Kelola jadwal pengiriman harian dan penugasan kurir</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="fas fa-plus"></i> Buat Jadwal Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Navigation -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-2" onclick="changeDate(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <input type="date" id="selectedDate" class="form-control me-2" 
                               value="{{ request('date', date('Y-m-d')) }}" 
                               onchange="loadScheduleForDate(this.value)">
                        <button class="btn btn-outline-secondary" onclick="changeDate(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary" onclick="loadScheduleForDate('{{ date('Y-m-d') }}')">
                            Hari Ini
                        </button>
                        <button class="btn btn-outline-primary" onclick="loadScheduleForDate('{{ date('Y-m-d', strtotime('+1 day')) }}')">
                            Besok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Restriction Alert -->
    <div class="alert alert-warning" id="timeRestrictionAlert" style="display: none;">
        <i class="fas fa-clock"></i>
        <strong>Perhatian:</strong> Saat ini sudah melewati jam 16:00. 
        Pengiriman untuk hari ini tidak dapat dijadwalkan lagi. Silakan jadwalkan untuk besok atau hari berikutnya.
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary" id="totalScheduled">0</h3>
                    <p class="card-text">Total Terjadwal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning" id="pendingSchedule">0</h3>
                    <p class="card-text">Menunggu Jadwal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info" id="activeCouriers">0</h3>
                    <p class="card-text">Kurir Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success" id="completedToday">0</h3>
                    <p class="card-text">Selesai Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Courier Schedule Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Jadwal Kurir - <span id="scheduleDate">{{ date('d/m/Y') }}</span></h5>
        </div>
        <div class="card-body">
            <div id="courierScheduleGrid">
                <!-- Dynamic content will be loaded here -->
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat jadwal kurir...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Schedule Modal -->
<div class="modal fade" id="createScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createScheduleForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Buat Jadwal Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="schedule_date" class="form-label">Tanggal Pengiriman</label>
                                <input type="date" name="schedule_date" id="schedule_date" 
                                       class="form-control" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="courier_id" class="form-label">Kurir</label>
                                <select name="courier_id" id="courier_id" class="form-select" required>
                                    <option value="">-- Pilih Kurir --</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Transaksi</label>
                        <div id="availableTransactions" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <!-- Transactions will be loaded dynamically -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="schedule_notes" class="form-label">Catatan</label>
                        <textarea name="schedule_notes" id="schedule_notes" rows="3" 
                                  class="form-control" placeholder="Catatan untuk jadwal pengiriman (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editScheduleForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jadwal Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_schedule_id" name="schedule_id">
                    
                    <div class="mb-3">
                        <label for="edit_schedule_date" class="form-label">Tanggal Pengiriman</label>
                        <input type="date" name="schedule_date" id="edit_schedule_date" 
                               class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_courier_id" class="form-label">Kurir</label>
                        <select name="courier_id" id="edit_courier_id" class="form-select" required>
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_schedule_notes" class="form-label">Catatan</label>
                        <textarea name="schedule_notes" id="edit_schedule_notes" rows="3" 
                                  class="form-control" placeholder="Catatan untuk jadwal pengiriman (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize
    checkTimeRestriction();
    loadCouriers();
    loadScheduleForDate(document.getElementById('selectedDate').value);
    
    // Set up form handlers
    setupFormHandlers();
    
    // Auto-refresh every 5 minutes
    setInterval(() => {
        loadScheduleForDate(document.getElementById('selectedDate').value);
    }, 300000);
});

function checkTimeRestriction() {
    const now = new Date();
    const currentHour = now.getHours();
    const alertElement = document.getElementById('timeRestrictionAlert');
    
    if (currentHour >= 16) {
        alertElement.style.display = 'block';
        
        // Update minimum date for schedule inputs
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        
        document.getElementById('schedule_date').min = tomorrowStr;
        document.getElementById('edit_schedule_date').min = tomorrowStr;
    }
}

function changeDate(days) {
    const dateInput = document.getElementById('selectedDate');
    const currentDate = new Date(dateInput.value);
    currentDate.setDate(currentDate.getDate() + days);
    
    const newDateStr = currentDate.toISOString().split('T')[0];
    dateInput.value = newDateStr;
    loadScheduleForDate(newDateStr);
}

function loadScheduleForDate(date) {
    const scheduleGrid = document.getElementById('courierScheduleGrid');
    const scheduleDateSpan = document.getElementById('scheduleDate');
    
    // Update date display
    const dateObj = new Date(date);
    scheduleDateSpan.textContent = dateObj.toLocaleDateString('id-ID');
    
    // Show loading
    scheduleGrid.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat jadwal kurir...</p>
        </div>
    `;
    
    // Fetch schedule data
    fetch(`/dashboard/warehouse/shipments/schedule-data?date=${date}`)
        .then(response => response.json())
        .then(data => {
            renderScheduleGrid(data);
            updateStatistics(data.statistics);
        })
        .catch(error => {
            console.error('Error loading schedule:', error);
            scheduleGrid.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Gagal memuat jadwal. Silakan refresh halaman.
                </div>
            `;
        });
}

function renderScheduleGrid(data) {
    const scheduleGrid = document.getElementById('courierScheduleGrid');
    
    if (data.couriers.length === 0) {
        scheduleGrid.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>Belum Ada Kurir Terjadwal</h5>
                <p class="text-muted">Klik "Buat Jadwal Baru" untuk menambah jadwal pengiriman.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row">';
    
    data.couriers.forEach(courier => {
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">${courier.name}</h6>
                            <small class="text-muted">${courier.email}</small>
                        </div>
                        <span class="badge bg-${courier.shipments.length > 0 ? 'success' : 'secondary'}">
                            ${courier.shipments.length} pengiriman
                        </span>
                    </div>
                    <div class="card-body">
                        ${courier.shipments.length > 0 ? renderCourierShipments(courier.shipments) : '<p class="text-muted text-center">Belum ada pengiriman</p>'}
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-outline-primary" onclick="assignMoreShipments(${courier.id})">
                            <i class="fas fa-plus"></i> Tambah Pengiriman
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    scheduleGrid.innerHTML = html;
}

function renderCourierShipments(shipments) {
    let html = '<div class="list-group list-group-flush">';
    
    shipments.forEach(shipment => {
        const statusClass = getStatusClass(shipment.status);
        html += `
            <div class="list-group-item px-0 py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">#${shipment.transaction_id}</h6>
                        <p class="mb-1 small">${shipment.customer_name}</p>
                        <small class="text-muted">${shipment.address}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-${statusClass}">${shipment.status}</span>
                        <div class="btn-group mt-1" role="group">
                            <button class="btn btn-sm btn-outline-secondary" onclick="editShipment(${shipment.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="viewShipment(${shipment.transaction_id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

function getStatusClass(status) {
    const statusClasses = {
        'Dijadwalkan': 'info',
        'Menunggu Pengiriman': 'warning',
        'Dalam Perjalanan': 'primary',
        'Terkirim': 'success',
        'Dibatalkan': 'danger'
    };
    return statusClasses[status] || 'secondary';
}

function updateStatistics(stats) {
    document.getElementById('totalScheduled').textContent = stats.total_scheduled || 0;
    document.getElementById('pendingSchedule').textContent = stats.pending_schedule || 0;
    document.getElementById('activeCouriers').textContent = stats.active_couriers || 0;
    document.getElementById('completedToday').textContent = stats.completed_today || 0;
}

function loadCouriers() {
    fetch('/dashboard/warehouse/shipments/couriers')
        .then(response => response.json())
        .then(couriers => {
            const courierSelects = ['courier_id', 'edit_courier_id'];
            
            courierSelects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">-- Pilih Kurir --</option>';
                
                couriers.forEach(courier => {
                    select.innerHTML += `<option value="${courier.id}">${courier.name} - ${courier.email}</option>`;
                });
            });
        })
        .catch(error => console.error('Error loading couriers:', error));
}

function loadAvailableTransactions(date) {
    const container = document.getElementById('availableTransactions');
    container.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Memuat transaksi...</div>';
    
    fetch(`/dashboard/warehouse/shipments/available-transactions?date=${date}`)
        .then(response => response.json())
        .then(transactions => {
            if (transactions.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">Tidak ada transaksi yang tersedia untuk dijadwalkan.</p>';
                return;
            }
            
            let html = '';
            transactions.forEach(transaction => {
                const orderTime = new Date(transaction.tanggal_pesan);
                const isAfter4PM = orderTime.getHours() >= 16;
                const canScheduleToday = !isAfter4PM || date !== transaction.tanggal_pesan.split(' ')[0];
                
                html += `
                    <div class="form-check mb-2 ${!canScheduleToday ? 'opacity-50' : ''}">
                        <input class="form-check-input" type="checkbox" 
                               name="transaction_ids[]" value="${transaction.transaksi_id}" 
                               id="trans_${transaction.transaksi_id}"
                               ${!canScheduleToday ? 'disabled' : ''}>
                        <label class="form-check-label" for="trans_${transaction.transaksi_id}">
                            <strong>#${transaction.transaksi_id}</strong> - ${transaction.customer_name}
                            <br>
                            <small class="text-muted">
                                Dipesan: ${new Date(transaction.tanggal_pesan).toLocaleString('id-ID')}
                                ${isAfter4PM ? '<span class="text-warning">(Setelah 16:00)</span>' : ''}
                            </small>
                            <br>
                            <small class="text-muted">Total: Rp ${parseInt(transaction.total_harga).toLocaleString('id-ID')}</small>
                        </label>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading transactions:', error);
            container.innerHTML = '<div class="alert alert-danger">Gagal memuat transaksi</div>';
        });
}

function setupFormHandlers() {
    // Create schedule form
    document.getElementById('createScheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const selectedTransactions = Array.from(document.querySelectorAll('input[name="transaction_ids[]"]:checked'))
            .map(cb => cb.value);
        
        if (selectedTransactions.length === 0) {
            alert('Pilih minimal satu transaksi untuk dijadwalkan.');
            return;
        }
        
        // Validate 4 PM rule
        const scheduleDate = formData.get('schedule_date');
        const now = new Date();
        const isToday = scheduleDate === now.toISOString().split('T')[0];
        
        if (isToday && now.getHours() >= 16) {
            alert('Pengiriman tidak bisa dijadwalkan untuk hari ini setelah jam 16:00.');
            return;
        }
        
        // Add selected transactions to form data
        selectedTransactions.forEach(id => {
            formData.append('transaction_ids[]', id);
        });
        
        fetch('/dashboard/warehouse/shipments/bulk-schedule', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('createScheduleModal')).hide();
                loadScheduleForDate(document.getElementById('selectedDate').value);
                showAlert('success', 'Jadwal pengiriman berhasil dibuat.');
            } else {
                showAlert('danger', data.message || 'Gagal membuat jadwal pengiriman.');
            }
        })
        .catch(error => {
            console.error('Error creating schedule:', error);
            showAlert('danger', 'Terjadi kesalahan saat membuat jadwal.');
        });
    });
    
    // Date change handler for create modal
    document.getElementById('schedule_date').addEventListener('change', function() {
        loadAvailableTransactions(this.value);
    });
}

function assignMoreShipments(courierId) {
    document.getElementById('courier_id').value = courierId;
    const modal = new bootstrap.Modal(document.getElementById('createScheduleModal'));
    modal.show();
    
    // Load transactions for current date
    const currentDate = document.getElementById('selectedDate').value;
    document.getElementById('schedule_date').value = currentDate;
    loadAvailableTransactions(currentDate);
}

function editShipment(shipmentId) {
    // Implementation for editing individual shipment
    fetch(`/dashboard/warehouse/shipments/${shipmentId}`)
        .then(response => response.json())
        .then(shipment => {
            // Populate edit modal with shipment data
            document.getElementById('edit_schedule_id').value = shipment.id;
            document.getElementById('edit_schedule_date').value = shipment.tanggal_kirim;
            document.getElementById('edit_courier_id').value = shipment.pengirim_id;
            document.getElementById('edit_schedule_notes').value = shipment.catatan || '';
            
            const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading shipment:', error);
            showAlert('danger', 'Gagal memuat data pengiriman.');
        });
}

function viewShipment(transactionId) {
    window.open(`/dashboard/warehouse/shipments/${transactionId}`, '_blank');
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert alert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}
</script>
@endpush
