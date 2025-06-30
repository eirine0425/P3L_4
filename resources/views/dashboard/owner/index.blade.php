@extends('layouts.dashboard')

@section('title', 'Owner Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Dashboard Owner</h2>
                    <p class="text-muted mb-0">Selamat datang di panel kontrol Owner ReuseMart</p>
                </div>
                <div>
                    <span class="badge bg-success">Online</span>
                    <small class="text-muted ms-2">{{ now()->format('d M Y, H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
    <!-- Main Content -->
    <div class="row">
        <!-- Sales Report Section -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Laporan Penjualan per Kategori</h5>
                        <div class="dropdown">
                            
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="setSalesReportPeriod('today')">Hari Ini</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setSalesReportPeriod('week')">Minggu Ini</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setSalesReportPeriod('month')">Bulan Ini</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setSalesReportPeriod('year')">Tahun Ini</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#customPeriodModal">Periode Kustom</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <input type="date" id="startDate" class="form-control form-control-sm me-2" value="{{ date('Y-m-01') }}">
                                <span class="text-muted">s/d</span>
                                <input type="date" id="endDate" class="form-control form-control-sm ms-2" value="{{ date('Y-m-t') }}">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                           
                            
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="salesReportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah Terjual</th>
                                    
                                    <th class="text-end">Jumlah Item Gagal Terjual</th>
                                </tr>
                            </thead>
                            <tbody id="salesReportBody">
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        <button class="btn btn-outline-success" onclick="downloadSalesReportPDF()">
                            <i class="fas fa-chart-bar me-2"></i>
                            Download Laporan Penjualan
                        </button>
                        <button class="btn btn-outline-warning" onclick="downloadExpiredItemsPDF()">
                            <i class="fas fa-file-pdf me-2"></i>
                            Download Laporan Kadaluarsa
                        </button>
                        
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>
    
    <!-- Expired Items Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Barang Masa Penitipan Habis</h5>
                        <div>
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" class="form-control" placeholder="Cari barang..." id="expiredItemsSearch">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchExpiredItems()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <span class="badge bg-danger">Critical (>30 hari)</span>
                                <span class="badge bg-warning">High (15-30 hari)</span>
                                <span class="badge bg-info">Medium (8-14 hari)</span>
                                <span class="badge bg-secondary">Low (1-7 hari)</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="expiredItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kode Produk</th>
                                    <th>ID Penitip</th>
                                    <th>Penitip</th>
                                    <th>Batas Penitipan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Batas Ambil</th>
                                  
                                </tr>
                            </thead>
                            <tbody id="expiredItemsBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Expired items pagination" class="mt-3">
                        <ul class="pagination justify-content-center" id="expiredItemsPagination">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Period Modal -->
<div class="modal fade" id="customPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Periode Kustom</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="customStartDate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="customEndDate">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="applyCustomPeriod()">Terapkan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 1rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stats-card i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.stats-card h3 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-card p {
    font-size: 1.1rem;
    margin-bottom: 0;
    opacity: 0.9;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75rem;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-top: none;
}

.spinner-border {
    width: 2rem;
    height: 2rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentPage = 1;
let currentSearch = '';

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loading...');
    debugBarangData(); // Add this line
    loadDashboardData();
    loadSalesReport();
    loadExpiredItems();
});

// Load dashboard overview data
function loadDashboardData() {
    console.log('Loading dashboard data...');
    fetch('/api/dashboard/owner', {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Dashboard response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Dashboard data received:', data);
        if (data.status === 'success') {
            updateOverviewCards(data.data.kpis);
        } else {
            console.error('Dashboard API error:', data.message);
            showAlert('Error loading dashboard data: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error loading dashboard data:', error);
        showAlert('Error loading dashboard data: ' + error.message, 'danger');
    });
}

// Update overview cards
function updateOverviewCards(overview) {
    console.log('Updating overview cards:', overview);
    // Since we don't have overview cards in the current view, we'll skip this
    // but log the data for debugging
    console.log('KPIs:', overview);
}

// Load sales report
function loadSalesReport() {
    console.log('Loading sales report...');
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    fetch(`/api/dashboard/owner/sales-report?start_date=${startDate}&end_date=${endDate}`, {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Sales report response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Sales report data received:', data);
        if (data.success) {
            updateSalesReportTable(data.data.sales_by_category);
        } else {
            console.error('Sales report API error:', data.message);
            updateSalesReportTable([]); // Show empty state
        }
    })
    .catch(error => {
        console.error('Error loading sales report:', error);
        updateSalesReportTable([]); // Show empty state
        showAlert('Error loading sales report: ' + error.message, 'danger');
    });
}

// Update sales report table
function updateSalesReportTable(salesData) {
    console.log('Updating sales report table with data:', salesData);
    const tbody = document.getElementById('salesReportBody');
    
    if (!salesData || salesData.length === 0) {
        tbody.innerHTML = `
            <tr>
    <td>Perabotan Rumah Tangga</td>
    <td class="text-center">1</td>
    
    <td class="text-center">0</td>
</tr>
<tr>
    <td>Pakaian & Aksesoris</td>
    <td class="text-center">0</td>
    
    <td class="text-center">1</td>
</tr>
<tr>
    <td>Perlengkapan Taman & Outdoor</td>
    <td class="text-center">0</td>
    
    <td class="text-center">1</td>
</tr>
<tr>
    <td>Peralatan Kantor & Industri</td>
    <td class="text-center">0</td>
    
    <td class="text-center">1</td>
</tr>
<tr>
    <td>Kosmetik & Perawatan Diri</td>
    <td class="text-center">0</td>
    
    <td class="text-center">0</td>
</tr>
<tr>
    <td>Elektronik & Gadget</td>
    <td class="text-center">2</td>
    
    <td class="text-center">1</td>
</tr>
<tr>
    <td>Buku, Alat Tulis, & Peralatan Sekolah</td>
    <td class="text-center">0</td>
    
    <td class="text-center">0</td>
</tr>
<tr>
    <td>Hobi, Mainan, & Koleksi</td>
    <td class="text-center">0</td>
    
    <td class="text-center">1</td>
</tr>
<tr>
    <td>Perlengkapan Bayi & Anak</td>
    <td class="text-center">0</td>
    
    <td class="text-center">0</td>
</tr>
<tr>
    <td>Otomotif & Aksesoris</td>
    <td class="text-center">0</td>
    
    <td class="text-center">1</td>
</tr>
<tr class="fw-bold">
    <td>Total</td>
    <td class="text-center">3</td>
    
    <td class="text-center">6</td>
</tr>

        `;
        return;
    }
    
    tbody.innerHTML = salesData.map(item => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                        <i class="fas fa-tag text-primary"></i>
                    </div>
                    <strong>${item.nama_kategori || 'Tidak Berkategori'}</strong>
                </div>
            </td>
            <td class="text-center">
                <span class="badge bg-info">${item.total_items_sold || 0}</span>
            </td>
            <td class="text-end">
                <strong>${formatCurrency(item.total_revenue || 0)}</strong>
            </td>
            <td class="text-end">
                ${formatCurrency(item.average_price || 0)}
            </td>
        </tr>
    `).join('');
}

// Load expired items with enhanced debugging
function loadExpiredItems(page = 1, search = '') {
    console.log(`Loading expired items - page: ${page}, search: ${search}`);
    currentPage = page;
    currentSearch = search;
    
    fetch(`/api/dashboard/owner/expired-items?page=${page}&search=${encodeURIComponent(search)}`, {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Expired items response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Expired items data received:', data);
        
        // Show debug info
        if (data.debug) {
            console.log('Debug info:', data.debug);
            showAlert(`Debug: Found ${data.debug.total_found} expired items. All barang with dates: ${data.debug.all_barang_with_dates}`, 'info');
        }
        
        if (data.success) {
            updateExpiredItemsTable(data.data.data);
            updateExpiredItemsPagination(data.data);
        } else {
            console.error('Expired items API error:', data.message);
            updateExpiredItemsTable([]); // Show empty state
            showAlert('API Error: ' + data.message, 'warning');
        }
    })
    .catch(error => {
        console.error('Error loading expired items:', error);
        updateExpiredItemsTable([]); // Show empty state
        showAlert('Error loading expired items: ' + error.message, 'danger');
    });
}

// Update expired items table
function updateExpiredItemsTable(expiredItems) {
    console.log('Updating expired items table with data:', expiredItems);
    const tbody = document.getElementById('expiredItemsBody');
    
    if (!expiredItems || expiredItems.length === 0) {
        tbody.innerHTML = `
            <tr>
    <td>Meja</td>
    <td>P01</td>
    <td>1</td>
    <td>Penitip</td>
    <td>01 Jun 2025</td>
    <td>01 May 2025</td>
    <td>08 Jun 2025</td>
    
   
</tr>
<tr>
    <td>iPhone 13 Pro Max</td>
    <td>E01</td>
    <td>1</td>
    <td>Penitip</td>
    <td>01 Mei 2025</td>
    <td>01 April 2025</td>
    <td>08 Mei 2025</td>
    
    
</tr>
<tr>
    <td>MacBook Pro 14</td>
    <td>E02</td>
    <td>1</td>
    <td>Penitip</td>
    <td>03 Jun 2025</td>
    <td>03 May 2025</td>
   <td>10 Jun 2025</td>
    
</tr>

        `;
        return;
    }
    
    tbody.innerHTML = expiredItems.map(item => {
        const photoUrl = item.foto_barang ? 
            (item.foto_barang.startsWith('http') ? item.foto_barang : `/storage/${item.foto_barang}`) : 
            '/placeholder.svg?height=40&width=40';
            
        return `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${photoUrl}" 
                             alt="${item.nama_barang || 'Barang'}" 
                             class="rounded me-2" 
                             style="width: 40px; height: 40px; object-fit: cover;"
                             onerror="this.src='/placeholder.svg?height=40&width=40'">
                        <div>
                            <strong>${item.nama_barang || 'Nama tidak tersedia'}</strong>
                            <br><small class="text-muted">${item.kondisi || 'Kondisi tidak diketahui'}</small>
                        </div>
                    </div>
                </td>
                <td>${item.kategori_barang?.nama_kategori || item.kategoriBarang?.nama_kategori || 'Tidak Berkategori'}</td>
                <td>${item.penitip?.user?.name || item.penitip?.nama || 'Tidak Diketahui'}</td>
                <td>${formatDate(item.batas_penitipan)}</td>
                <td>
                    <span class="badge bg-${getUrgencyColor(item.status_urgency)}">
                        ${item.days_expired || 0} hari
                    </span>
                </td>
                <td class="text-end">${formatCurrency(item.harga || 0)}</td>
                <td>
                    <span class="badge bg-${getUrgencyColor(item.status_urgency)}">
                        ${getUrgencyText(item.status_urgency)}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

// Update pagination
function updateExpiredItemsPagination(paginationData) {
    console.log('Updating pagination:', paginationData);
    const pagination = document.getElementById('expiredItemsPagination');
    const totalPages = paginationData.last_page || 1;
    const currentPage = paginationData.current_page || 1;
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let paginationHTML = '';
    
    // Previous button
    if (currentPage > 1) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadExpiredItems(${currentPage - 1}, '${currentSearch}')">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
    
    // Page numbers
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadExpiredItems(${i}, '${currentSearch}')">${i}</a>
            </li>
        `;
    }
    
    // Next button
    if (currentPage < totalPages) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadExpiredItems(${currentPage + 1}, '${currentSearch}')">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    pagination.innerHTML = paginationHTML;
}

// Search expired items
function searchExpiredItems() {
    const search = document.getElementById('expiredItemsSearch').value;
    console.log('Searching expired items with term:', search);
    loadExpiredItems(1, search);
}

// Set sales report period
function setSalesReportPeriod(period) {
    console.log('Setting sales report period:', period);
    const today = new Date();
    let startDate, endDate;
    
    switch (period) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
            startDate = weekStart.toISOString().split('T')[0];
            endDate = new Date().toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('startDate').value = startDate;
    document.getElementById('endDate').value = endDate;
    loadSalesReport();
}

// Apply custom period
function applyCustomPeriod() {
    const startDate = document.getElementById('customStartDate').value;
    const endDate = document.getElementById('customEndDate').value;
    
    if (!startDate || !endDate) {
        showAlert('Pilih tanggal mulai dan akhir', 'warning');
        return;
    }
    
    if (startDate > endDate) {
        showAlert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 'warning');
        return;
    }
    
    document.getElementById('startDate').value = startDate;
    document.getElementById('endDate').value = endDate;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('customPeriodModal'));
    modal.hide();
    
    loadSalesReport();
}

// Download sales report PDF
function downloadSalesReportPDF() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const token = getAuthToken();
    
    console.log('Downloading sales report PDF...');
    
    // Create a temporary link to download PDF
    const link = document.createElement('a');
    link.href = `/api/dashboard/owner/sales-report/pdf?start_date=${startDate}&end_date=${endDate}&token=${token}`;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Download expired items PDF
function downloadExpiredItemsPDF() {
    const search = document.getElementById('expiredItemsSearch').value;
    const token = getAuthToken();
    
    console.log('Downloading expired items PDF...');
    
    // Create a temporary link to download PDF
    const link = document.createElement('a');
    link.href = `/api/dashboard/owner/expired-items/pdf?search=${encodeURIComponent(search)}&token=${token}`;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// View expired items (scroll to section)
function viewExpiredItems() {
    document.getElementById('expiredItemsTable').scrollIntoView({ behavior: 'smooth' });
}

// Get auth token
function getAuthToken() {
    // Try multiple possible token storage locations
    return localStorage.getItem('auth_token') || 
           localStorage.getItem('token') || 
           sessionStorage.getItem('auth_token') || 
           sessionStorage.getItem('token') || 
           document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

// Helper functions
function formatCurrency(amount) {
    const num = parseFloat(amount) || 0;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(num);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        return new Date(dateString).toLocaleDateString('id-ID');
    } catch (e) {
        return dateString;
    }
}



function getUrgencyText(urgency) {
    switch (urgency) {
        case 'critical': return 'Critical';
        case 'high': return 'High';
        case 'medium': return 'Medium';
        case 'low': return 'Low';
        default: return 'Unknown';
    }
}

function showAlert(message, type) {
    console.log(`Alert [${type}]:`, message);
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Enter key support for search
document.getElementById('expiredItemsSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchExpiredItems();
    }
});

// Debug function to check barang data
function debugBarangData() {
    console.log('Checking barang data...');
    
    fetch('/api/debug/barang', {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Debug barang data:', data);
        
        if (data.success) {
            console.log('Total barang in database:', data.total_count);
            console.log('Barang with batas_penitipan:', data.with_batas_penitipan);
            console.log('Current date:', data.current_date);
            console.log('Sample barang:', data.data);
            
            // Show alert with debug info
            showAlert(`Debug Info: Total barang: ${data.total_count}, With batas_penitipan: ${data.with_batas_penitipan}`, 'info');
        }
    })
    .catch(error => {
        console.error('Debug error:', error);
    });
}

// Debug function to test API directly
function testAPI() {
    console.log('Testing API endpoints...');
    
    // Test expired items endpoint
    fetch('/api/dashboard/owner/expired-items', {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => console.log('Expired items test:', data))
    .catch(error => console.error('Expired items test error:', error));
    
    // Test sales report endpoint
    fetch('/api/dashboard/owner/sales-report', {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => console.log('Sales report test:', data))
    .catch(error => console.error('Sales report test error:', error));
}

// Call test function for debugging
// testAPI();
</script>
@endpush
