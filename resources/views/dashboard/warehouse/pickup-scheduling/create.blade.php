@extends('layouts.dashboard')

@section('title', 'Buat Jadwal Pengambilan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Buat Jadwal Pengambilan Barang</h1>
            <p class="text-muted">Jadwalkan pengambilan barang untuk penitip</p>
        </div>
        <a href="{{ route('warehouse.pickup.scheduling') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Jadwal Pengambilan</h5>
                </div>
                <div class="card-body">
                    <form id="pickupScheduleForm" method="POST" action="{{ route('warehouse.pickup.schedule.store') }}">
                        @csrf
                        
                        <!-- Step 1: Select Penitip -->
                        <div class="step-section" id="step1">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user"></i> Langkah 1: Pilih Penitip
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Penitip <span class="text-danger">*</span></label>
                                <select name="penitip_id" id="penitipSelect" class="form-select" required>
                                    <option value="">Pilih Penitip...</option>
                                    @foreach($penitips as $penitip)
                                        <option value="{{ $penitip->penitip_id }}" 
                                                data-email="{{ $penitip->user->email }}"
                                                data-phone="{{ $penitip->user->phone ?? '' }}"
                                                data-items="{{ $penitip->barang_count }}">
                                            {{ $penitip->user->name }} 
                                            ({{ $penitip->barang_count }} barang tersedia)
                                        </option>
                                    @endforeach
                                </select>
                                @error('penitip_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="penitipInfo" class="alert alert-info" style="display: none;">
                                <h6>Informasi Penitip:</h6>
                                <div id="penitipDetails"></div>
                            </div>
                        </div>

                        <!-- Step 2: Select Items -->
                        <div class="step-section" id="step2" style="display: none;">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-boxes"></i> Langkah 2: Pilih Barang
                            </h6>
                            
                            <div id="itemsContainer">
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="text-muted mt-2">Memuat daftar barang...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Schedule Details -->
                        <div class="step-section" id="step3" style="display: none;">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-calendar"></i> Langkah 3: Detail Jadwal
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Pengambilan <span class="text-danger">*</span></label>
                                        <input type="date" name="scheduled_date" id="scheduledDate" 
                                               class="form-control" min="{{ date('Y-m-d') }}" required>
                                        <div class="form-text">Pilih tanggal pengambilan (hari kerja saja)</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Waktu Pengambilan <span class="text-danger">*</span></label>
                                        <select name="scheduled_time" id="scheduledTime" class="form-select" required>
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
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Metode Pengambilan <span class="text-danger">*</span></label>
                                        <select name="pickup_method" class="form-select" required>
                                            <option value="self_pickup">Diambil Sendiri</option>
                                            <option value="courier_delivery">Kirim via Kurir</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nomor Kontak <span class="text-danger">*</span></label>
                                        <input type="text" name="contact_phone" id="contactPhone" 
                                               class="form-control" placeholder="Nomor telepon yang bisa dihubungi" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Pengambilan</label>
                                <textarea name="pickup_address" class="form-control" rows="3" 
                                          placeholder="Alamat lengkap untuk pengambilan (opsional untuk diambil sendiri)"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="Catatan tambahan untuk pengambilan..."></textarea>
                            </div>

                            <div id="validationMessages" class="alert alert-warning" style="display: none;">
                                <h6>Peringatan:</h6>
                                <ul id="validationList"></ul>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-outline-secondary" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Sebelumnya
                            </button>
                            <div class="ms-auto">
                                <button type="button" id="nextBtn" class="btn btn-primary" disabled>
                                    Selanjutnya <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-success" style="display: none;">
                                    <i class="fas fa-save"></i> Buat Jadwal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ringkasan Jadwal</h6>
                </div>
                <div class="card-body">
                    <div id="summaryContent">
                        <p class="text-muted">Silakan lengkapi form untuk melihat ringkasan.</p>
                    </div>
                </div>
            </div>

            <!-- Guidelines Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Panduan Penjadwalan</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-clock text-primary"></i>
                            Jam operasional: 08:00 - 17:00
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar text-primary"></i>
                            Hanya hari kerja (Senin - Jumat)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-limit text-primary"></i>
                            Maksimal 5 jadwal per hari
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-bell text-primary"></i>
                            Notifikasi otomatis ke penitip
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone text-primary"></i>
                            Konfirmasi via telepon direkomendasikan
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
let selectedItems = [];

$(document).ready(function() {
    updateStepVisibility();
    
    $('#penitipSelect').change(function() {
        const penitipId = $(this).val();
        if (penitipId) {
            loadPenitipInfo(penitipId);
            loadPenitipItems(penitipId);
        } else {
            $('#penitipInfo').hide();
            $('#step2').hide();
            $('#step3').hide();
            currentStep = 1;
            updateStepVisibility();
        }
    });

    $('#scheduledDate').change(function() {
        validateScheduleDateTime();
    });

    $('#scheduledTime').change(function() {
        validateScheduleDateTime();
    });

    $('#nextBtn').click(function() {
        if (validateCurrentStep()) {
            currentStep++;
            updateStepVisibility();
            updateSummary();
        }
    });

    $('#prevBtn').click(function() {
        currentStep--;
        updateStepVisibility();
    });

    $('#pickupScheduleForm').submit(function(e) {
        if (selectedItems.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 barang untuk dijadwalkan pengambilan.');
            return;
        }

        selectedItems.forEach(function(itemId) {
            $('<input>').attr({
                type: 'hidden',
                name: 'barang_ids[]',
                value: itemId
            }).appendTo('#pickupScheduleForm');
        });
    });
});

function loadPenitipInfo(penitipId) {
    const option = $(`#penitipSelect option[value="${penitipId}"]`);
    const email = option.data('email');
    const phone = option.data('phone');
    const itemCount = option.data('items');
    
    $('#penitipDetails').html(`
        <div class="row">
            <div class="col-sm-6">
                <strong>Email:</strong><br>
                <span class="text-muted">${email}</span>
            </div>
            <div class="col-sm-6">
                <strong>Telepon:</strong><br>
                <span class="text-muted">${phone || 'Tidak tersedia'}</span>
            </div>
        </div>
        <div class="mt-2">
            <strong>Barang Tersedia:</strong> ${itemCount} item
        </div>
    `);
    
    $('#penitipInfo').show();
    $('#contactPhone').val(phone || '');
}

function loadPenitipItems(penitipId) {
    $('#itemsContainer').html(`
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
            <p class="text-muted mt-2">Memuat daftar barang...</p>
        </div>
    `);

    $.get(`{{ route('warehouse.api.penitip.items', '') }}/${penitipId}`)
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                displayItems(response.data);
                $('#nextBtn').prop('disabled', false);
            } else {
                $('#itemsContainer').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Tidak ada barang yang tersedia untuk pengambilan.
                    </div>
                `);
            }
        })
        .fail(function() {
            $('#itemsContainer').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    Gagal memuat daftar barang. Silakan coba lagi.
                </div>
            `);
        });
}

