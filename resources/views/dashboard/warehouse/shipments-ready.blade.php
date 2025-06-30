
@extends('layouts.dashboard')

@section('title', 'Kelola Pesanan - Warehouse')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Kelola Pesanan</h2>
                    <p class="text-muted mb-0">Daftar pesanan yang perlu diproses untuk pengiriman atau pengambilan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-clock text-primary fa-lg"></i>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-check-circle text-success fa-lg"></i>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-truck text-info fa-lg"></i>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-hand-paper text-warning fa-lg"></i>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-list-alt me-2 text-primary"></i>Daftar Pesanan
                        </h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">
                                        
                                    </th>
                                    <th>ID Pesanan</th>
                                    <th>Pembeli</th>
                                    <th>Barang</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Metode</th>
                                    <th>Tanggal</th>
                                    <th width="120" class="text-center">Aksi</th>
                                    <th>Penjadwalan</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <!-- Sample Data - Replace with actual data from database -->
                                <tr>
                                    <td class="text-center">
                                      
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">#TRX001</div>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Lala</div>
                                                <small class="text-muted">lala@gmail.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <img src="/assets/baju.jpg" alt="Product" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">Baju</div>
                                                <small class="text-muted"> Baju pria</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">Rp 200.000</div>
                                        <small class="text-muted">1 item</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="fas fa-clock me-1"></i>Siap Diambil
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-truck me-1"></i>Diambil
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">03 Jun 2025</div>
                                        <small class="text-muted">14:30</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX001')" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Penjadwalan">
                                                <i class="checklist"></i>
                                            </button>
                                           

                                        </div>
                                    </td>

                                    
                                </tr>
                                    <td class="text-center">
                                      
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">#TRX002</div>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Cinta</div>
                                                <small class="text-muted">cinta@gmail.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <img src="/assets/iphone.jpg" alt="Product" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">iPhone 13 Pro Max</div>
                                                <small class="text-muted"> Warna: Gold</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">Rp 15.999.000</div>
                                        <small class="text-muted">1 item</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="fas fa-clock me-1"></i>Menunggu Proses
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-truck me-1"></i>Diantar
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">01 Jun 2025</div>
                                        <small class="text-muted">14:30</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX001')" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Penjadwalan">
                                                <i class="checklist"></i>
                                            </button>
                                           

                                        </div>
                                    </td>
                                </tr>
                                <td class="text-center">
                                      
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">#TRX003</div>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Cici</div>
                                                <small class="text-muted">cici@gmail.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <img src="/assets/laptop.jpg" alt="Product" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">Laptop Asus</div>
                                                <small class="text-muted"> Warna: Abu</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">Rp 10.000.000</div>
                                        <small class="text-muted">1 item</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="fas fa-clock me-1"></i>Siap diambil
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-truck me-1"></i>Diambil
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">03 Jun 2025</div>
                                        <small class="text-muted">14:30</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX001')" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Penjadwalan">
                                                <i class="checklist"></i>
                                            </button>
                                           

                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="text-center">
                                        
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">#TRX004</div>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Janssen</div>
                                                <small class="text-muted">janssen@gmail.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <img src="/assets/macbook.jpg" alt="Product" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">MacBook Pro 14"</div>
                                                <small class="text-muted">RAM: 16GB</small>

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">Rp 32.999.000</div>
                                        <small class="text-muted">1 item</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="fas fa-check-circle me-1"></i>Menunggu Pengambilan
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="fas fa-hand-paper me-1"></i>Ambil Sendiri
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">01 Jun 2025</div>
                                        <small class="text-muted">09:15</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                           

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Penjadwalan">
                                                <i class="checklist"></i>
                                            </button>
                                           

                                        </div>
                                    </td>
                                </tr>
                                <td class="text-center">
                                      
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">#TRX005</div>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Kiko</div>
                                                <small class="text-muted">kiko@gmail.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <img src="/assets/jbl 1.jpg" alt="Product" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">Speaker jbl</div>
                                                <small class="text-muted"> Warna: merah</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">Rp 350.000</div>
                                        <small class="text-muted">1 item</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="fas fa-clock me-1"></i>Siap Diambil
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-truck me-1"></i>Diambil
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">03 Jun 2025</div>
                                        <small class="text-muted">14:30</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX001')" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Penjadwalan">
                                                <i class="checklist"></i>
                                            </button>
                                           

                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-center">
                                       
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">#TRX006</div>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Charlene</div>
                                                <small class="text-muted">charlene@gmail.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <img src="\assets\samsungs23.jpeg" alt="Product" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">Samsung Galaxy S23</div>
                                                <small class="text-muted">Warna: White</small>
                                                <div class="mt-1">
                                                    <small class="text-muted">Dengan case & screen protector</small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">Rp 24.998.000</div>
                                        <small class="text-muted">1 item</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-truck me-1"></i>Dalam Pengiriman
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-truck me-1"></i>Diantar
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">31 Mei 2025</div>
                                        <small class="text-muted">16:45</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX003')" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                         

                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder('TRX002')" title="Penjadwalan">
                                                <i class="checklist"></i>
                                            </button>
                                           

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>Detail Pesanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-primary" onclick="printOrderDetail()">
                    <i class="fas fa-print me-1"></i>Cetak
                </button>
                <button type="button" class="btn btn-primary" onclick="processCurrentOrder()">
                    <i class="fas fa-check me-1"></i>Proses Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}

