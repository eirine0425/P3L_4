@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Checkout</h1>

    <div class="row">
        <div class="col-md-8">
            <h2>Item yang Dipilih</h2>
            <div id="selected-items-container">
                <!-- Item yang dipilih akan ditampilkan di sini -->
            </div>

            <h2>Alamat Pengiriman</h2>
            <div id="alamat-container">
                <div id="alamat-display-container">
                    <!-- Alamat akan ditampilkan di sini -->
                </div>
                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#alamatSelectorModal">
                    Pilih Alamat Pengiriman
                </button>
            </div>

            <div class="mt-4">
                <h2>Metode Pembayaran</h2>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                    <label class="form-check-label" for="cod">
                        Bayar di Tempat (COD)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                    <label class="form-check-label" for="bank_transfer">
                        Transfer Bank
                    </label>
                </div>
                
                <div id="cod-info" class="mt-2">
                    <p class="text-muted">Anda akan membayar saat barang sampai di tempat.</p>
                </div>
                
                <div id="bank-transfer-info" class="mt-2" style="display: none;">
                    <p class="text-muted">Silakan transfer ke rekening berikut:</p>
                    <ul class="list-unstyled">
                        <li>Bank: [Nama Bank]</li>
                        <li>Nomor Rekening: [Nomor Rekening]</li>
                        <li>Atas Nama: [Nama Pemilik Rekening]</li>
                    </ul>
                    <p class="text-muted">Upload bukti pembayaran setelah melakukan transfer.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <h2>Ringkasan Pesanan</h2>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Item:</span>
                        <span id="checkout-item-count">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Subtotal:</span>
                        <span id="checkout-subtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Ongkos Kirim:</span>
                        <span id="shipping-cost">Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold" id="checkout-total">Rp 0</span>
                    </div>
                </div>
            </div>

            <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="alamat_id" id="hidden-alamat-id">
                <input type="hidden" name="selected_items" id="hidden-selected-items">
                <input type="hidden" name="payment_method" id="hidden-payment-method" value="cod">
                <input type="hidden" name="subtotal" id="hidden-subtotal">
                <input type="hidden" name="shipping_cost" id="hidden-shipping-cost">
                <input type="hidden" name="total" id="hidden-total">
                
                <button type="submit" class="btn btn-success w-100 mt-3" id="place-order-btn" disabled>
                    <span id="order-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Pesan Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="alamatSelectorModal" tabindex="-1" aria-labelledby="alamatSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alamatSelectorModalLabel">Pilih Alamat Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alamat-selector-content">
                <!-- Alamat selector content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="error-message">
                <!-- Pesan error akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noAlamatModal" tabindex="-1" aria-labelledby="noAlamatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noAlamatModalLabel">Peringatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Anda belum memiliki alamat. Silakan tambahkan alamat terlebih dahulu.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alamatModal" tabindex="-1" aria-labelledby="alamatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alamatModalLabel">Pilih Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alamat-list">
                <!-- Daftar alamat akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize variables
    const selectedItems = @json(request()->input('selected_items', []));
    let isLoadingItems = false;
    let isLoadingAlamat = false;
    
    console.log('Checkout initialized with items:', selectedItems);
    
    // Validate selected items
    if (!selectedItems || selectedItems.length === 0) {
        showError('Tidak ada item yang dipilih untuk checkout');
        setTimeout(() => {
            window.location.href = '{{ route("cart.index") }}';
        }, 2000);
        return;
    }
    
    // Initialize page
    initializePage();
    
    function initializePage() {
        // Load selected items
        loadSelectedItems(selectedItems);
        
        // Load default alamat
        loadDefaultAlamat();
        
        // Setup event handlers
        setupEventHandlers();
    }
    
    function setupEventHandlers() {
        // Payment method change
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
        
        // Form submission
        $('#checkout-form').on('submit', function(e) {
            const alamatId = $('#hidden-alamat-id').val();
            const selectedItemsValue = $('#hidden-selected-items').val();
            
            if (!alamatId) {
                e.preventDefault();
                showError('Silakan pilih alamat pengiriman terlebih dahulu');
                return false;
            }
            
            if (!selectedItemsValue) {
                e.preventDefault();
                showError('Data item tidak valid');
                return false;
            }
            
            // Show loading state
            $('#place-order-btn').prop('disabled', true);
            $('#order-spinner').removeClass('d-none');
            
            // Add timeout to prevent infinite loading
            setTimeout(() => {
                if ($('#place-order-btn').prop('disabled')) {
                    $('#place-order-btn').prop('disabled', false);
                    $('#order-spinner').addClass('d-none');
                    showError('Proses checkout timeout. Silakan coba lagi.');
                }
            }, 30000); // 30 seconds timeout
        });
        
        // Modal event handlers
        $('#alamatSelectorModal').on('show.bs.modal', function() {
            if (!isLoadingAlamat) {
                loadAlamatSelector();
            }
        });
        
        // Handle alamat selection (delegated event)
        $(document).on('click', '.alamat-option', function(e) {
            e.preventDefault();
            const alamatId = $(this).data('alamat-id');
            const alamatData = {
                alamat_id: alamatId,
                nama_penerima: $(this).data('nama-penerima'),
                no_telepon: $(this).data('no-telepon'),
                alamat: $(this).data('alamat'),
                kota: $(this).data('kota'),
                provinsi: $(this).data('provinsi'),
                kode_pos: $(this).data('kode-pos'),
                status_default: $(this).data('status-default')
            };
            
            selectAlamat(alamatId, alamatData);
            $('#alamatSelectorModal').modal('hide');
        });
    }
    
    function loadSelectedItems(selectedItems) {
        if (isLoadingItems) return;
        
        isLoadingItems = true;
        console.log('Loading selected items:', selectedItems);
        
        // Show loading state
        $('#selected-items-container').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat item yang dipilih...</p>
            </div>
        `);
        
        $.ajax({
            url: '{{ route("buyer.cart.selected-items") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                selected_items: selectedItems
            },
            timeout: 15000,
            success: function(response) {
                console.log('Selected items response:', response);
                
                if (response && response.status === 'success' && response.data) {
                    displaySelectedItems(response.data.items || []);
                    updateOrderSummary(response.data);
                    $('#hidden-selected-items').val(JSON.stringify(selectedItems));
                } else {
                    const message = response?.message || 'Gagal memuat item yang dipilih';
                    showError(message);
                    displayEmptyItems();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading selected items:', {xhr, status, error});
                
                let errorMessage = 'Terjadi kesalahan saat memuat item';
                
                if (status === 'timeout') {
                    errorMessage = 'Permintaan timeout. Silakan coba lagi.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Route tidak ditemukan. Periksa konfigurasi.';
                } else if (xhr.status === 419) {
                    errorMessage = 'Sesi telah berakhir. Silakan refresh halaman.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showError(errorMessage);
                displayEmptyItems();
            },
            complete: function() {
                isLoadingItems = false;
            }
        });
    }
    
    function displaySelectedItems(items) {
        let html = '';
        
        if (!items || items.length === 0) {
            displayEmptyItems();
            return;
        }
        
        items.forEach(function(item, index) {
            const barang = item.barang || item;
            const kategori = barang.kategori_barang?.nama_kategori || barang.kategori || 'Tanpa Kategori';
            const imageUrl = barang.foto_barang ? 
                '{{ asset("storage/") }}/' + barang.foto_barang : 
                '/placeholder.svg?height=80&width=80';
            const harga = barang.harga || 0;
            const jumlah = item.jumlah || 1;
            
            html += `
                <div class="border-bottom py-3 ${index === items.length - 1 ? 'border-bottom-0' : ''}">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${imageUrl}" alt="${barang.nama_barang || 'Produk'}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="height: 80px; width: 80px; object-fit: cover;"
                                 onerror="this.src='/placeholder.svg?height=80&width=80'">
                        </div>
                        <div class="col-md-7">
                            <h6 class="mb-1 fw-bold">${barang.nama_barang || 'Nama tidak tersedia'}</h6>
                            <p class="text-muted mb-1">
                                <i class="fas fa-tag me-1"></i>${kategori}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Kondisi: ${barang.kondisi || 'Baik'}
                            </small>
                            ${jumlah > 1 ? `<br><small class="text-muted">Jumlah: ${jumlah}</small>` : ''}
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="fw-bold text-primary fs-5">Rp ${formatNumber(harga)}</span>
                            ${jumlah > 1 ? `<br><small class="text-muted">Total: Rp ${formatNumber(harga * jumlah)}</small>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#selected-items-container').html(html);
    }
    
    function displayEmptyItems() {
        $('#selected-items-container').html(`
            <div class="text-center py-4">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Tidak ada item yang dipilih</p>
                <a href="{{ route('cart.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                </a>
            </div>
        `);
    }
    
    function updateOrderSummary(data) {
        const subtotal = data.subtotal || 0;
        const count = data.count || 0;
        const shippingCost = subtotal > 1500000 ? 0 : 100000; // Free shipping over 1.5M
        const adminFee = 2500;
        const total = subtotal + shippingCost + adminFee;
        
        $('#checkout-item-count').text(count);
        $('#checkout-subtotal').text('Rp ' + formatNumber(subtotal));
        $('#shipping-cost').text(shippingCost === 0 ? 'GRATIS' : 'Rp ' + formatNumber(shippingCost));
        $('#checkout-total').text('Rp ' + formatNumber(total));
        
        // Update hidden fields
        $('#hidden-subtotal').val(subtotal);
        $('#hidden-shipping-cost').val(shippingCost);
        $('#hidden-total').val(total);
        
        // Enable place order button if alamat is selected
        const alamatId = $('#hidden-alamat-id').val();
        if (alamatId && subtotal > 0) {
            $('#place-order-btn').prop('disabled', false);
        }
    }
    
    function loadDefaultAlamat() {
        if (isLoadingAlamat) return;
        
        isLoadingAlamat = true;
        
        $.ajax({
            url: '{{ route("buyer.alamat.default") }}',
            method: 'GET',
            timeout: 10000,
            success: function(response) {
                console.log('Default alamat response:', response);
                
                if (response && response.alamat) {
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
            error: function(xhr, status, error) {
                console.error('Error loading default alamat:', {xhr, status, error});
                displayNoAlamat();
            },
            complete: function() {
                isLoadingAlamat = false;
            }
        });
    }
    
    function displaySelectedAlamat(alamat) {
        const html = `
            <div class="selected-alamat">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1 fw-bold">${alamat.nama_penerima || 'Nama tidak tersedia'}</h6>
                        <p class="mb-1"><i class="fas fa-phone me-1"></i>${alamat.no_telepon || 'No telepon tidak tersedia'}</p>
                        <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i>${alamat.alamat || 'Alamat tidak tersedia'}</p>
                        <p class="mb-0 text-muted">${alamat.kota || ''}, ${alamat.provinsi || ''} ${alamat.kode_pos || ''}</p>
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
        $('#alamat-selector-content').html(`
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat daftar alamat...</p>
            </div>
        `);
        
        $.ajax({
            url: '{{ route("buyer.alamat.select") }}',
            method: 'GET',
            timeout: 10000,
            success: function(response) {
                console.log('Alamat selector response:', response);
                
                if (response && response.success && response.html) {
                    $('#alamat-selector-content').html(response.html);
                } else if (response && response.alamats) {
                    displayAlamatList(response.alamats);
                } else {
                    $('#alamat-selector-content').html('<p class="text-center text-muted">Gagal memuat daftar alamat</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading alamat selector:', {xhr, status, error});
                $('#alamat-selector-content').html('<p class="text-center text-muted">Terjadi kesalahan saat memuat alamat</p>');
            }
        });
    }
    
    function displayAlamatList(alamats) {
        if (!alamats || alamats.length === 0) {
            $('#alamat-selector-content').html(`
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada alamat tersimpan</p>
                </div>
            `);
            return;
        }
        
        let html = '<div class="list-group">';
        alamats.forEach(alamat => {
            html += `
                <a href="#" class="list-group-item list-group-item-action alamat-option" 
                   data-alamat-id="${alamat.alamat_id}"
                   data-nama-penerima="${alamat.nama_penerima}"
                   data-no-telepon="${alamat.no_telepon}"
                   data-alamat="${alamat.alamat}"
                   data-kota="${alamat.kota}"
                   data-provinsi="${alamat.provinsi}"
                   data-kode-pos="${alamat.kode_pos}"
                   data-status-default="${alamat.status_default}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${alamat.nama_penerima}</strong>
                            ${alamat.status_default === 'Y' ? '<span class="badge bg-success ms-2">Utama</span>' : ''}
                            <br>
                            <small class="text-muted">${alamat.no_telepon}</small>
                            <br>
                            ${alamat.alamat}, ${alamat.kota}, ${alamat.provinsi} ${alamat.kode_pos}
                        </div>
                    </div>
                </a>
            `;
        });
        html += '</div>';
        
        $('#alamat-selector-content').html(html);
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
        if (!num) return '0';
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
});
</script>
@endsection