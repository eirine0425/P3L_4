@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Keranjang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
            
            <h2 class="mb-4">
                <i class="fas fa-credit-card me-2"></i>Checkout
            </h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Selected Items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Item yang Dipilih
                    </h5>
                </div>
                <div class="card-body" id="selected-items-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat item yang dipilih...</p>
                    </div>
                </div>
            </div>

            <!-- Alamat Pengiriman -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <div id="alamat-display-container">
                        <div class="text-center py-3">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat alamat pengiriman...</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#alamatSelectorModal">
                            <i class="fas fa-edit me-2"></i>Pilih Alamat Lain
                        </button>
                        <a href="{{ route('buyer.alamat.create') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-plus me-2"></i>Tambah Alamat Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Metode Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check payment-option mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" checked>
                                <label class="form-check-label w-100" for="bank_transfer">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-university fa-2x text-primary me-3"></i>
                                        <div>
                                            <strong>Transfer Bank</strong>
                                            <p class="mb-0 text-muted small">Transfer ke rekening bank</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check payment-option mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                <label class="form-check-label w-100" for="cod">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                        <div>
                                            <strong>Bayar di Tempat (COD)</strong>
                                            <p class="mb-0 text-muted small">Bayar saat barang diterima</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="bank-transfer-info" class="alert alert-info mt-3">
                        <h6><i class="fas fa-info-circle me-2"></i>Informasi Transfer Bank</h6>
                        <p class="mb-2"><strong>Bank BCA</strong></p>
                        <p class="mb-2">No. Rekening: <strong>1234567890</strong></p>
                        <p class="mb-0">Atas Nama: <strong>ReuseMart Indonesia</strong></p>
                    </div>
                    
                    <div id="cod-info" class="alert alert-warning mt-3" style="display: none;">
                        <h6><i class="fas fa-info-circle me-2"></i>Informasi COD</h6>
                        <p class="mb-2">Pembayaran dilakukan saat barang diterima</p>
                        <p class="mb-0">Pastikan Anda menyiapkan uang pas sesuai total pembayaran</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Ringkasan Pesanan -->
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Ringkasan Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="order-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (<span id="checkout-item-count">0</span> item):</span>
                            <span class="fw-bold" id="checkout-subtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim:</span>
                            <span id="shipping-cost">Rp 15.000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Biaya Admin:</span>
                            <span>Rp 2.500</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold text-primary fs-4" id="checkout-total">Rp 0</span>
                        </div>
                    </div>
                    
                    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="selected_items" id="hidden-selected-items">
                        <input type="hidden" name="alamat_id" id="hidden-alamat-id">
                        <input type="hidden" name="payment_method" id="hidden-payment-method" value="bank_transfer">
                        <input type="hidden" name="subtotal" id="hidden-subtotal">
                        <input type="hidden" name="shipping_cost" id="hidden-shipping-cost" value="15000">
                        <input type="hidden" name="admin_fee" id="hidden-admin-fee" value="2500">
                        <input type="hidden" name="total" id="hidden-total">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="place-order-btn" disabled>
                                <i class="fas fa-shopping-cart me-2"></i>Buat Pesanan
                                <div class="spinner-border spinner-border-sm ms-2 d-none" id="order-spinner"></div>
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                            </a>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Transaksi Anda aman dan terlindungi
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alamat Selector Modal -->
<div class="modal fade" id="alamatSelectorModal" tabindex="-1" aria-labelledby="alamatSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alamatSelectorModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>Pilih Alamat Pengiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alamat-selector-content">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat daftar alamat...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('buyer.alamat.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Alamat Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const selectedItems = @json(request()->input('selected_items', []));
    
    if (selectedItems.length === 0) {
        showError('Tidak ada item yang dipilih untuk checkout');
        setTimeout(() => {
            window.location.href = '{{ route("cart.index") }}';
        }, 2000);
        return;
    }
    
    // Load selected items
    loadSelectedItems(selectedItems);
    
    // Load default alamat
    loadDefaultAlamat();
    
    // Handle payment method change
    $('input[name="payment_method"]').on('change', function() {
        const method = $(this).val();
        $('#hidden-payment-method').val(method);
        
        if (method === 'bank_transfer') {
            $('#bank-transfer-info').show();
            $('#cod-info').hide();
        } else {
            $('#bank-transfer-info').hide();
            $('#cod-info').show();
        }
    });
    
    // Handle form submission
    $('#checkout-form').on('submit', function(e) {
        const alamatId = $('#hidden-alamat-id').val();
        if (!alamatId) {
            e.preventDefault();
            showError('Silakan pilih alamat pengiriman terlebih dahulu');
            return false;
        }
        
        // Show loading state
        $('#place-order-btn').prop('disabled', true);
        $('#order-spinner').removeClass('d-none');
    });
    
    // Load alamat selector when modal is opened
    $('#alamatSelectorModal').on('show.bs.modal', function() {
        loadAlamatSelector();
    });
});

function loadSelectedItems(selectedItems) {
    console.log('Loading selected items:', selectedItems);
    
    $.ajax({
        url: '{{ route("buyer.cart.selected-items") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            selected_items: selectedItems
        },
        success: function(response) {
            console.log('Response received:', response);
            
            if (response.status === 'success') {
                displaySelectedItems(response.data.items);
                updateOrderSummary(response.data);
                $('#hidden-selected-items').val(JSON.stringify(selectedItems));
            } else {
                showError(response.message || 'Gagal memuat item yang dipilih');
            }
        },
        error: function(xhr) {
            console.error('Error loading selected items:', xhr);
            let errorMessage = 'Terjadi kesalahan saat memuat item';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showError(errorMessage);
        }
    });
}

