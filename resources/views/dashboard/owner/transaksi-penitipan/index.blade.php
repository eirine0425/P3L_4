@extends('layouts.dashboard')

@section('title', 'Laporan Transaksi Penitipan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Transaksi Penitipan</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form id="filterForm" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ date('Y-m-t') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="penitip_id" class="form-label">Penitip</label>
                                <select class="form-control" id="penitip_id" name="penitip_id">
                                    <option value="">Semua Penitip</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button type="button" >
                                    <div class="table-actions">
                                        <a href="{{ route('dashboard.owner.transaksi-penitipan.print') }}" target="_blank" class="btn btn-info">🖨️ Print</a>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4" id="summaryCards">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Transaksi</h5>
                                    <h3 id="totalTransaksi">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Penitip Terlibat</h5>
                                    <h3 id="penitipTerlibat">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Nilai Barang</h5>
                                    <h3 id="totalNilai">Rp 0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Items Near Expiry</h5>
                                    <h3 id="itemsNearExpiry">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="transaksiTable">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>ID Penitip</th>
                                    <th>Nama Penitip</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Metode</th>
                                    <th>Tanggal Penitipan</th>
                                    <th>Batas Penitipan</th>
                                    <th>Sisa Hari</th>
                                </tr>
                            </thead>
                            <tbody id="transaksiTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="text-center" style="display: none;">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentData = null;

    // Load initial data
    loadData();

    // Load penitip options
    loadPenitipOptions();

    // Form submit handler
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // Reset filter
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('filterForm').reset();
        document.getElementById('start_date').value = '{{ date('Y-m-01') }}';
        document.getElementById('end_date').value = '{{ date('Y-m-t') }}';
        loadData();
    });

    // Print report
    document.getElementById('printReport').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        window.open(`{{ route('dashboard.owner.transaksi-penitipan.print') }}?${params}`, '_blank');
    });

    // Export report
    document.getElementById('exportReport').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        window.location.href = `{{ route('dashboard.owner.transaksi-penitipan.export') }}?${params}`;
    });

    function loadData() {
        showLoading(true);
        
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);

        fetch(`{{ route('dashboard.owner.transaksi-penitipan.report') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    currentData = data.data;
                    updateSummaryCards(data.data.summary);
                    updateTable(data.data.transaksi_penitipan);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data');
            })
            .finally(() => {
                showLoading(false);
            });
    }

    function loadPenitipOptions() {
        fetch(`{{ route('dashboard.owner.transaksi-penitipan.report') }}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data.all_penitip) {
                    const select = document.getElementById('penitip_id');
                    data.data.all_penitip.forEach(penitip => {
                        const option = document.createElement('option');
                        option.value = penitip.penitip_id;
                        option.textContent = `${penitip.nama || penitip.user_name} (${penitip.phone_number || ''})`;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error loading penitip options:', error));
    }

    function updateSummaryCards(summary) {
        document.getElementById('totalTransaksi').textContent = summary.total_transaksi.toLocaleString();
        document.getElementById('penitipTerlibat').textContent = summary.penitip_terlibat.toLocaleString();
        document.getElementById('totalNilai').textContent = 'Rp ' + summary.total_nilai_barang.toLocaleString();
        document.getElementById('itemsNearExpiry').textContent = summary.items_near_expiry.toLocaleString();
    }

    function updateTable(transaksi) {
        const tbody = document.getElementById('transaksiTableBody');
        tbody.innerHTML = '';

        if (transaksi.length === 0) {
            tbody.innerHTML = '<tr><td colspan="12" class="text-center">Tidak ada data</td></tr>';
            return;
        }

        transaksi.forEach(item => {
            const row = document.createElement('tr');
            
            // Add warning class for items near expiry
            if (item.sisa_hari <= 7 && item.sisa_hari >= 0 && item.status_penitipan === 'Aktif') {
                row.classList.add('table-warning');
            }
            
            row.innerHTML = `
                <td>${item.transaksi_penitipan_id}</td>
                <td>${item.id_penitip_display}</td>
                <td>${item.penitip_nama || item.user_nama || '-'}</td>
                <td>${item.kode_barang}</td>
                <td>${item.nama_barang}</td>
                <td>${item.nama_kategori || 'Tidak Dikategorikan'}</td>
                <td>Rp ${(item.barang_harga || 0).toLocaleString()}</td>
                <td><span class="badge badge-${getStatusBadgeClass(item.status_penitipan)}">${item.status_penitipan}</span></td>
                <td>${item.metode_penitipan}</td>
                <td>${formatDate(item.tanggal_penitipan)}</td>
                <td>${formatDate(item.batas_penitipan)}</td>
                <td>${item.sisa_hari} hari</td>
            `;
            tbody.appendChild(row);
        });
    }

    function getStatusBadgeClass(status) {
        switch(status) {
            case 'Aktif': return 'success';
            case 'Selesai': return 'primary';
            case 'Dibatalkan': return 'danger';
            default: return 'secondary';
        }
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID');
    }

    function printReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate
            });

            window.open(`/dashboard/owner/transaksi-penitipan/print?${params}`, '_blank');
        }

    function showLoading(show) {
        document.getElementById('loadingIndicator').style.display = show ? 'block' : 'none';
        document.getElementById('transaksiTable').style.display = show ? 'none' : 'table';
    }
});
</script>
@endsection
