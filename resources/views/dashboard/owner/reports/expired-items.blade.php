@extends('layouts.dashboard')

@section('title', 'Laporan Barang Kadaluarsa')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Laporan Barang Masa Penitipan Habis
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form id="filterForm" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                            <div class="col-md-3">
                                <label for="penitip_id" class="form-label">Penitip</label>
                                <select class="form-control" id="penitip_id" name="penitip_id">
                                    <option value="">Semua Penitip</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select class="form-control" id="kategori_id" name="kategori_id">
                                    <option value="">Semua Kategori</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" id="filterBtn" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter Data
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="button" id="downloadPdfBtn" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4" id="summaryCards" style="display: none;">
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 id="totalItems">0</h4>
                                            <p class="mb-0">Total Barang Kadaluarsa</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 id="totalValue">Rp 0</h4>
                                            <p class="mb-0">Total Nilai Barang</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                                            <h4 id="avgDays">0 Hari</h4>
                                            <p class="mb-0">Rata-rata Hari Kadaluarsa</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-times fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="expiredItemsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Penitip</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Kondisi</th>
                                    <th>Batas Penitipan</th>
                                    <th>Hari Lewat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let dataTable;
    
    // Initialize DataTable
    function initDataTable() {
        if (dataTable) {
            dataTable.destroy();
        }
        
        dataTable = $('#expiredItemsTable').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            },
            columnDefs: [
                { targets: [4], className: 'text-right' },
                { targets: [5, 6, 7, 8], className: 'text-center' }
            ]
        });
    }

    // Load filter options
    function loadFilterOptions() {
        $.ajax({
            url: '/api/reports/expired-items/filters',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            },
            success: function(response) {
                if (response.success) {
                    // Populate penitip dropdown
                    const penitipSelect = $('#penitip_id');
                    penitipSelect.empty().append('<option value="">Semua Penitip</option>');
                    response.data.penitips.forEach(function(penitip) {
                        penitipSelect.append(`<option value="${penitip.id}">${penitip.name}</option>`);
                    });

                    // Populate category dropdown
                    const categorySelect = $('#kategori_id');
                    categorySelect.empty().append('<option value="">Semua Kategori</option>');
                    response.data.categories.forEach(function(category) {
                        categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading filter options:', error);
                console.log('Response:', xhr.responseText);
            }
        });
    }

    // Load data
    function loadData() {
        const formData = $('#filterForm').serialize();
        
        // Show loading
        $('#expiredItemsTable tbody').html('<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
        
        $.ajax({
            url: '/api/reports/expired-items/data?' + formData,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            },
            success: function(response) {
                console.log('Data loaded successfully:', response);
                
                if (response.success) {
                    // Update summary cards
                    $('#totalItems').text(response.summary.total_expired_items.toLocaleString());
                    $('#totalValue').text('Rp ' + response.summary.total_value.toLocaleString());
                    $('#avgDays').text(response.summary.average_days_expired + ' Hari');
                    $('#summaryCards').show();

                    // Clear and populate table
                    dataTable.clear();
                    
                    if (response.data.data && response.data.data.length > 0) {
                        response.data.data.forEach(function(item, index) {
                            const kondisiBadge = getKondisiBadge(item.kondisi);
                            const statusBadge = getStatusBadge(item.status);
                            const batasPenitipan = new Date(item.batas_penitipan).toLocaleDateString('id-ID');
                            const hariLewat = Math.abs(item.sisa_hari);
                            
                            dataTable.row.add([
                                index + 1,
                                `<strong>${item.nama_barang}</strong>${item.deskripsi ? '<br><small class="text-muted">' + item.deskripsi.substring(0, 50) + '...</small>' : ''}`,
                                `${item.penitip?.nama || item.penitip?.user?.name || 'N/A'}${item.penitip?.user?.email ? '<br><small class="text-muted">' + item.penitip.user.email + '</small>' : ''}`,
                                item.kategori_barang?.nama_kategori || 'N/A',
                                'Rp ' + item.harga.toLocaleString(),
                                kondisiBadge,
                                batasPenitipan,
                                `<span class="text-danger font-weight-bold">${hariLewat} hari</span>`,
                                statusBadge
                            ]);
                        });
                    } else {
                        dataTable.row.add([
                            '',
                            'Tidak ada data barang kadaluarsa',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            ''
                        ]);
                    }
                    
                    dataTable.draw();
                } else {
                    alert('Gagal memuat data: ' + response.message);
                    $('#expiredItemsTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Gagal memuat data</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading data:', error);
                console.log('Status:', status);
                console.log('Response:', xhr.responseText);
                
                let errorMessage = 'Gagal memuat data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert('Error: ' + errorMessage);
                $('#expiredItemsTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + errorMessage + '</td></tr>');
            }
        });
    }

    // Helper functions for badges
    function getKondisiBadge(kondisi) {
        const badges = {
            'baru': '<span class="badge badge-primary">Baru</span>',
            'bekas': '<span class="badge badge-warning">Bekas</span>',
            'rusak': '<span class="badge badge-danger">Rusak</span>'
        };
        return badges[kondisi] || '<span class="badge badge-secondary">' + kondisi + '</span>';
    }

    function getStatusBadge(status) {
        const badges = {
            'belum_terjual': '<span class="badge badge-warning">Belum Terjual</span>',
            'terjual': '<span class="badge badge-success">Terjual</span>',
            'diambil_kembali': '<span class="badge badge-secondary">Diambil Kembali</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status.replace('_', ' ') + '</span>';
    }

    // Event handlers
    $('#filterBtn').click(function() {
        loadData();
    });

    $('#resetBtn').click(function() {
        $('#filterForm')[0].reset();
        $('#summaryCards').hide();
        dataTable.clear().draw();
    });

    // Replace the downloadPdfBtn click handler with this improved version
    $('#downloadPdfBtn').click(function() {
        const formData = $('#filterForm').serialize();
        
        // Create a form and submit it directly to open in a new tab
        // This preserves the session cookie which contains authentication
        const form = $('<form>', {
            'method': 'GET',
            'action': '/api/reports/expired-items/pdf',
            'target': '_blank'
        });
        
        // Add all form fields from the filter form
        const filterParams = $('#filterForm').serializeArray();
        $.each(filterParams, function(i, field) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': field.name,
                'value': field.value
            }));
        });
        
        // Add CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));
        
        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();
    });

    // Initialize
    initDataTable();
    loadFilterOptions();
    
    // Auto load data on page load
    setTimeout(function() {
        loadData();
    }, 1000);
});
</script>
@endsection