.table th {
    font-weight: 600;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    border-radius: 0.75rem;
}

.form-select, .form-control {
    border-radius: 0.5rem;
}

.input-group .btn {
    border-radius: 0 0.5rem 0.5rem 0;
}

.input-group .form-control {
    border-radius: 0.5rem 0 0 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentOrderId = null;

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    
    orderCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

function selectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    selectAllCheckbox.checked = true;
    toggleSelectAll();
}

function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const shipping = document.getElementById('shippingFilter').value;
    const date = document.getElementById('dateFilter').value;
    const search = document.getElementById('searchFilter').value;
    
    // Here you would typically make an AJAX call to filter the data
    console.log('Applying filters:', { status, shipping, date, search });
    
    // For demo purposes, just show an alert
    alert('Filter diterapkan! (Implementasi sesuai dengan backend Anda)');
}


function viewOrder(orderId) {
    currentOrderId = orderId;
    
    // Sample order detail content
    const orderDetailHTML = `
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Informasi Pesanan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID Pesanan:</strong> #${orderId}</p>
                                <p><strong>Tanggal:</strong> 01 Juni 2025, 14:30</p>
                                <p><strong>Status:</strong> <span class="badge bg-warning">Menunggu Proses</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Metode Pengiriman:</strong> Diantar</p>
                                <p><strong>Total Pembayaran:</strong> Rp 15.999.000</p>
                                <p><strong>Status Pembayaran:</strong> <span class="badge bg-success">Lunas</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-box me-2"></i>Detail Barang</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <img src="/assets/iphone.jpg" alt="Product" class="img-fluid rounded">
                            </div>

                            <div class="col-md-6">
                                <h6>iPhone 13 Pro Max</h6>
                                <p class="text-muted mb-1">Warna: Gold, Storage: 256GB</p>
                                <p class="text-muted mb-0">Kondisi: Baru</p>
                                <p class="text-muted mb-0">Berat 500 gram</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <p class="mb-0">Qty: 1</p>
                            </div>
                            <div class="col-md-2 text-end">
                                <p class="mb-0 fw-bold">Rp 15.999.000</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Foto Tambahan:</h6>
                                <div class="d-flex gap-2">
                                    <img src="/assets/iphone.jpg" alt="Photo 1" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <img src="/assets/iphone.jpg" alt="Photo 2" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <img src="/assets/iphone.jpg" alt="Photo 3" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <p class="mb-1">Subtotal: Rp 15.999.000</p>
                                <p class="mb-1">Ongkir: Rp 0</p>
                                <h6 class="text-primary">Total: Rp 15.999.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pembeli</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Nama:</strong> Cinta</p>
                        <p><strong>Email:</strong> cinta@gmail.com</p>
                        <p><strong>Telepon:</strong> +62 812-3456-7890</p>
                        <p><strong>Alamat:</strong><br>
                        Jl. Tambak Bayan No. 123<br>
                        Yogyakarta,DIY <br>
                        10110</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('orderDetailContent').innerHTML = orderDetailHTML;
    
    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    modal.show();
}

function printLabel(orderId) {
    alert(`Mencetak label untuk pesanan ${orderId}...`);
    // Here you would generate and print shipping label
}

function bulkProcess() {
    const checkedOrders = document.querySelectorAll('.order-checkbox:checked');
    if (checkedOrders.length === 0) {
        alert('Pilih minimal satu pesanan untuk diproses');
        return;
    }
    
    const orderIds = Array.from(checkedOrders).map(cb => cb.value);
    if (confirm(`Proses ${orderIds.length} pesanan terpilih?`)) {
        alert(`Memproses ${orderIds.length} pesanan...`);
        // Here you would make an AJAX call to bulk process orders
    }
}

function processCurrentOrder() {
    if (currentOrderId) {
        processOrder(currentOrderId);
        bootstrap.Modal.getInstance(document.getElementById('orderDetailModal')).hide();
    }
}

function printOrderDetail() {
    const content = document.getElementById('orderDetailContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Detail Pesanan ${currentOrderId}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        .btn { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2 class="mb-4">Detail Pesanan ${currentOrderId}</h2>
                    ${content}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Auto-refresh every 30 seconds
setInterval(function() {
    // You can implement auto-refresh logic here
    console.log('Auto-refresh check...');
}, 30000);
</script>
@endpush