function displaySelectedItems(items) {
    let html = '';
    
    if (!items || items.length === 0) {
        html = `
            <div class="text-center py-4">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada item yang dipilih</p>
            </div>
        `;
    } else {
        items.forEach(function(item, index) {
            const barang = item.barang;
            const kategori = barang.kategori_barang ? barang.kategori_barang.nama_kategori : 'Tanpa Kategori';
            const imageUrl = barang.foto_barang ? 
                '{{ asset("storage/") }}/' + barang.foto_barang : 
                '/placeholder.svg?height=80&width=80';
            
            html += `
                <div class="border-bottom py-3 ${index === items.length - 1 ? 'border-bottom-0' : ''}">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${imageUrl}" alt="${barang.nama_barang}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="height: 80px; width: 80px; object-fit: cover;">
                        </div>
                        <div class="col-md-7">
                            <h6 class="mb-1 fw-bold">${barang.nama_barang}</h6>
                            <p class="text-muted mb-1">
                                <i class="fas fa-tag me-1"></i>${kategori}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Kondisi: ${barang.kondisi || 'Baik'}
                            </small>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="fw-bold text-primary fs-5">Rp ${formatNumber(barang.harga)}</span>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#selected-items-container').html(html);
}

function updateOrderSummary(data) {
    const subtotal = data.subtotal;
    const shippingCost = 15000;
    const adminFee = 2500;
    const total = subtotal + shippingCost + adminFee;
    
    $('#checkout-item-count').text(data.count);
    $('#checkout-subtotal').text('Rp ' + formatNumber(subtotal));
    $('#checkout-total').text('Rp ' + formatNumber(total));
    
    // Update hidden fields
    $('#hidden-subtotal').val(subtotal);
    $('#hidden-total').val(total);
    
    // Enable place order button if alamat is selected
    const alamatId = $('#hidden-alamat-id').val();
    if (alamatId) {
        $('#place-order-btn').prop('disabled', false);
    }
}

function loadDefaultAlamat() {
    $.ajax({
        url: '{{ route("buyer.alamat.default") }}',
        method: 'GET',
        success: function(response) {
            if (response.alamat) {
                displaySelectedAlamat(response.alamat);
                $('#hidden-alamat-id').val(response.alamat.alamat_id);
                
                // Enable place order button if items are loaded
                const subtotal = $('#hidden-subtotal').val();
                if (subtotal && subtotal > 0) {
                    $('#place-order-btn').prop('disabled', false);
                }
            } else {
                displayNoAlamat();
            }
        },
        error: function(xhr) {
            console.error('Error loading default alamat:', xhr);
            displayNoAlamat();
        }
    });
}

function displaySelectedAlamat(alamat) {
    const html = `
        <div class="selected-alamat">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1 fw-bold">${alamat.nama_penerima}</h6>
                    <p class="mb-1">${alamat.no_telepon}</p>
                    <p class="mb-1">${alamat.alamat}</p>
                    <p class="mb-0 text-muted">${alamat.kota}, ${alamat.provinsi} ${alamat.kode_pos}</p>
                </div>
                <div>
                    ${alamat.status_default === 'Y' ? '<span class="badge bg-success">Alamat Utama</span>' : ''}
                </div>
            </div>
        </div>
    `;
    
    $('#alamat-display-container').html(html);
}

function displayNoAlamat() {
    const html = `
        <div class="text-center py-4">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-3">Belum ada alamat pengiriman</p>
            <a href="{{ route('buyer.alamat.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Alamat
            </a>
        </div>
    `;
    
    $('#alamat-display-container').html(html);
}

function loadAlamatSelector() {
    $.ajax({
        url: '{{ route("buyer.alamat.select") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#alamat-selector-content').html(response.html);
                
                // Handle alamat selection
                $(document).off('click', '.alamat-option').on('click', '.alamat-option', function() {
                    const alamatId = $(this).data('alamat-id');
                    const alamatData = $(this).data('alamat');
                    
                    selectAlamat(alamatId, alamatData);
                    $('#alamatSelectorModal').modal('hide');
                });
            } else {
                $('#alamat-selector-content').html('<p class="text-center text-muted">Gagal memuat daftar alamat</p>');
            }
        },
        error: function(xhr) {
            console.error('Error loading alamat selector:', xhr);
            $('#alamat-selector-content').html('<p class="text-center text-muted">Terjadi kesalahan saat memuat alamat</p>');
        }
    });
}

function selectAlamat(alamatId, alamatData) {
    $('#hidden-alamat-id').val(alamatId);
    displaySelectedAlamat(alamatData);
    
    // Enable place order button if items are loaded
    const subtotal = $('#hidden-subtotal').val();
    if (subtotal && subtotal > 0) {
        $('#place-order-btn').prop('disabled', false);
    }
    
    showSuccess('Alamat pengiriman berhasil dipilih');
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function showError(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('.alert').remove();
    $('.container').first().prepend(alertHtml);
    
    // Scroll to top
    $('html, body').animate({ scrollTop: 0 }, 500);
}

function showSuccess(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('.alert').remove();
    $('.container').first().prepend(alertHtml);
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 3000);
}
</script>

<style>
.payment-option {
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-option:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
}

.payment-option input[type="radio"]:checked + label {
    background-color: #e3f2fd;
    border-color: #007bff !important;
}

.order-summary {
    font-size: 0.95rem;
}

.selected-alamat {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #28a745;
}

.alamat-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.alamat-option:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
}

.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 1020;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative;
        top: auto;
    }
}
</style>
@endsection
