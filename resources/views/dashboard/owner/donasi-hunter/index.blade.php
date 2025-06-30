@extends('layouts.dashboard')

@section('title', 'Laporan Donasi Hunter')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Laporan Donasi Hunter</h2>
            <p class="text-muted">Laporan donasi barang</p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label for="startDate" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                        <div class="col-md-4">
                            <label for="endDate" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                        <div class="col-md-4">
                            <label for="hunterId" class="form-label">Hunter</label>
                            <select class="form-control" id="hunterId" name="hunter_id">
                                <option value="">Semua Hunter</option>
                                <!-- Hunter options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-info" id="printReport">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading -->
    <div id="loadingIndicator" class="text-center py-5">
        <div class="spinner-border" role="status"></div>
        <p class="mt-2">Memuat data...</p>
    </div>
    
    <!-- Report Content -->
    <div id="reportContent" style="display: none;">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 id="totalDonasi">0</h4>
                        <p class="mb-0">Total Donasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 id="totalNilaiDonasi">Rp 0</h4>
                        <p class="mb-0">Total Nilai Donasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 id="hunterTerlibat">0</h4>
                        <p class="mb-0">Hunter Terlibat</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h4 id="organisasiTerlibat">0</h4>
                        <p class="mb-0">Organisasi Terlibat</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hunter Performance -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Performa Hunter</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Hunter</th>
                                    <th>Nama Hunter</th>
                                    <th>Telepon</th>
                                    <th>Total Donasi</th>
                                    <th>Total Nilai</th>
                                    <th>Rata-rata Nilai</th>
                                </tr>
                            </thead>
                            <tbody id="hunterPerformanceTable">
                                <tr><td colspan="6" class="text-center">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Donation List -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Daftar Donasi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Nilai</th>
                                    <th>Hunter</th>
                                    <th>Organisasi</th>
                                    <th>Tanggal Donasi</th>
                                </tr>
                            </thead>
                            <tbody id="donasiTable">
                                <tr><td colspan="7" class="text-center">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error -->
    <div id="errorDisplay" style="display: none;" class="alert alert-danger">
        <h5>Error</h5>
        <p id="errorMessage"></p>
        <button class="btn btn-danger" onclick="loadReportData()">Retry</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set default date range (current month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    $('#startDate').val(formatDateForInput(firstDay));
    $('#endDate').val(formatDateForInput(lastDay));
    
    // Load initial data
    loadReportData();
    
    // Form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadReportData();
    });
    
    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#startDate').val(formatDateForInput(firstDay));
        $('#endDate').val(formatDateForInput(lastDay));
        $('#hunterId').val('');
        loadReportData();
    });
    
    // Export report
    $('#exportReport').on('click', function() {
        exportReport();
    });
    
    // Print report
    $('#printReport').on('click', function() {
        printReport();
    });
});

function loadReportData() {
    $('#loadingIndicator').show();
    $('#reportContent').hide();
    $('#errorDisplay').hide();
    
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const hunterId = $('#hunterId').val();
    
    $.ajax({
        url: '/api/dashboard/owner/donasi-hunter-report',
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            hunter_id: hunterId
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.status === 'success') {
                updateSummary(response.data.summary);
                updateHunterPerformance(response.data.hunter_performance);
                updateDonasiTable(response.data.donasi_hunter);
                populateHunterDropdown(response.data.all_hunters);
                
                $('#loadingIndicator').hide();
                $('#reportContent').show();
            } else {
                showError('Invalid response format');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            showError('Failed to load report data');
        }
    });
}

function updateSummary(summary) {
    $('#totalDonasi').text(summary.total_donasi.toLocaleString());
    $('#totalNilaiDonasi').text(formatCurrency(summary.total_nilai_donasi));
    $('#hunterTerlibat').text(summary.hunter_terlibat.toLocaleString());
    $('#organisasiTerlibat').text(summary.organisasi_terlibat.toLocaleString());
}

function updateHunterPerformance(hunterPerformance) {
    let html = '';
    
    if (hunterPerformance && hunterPerformance.length > 0) {
        hunterPerformance.forEach(hunter => {
            html += `
                <tr>
                    <td>${hunter.kode_hunter || '-'}</td>
                    <td>${hunter.hunter_nama || '-'}</td>
                    <td>${hunter.hunter_telp || '-'}</td>
                    <td>${hunter.total_donasi.toLocaleString()}</td>
                    <td>${formatCurrency(hunter.total_nilai)}</td>
                    <td>${formatCurrency(hunter.avg_nilai)}</td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
    }
    
    $('#hunterPerformanceTable').html(html);
}

function updateDonasiTable(donasiList) {
    let html = '';
    
    if (donasiList && donasiList.length > 0) {
        donasiList.forEach(donasi => {
            html += `
                <tr>
                    <td>${donasi.kode_barang || '-'}</td>
                    <td>${donasi.nama_barang || '-'}</td>
                    <td>${donasi.nama_kategori || 'Tidak Dikategorikan'}</td>
                    <td>${formatCurrency(donasi.harga)}</td>
                    <td>${donasi.hunter_nama || donasi.hunter_user_nama || '-'}</td>
                    <td>${donasi.nama_organisasi || '-'}</td>
                    <td>${formatDate(donasi.tanggal_donasi)}</td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>';
    }
    
    $('#donasiTable').html(html);
}

function populateHunterDropdown(hunters) {
    let options = '<option value="">Semua Hunter</option>';
    
    if (hunters && hunters.length > 0) {
        hunters.forEach(hunter => {
            options += `<option value="${hunter.pegawai_id}">${hunter.nama || hunter.user_nama}</option>`;
        });
    }
    
    $('#hunterId').html(options);
}

function showError(message) {
    $('#loadingIndicator').hide();
    $('#reportContent').hide();
    $('#errorMessage').text(message);
    $('#errorDisplay').show();
}

function exportReport() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const hunterId = $('#hunterId').val();
    
    let url = `/api/dashboard/owner/donasi-hunter-export?start_date=${startDate}&end_date=${endDate}`;
    if (hunterId) {
        url += `&hunter_id=${hunterId}`;
    }
    
    window.open(url, '_blank');
}

function printReport() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const hunterId = $('#hunterId').val();
    
    let url = `/dashboard/owner/donasi-hunter/print?start_date=${startDate}&end_date=${endDate}`;
    if (hunterId) {
        url += `&hunter_id=${hunterId}`;
    }
    
    window.open(url, '_blank');
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount || 0);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID');
}

function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>
@endpush