function displayItems(items) {
    let html = `
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label fw-bold" for="selectAll">
                    Pilih Semua Barang
                </label>
            </div>
        </div>
        <div class="row">
    `;

    items.forEach(function(item) {
        const isExpired = item.is_expired;
        const statusClass = isExpired ? 'border-danger' : 'border-warning';
        const statusText = isExpired ? 'Kadaluarsa' : `${Math.abs(item.sisa_hari)} hari lagi`;
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card ${statusClass}">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" 
                                   value="${item.barang_id}" id="item_${item.barang_id}">
                            <label class="form-check-label" for="item_${item.barang_id}">
                                <strong>${item.nama_barang}</strong>
                            </label>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-tag"></i> ${item.kategori}<br>
                                <i class="fas fa-money-bill"></i> Rp ${parseInt(item.harga).toLocaleString()}<br>
                                <i class="fas fa-calendar"></i> ${statusText}
                                ${isExpired ? '<span class="text-danger">(Expired)</span>' : ''}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    $('#itemsContainer').html(html);

    $('#selectAll').change(function() {
        $('.item-checkbox').prop('checked', this.checked);
        updateSelectedItems();
    });

    $('.item-checkbox').change(function() {
        updateSelectedItems();
        const totalItems = $('.item-checkbox').length;
        const checkedItems = $('.item-checkbox:checked').length;
        $('#selectAll').prop('checked', totalItems === checkedItems);
    });
}

function updateSelectedItems() {
    selectedItems = $('.item-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    updateSummary();
}

function validateCurrentStep() {
    switch(currentStep) {
        case 1:
            return $('#penitipSelect').val() !== '';
        case 2:
            if (selectedItems.length === 0) {
                alert('Silakan pilih minimal 1 barang untuk dijadwalkan pengambilan.');
                return false;
            }
            return true;
        case 3:
            return validateScheduleForm();
        default:
            return true;
    }
}

function validateScheduleForm() {
    const date = $('#scheduledDate').val();
    const time = $('#scheduledTime').val();
    const phone = $('#contactPhone').val();
    
    if (!date || !time || !phone) {
        alert('Silakan lengkapi semua field yang wajib diisi.');
        return false;
    }
    
    return true;
}

function validateScheduleDateTime() {
    const date = $('#scheduledDate').val();
    const time = $('#scheduledTime').val();
    
    if (!date || !time) return;
    
    $.post('{{ route("warehouse.api.pickup.validate") }}', {
        scheduled_date: date,
        scheduled_time: time,
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (!response.valid) {
            $('#validationMessages').show();
            $('#validationList').html(response.errors.map(error => `<li>${error}</li>`).join(''));
        } else {
            $('#validationMessages').hide();
        }
    });
}

function updateStepVisibility() {
    $('.step-section').hide();
    $(`#step${currentStep}`).show();
    
    $('#prevBtn').toggle(currentStep > 1);
    $('#nextBtn').toggle(currentStep < 3);
    $('#submitBtn').toggle(currentStep === 3);
    
    if (currentStep === 2) {
        $('#nextBtn').prop('disabled', selectedItems.length === 0);
    }
}

function updateSummary() {
    if (currentStep === 1) {
        $('#summaryContent').html('<p class="text-muted">Silakan pilih penitip terlebih dahulu.</p>');
        return;
    }
    
    let html = '';
    
    const penitipName = $('#penitipSelect option:selected').text();
    html += `<div class="mb-3">
        <h6>Penitip:</h6>
        <p class="mb-0">${penitipName}</p>
    </div>`;
    
    if (selectedItems.length > 0) {
        html += `<div class="mb-3">
            <h6>Barang Dipilih:</h6>
            <p class="mb-0">${selectedItems.length} item</p>
        </div>`;
    }
    
    if (currentStep === 3) {
        const date = $('#scheduledDate').val();
        const time = $('#scheduledTime').val();
        const method = $('select[name="pickup_method"] option:selected').text();
        
        if (date) {
            html += `<div class="mb-3">
                <h6>Jadwal:</h6>
                <p class="mb-0">${new Date(date).toLocaleDateString('id-ID')}</p>
                ${time ? `<p class="mb-0">${time}</p>` : ''}
            </div>`;
        }
        
        if (method) {
            html += `<div class="mb-3">
                <h6>Metode:</h6>
                <p class="mb-0">${method}</p>
            </div>`;
        }
    }
    
    $('#summaryContent').html(html || '<p class="text-muted">Silakan lengkapi form untuk melihat ringkasan.</p>');
}
</script>
@endpush
