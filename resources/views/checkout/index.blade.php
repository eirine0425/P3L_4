@php
use App\Models\KeranjangBelanja;
use App\Models\Alamat;
use App\Models\Pembeli;

// Ambil dari session dengan validasi yang lebih baik
$rawSelected = session('checkout_items', []);
$selectedIds = [];

// Validasi dan konversi data session
if (is_string($rawSelected)) {
    $decoded = json_decode($rawSelected, true);
    $selectedIds = is_array($decoded) ? $decoded : [];
} elseif (is_array($rawSelected)) {
    $selectedIds = $rawSelected;
}

// Jika tidak ada di session, coba ambil dari request
if (empty($selectedIds)) {
    $requestItems = request()->input('selected_items', []);
    if (is_string($requestItems)) {
        $decoded = json_decode($requestItems, true);
        $selectedIds = is_array($decoded) ? $decoded : [];
    } elseif (is_array($requestItems)) {
        $selectedIds = $requestItems;
    }
}

// Pastikan $selectedIds adalah array dan tidak kosong
$selectedIds = is_array($selectedIds) ? array_filter($selectedIds) : [];

// Ambil data keranjang beserta barang hanya jika ada selectedIds
$selectedItems = collect(); // Inisialisasi sebagai empty collection
$subtotal = 0;
$itemsLoadedFromServer = false;

// FIXED: Ambil alamat user yang sedang login
$alamats = collect();
$alamatTerpilih = null;
if (Auth::check()) {
    $user = Auth::user();
    $pembeli = Pembeli::where('user_id', $user->id)->first();
    if ($pembeli) {
        $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
            ->orderBy('status_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        $defaultAlamat = $alamats->where('status_default', 'Y')->first();
        $alamatTerpilih = $defaultAlamat ? $defaultAlamat->alamat_id : null;
    }
}

if (!empty($selectedIds) && is_array($selectedIds)) {
    try {
        $selectedItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
            ->whereIn('keranjang_id', $selectedIds)
            ->get();
        // Hitung subtotal hanya jika ada items
        if ($selectedItems->isNotEmpty()) {
            foreach($selectedItems as $item) {
                $barang = $item->barang ?? null;
                if ($barang) {
                    $harga = $barang->harga ?? 0;
                    $jumlah = $item->jumlah ?? 1;
                    $subtotal += $harga * $jumlah;
                }
            }
            $itemsLoadedFromServer = true;
        }
    } catch (Exception $e) {
        // Jika terjadi error saat query, set sebagai empty collection
        $selectedItems = collect();
        $subtotal = 0;
        $itemsLoadedFromServer = false;
        // Log error untuk debugging
        \Log::error('Error loading selected items: ' . $e->getMessage());
    }
}

// Hitung shipping cost - default gratis untuk ambil sendiri
$shippingCost = 0;
$total = $subtotal + $shippingCost;
@endphp

@extends('layouts.app')
@section('content')
<div class="container">
    
    <!-- Breadcrumb Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fas fa-shopping-cart me-2"></i>Checkout</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Selected Items Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Item yang Dipilih</h5>
                </div>
                <div class="card-body">
                    <div id="selected-items-container">
                        @if($itemsLoadedFromServer)
                            @forelse ($selectedItems as $index => $item)
                                @php
                                    $barang = $item->barang ?? $item;
                                    $kategori = $barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori';
                                    $image = $barang->foto_barang ? asset('storage/' . $barang->foto_barang) : '/placeholder.svg?height=80&width=80';
                                    $harga = $barang->harga ?? 0;
                                    $jumlah = $item->jumlah ?? 1;
                                @endphp
                                <div class="border-bottom py-3 {{ $loop->last ? 'border-bottom-0' : '' }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="{{ $image }}" alt="{{ $barang->nama_barang }}" class="img-fluid rounded shadow-sm" style="height: 80px; width: 80px; object-fit: cover;" onerror="this.onerror=null; this.src='/img/no-image.png';">
                                        </div>
                                        <div class="col-md-7">
                                            <h6 class="mb-1 fw-bold">{{ $barang->nama_barang }}</h6>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-tag me-1"></i>{{ $kategori }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Kondisi: {{ $barang->kondisi ?? 'Baik' }}
                                            </small>
                                            @if($jumlah > 1)
                                                <br><small class="text-muted">Jumlah: {{ $jumlah }}</small>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <span class="fw-bold text-primary fs-5">Rp {{ number_format($harga, 0, ',', '.') }}</span>
                                            @if($jumlah > 1)
                                                <br><small class="text-muted">Total: Rp {{ number_format($harga * $jumlah, 0, ',', '.') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-3">Tidak ada item yang dipilih</p>
                                    <a href="{{ route('cart.index') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                                    </a>
                                </div>
                            @endforelse
                        @else
                            <div id="loading-items" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat item yang dipilih...</p>
                            </div>
                            <!-- Error message akan ditampilkan setelah timeout -->
                            <div id="loading-error" class="text-center py-4" style="display: none;">
                                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                <p class="text-muted mb-3">Gagal memuat item. Silakan coba lagi.</p>
                                <button id="retry-load-items" class="btn btn-primary">
                                    <i class="fas fa-sync me-2"></i>Coba Lagi
                                </button>
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delivery Method Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Metode Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="metode_pengiriman" class="form-label"><strong>Metode Pengiriman</strong></label>
                        <select class="form-select" id="metode_pengiriman" name="metode_pengiriman">
                            <option value="diambil">Ambil Sendiri - Di Gudang Reusemart</option>
                            <option value="diantar">Diantar Kurir - @if($subtotal >= 1500000) GRATIS @else Rp {{ number_format(100000, 0, ',', '.') }} @endif</option>
                        </select>
                    </div>
                    
                    <!-- FIXED: Alamat Dropdown Section - SELALU TAMPIL -->
                    <div id="alamat-dropdown-section" class="mb-3">
                        <label for="alamat_dropdown" class="form-label"><strong>Alamat Pengiriman</strong></label>
                        <select class="form-select" id="alamat_dropdown" name="alamat_id">
                            <option value=""> -- Pilih alamat -- </option>
                            @if ($alamats && $alamats->count() > 0)
                                @foreach ($alamats as $alamat)
                                    @php
                                        $label = "{$alamat->nama_penerima} - {$alamat->alamat}, {$alamat->kota}";
                                        $isDefault = $alamat->status_default === 'Y';
                                    @endphp
                                    <option value="{{ $alamat->alamat_id }}" {{ $alamatTerpilih == $alamat->alamat_id ? 'selected' : '' }}>
                                        {{ $label }}{{ $isDefault ? ' (Utama)' : '' }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Tidak ada alamat tersedia</option>
                            @endif
                        </select>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> Pilih alamat pengiriman untuk pesanan Anda
                            </small>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('buyer.alamat.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Tambah Alamat Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Point Usage Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Gunakan Point Reward</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-2">Point tersedia: <strong id="available-points">{{ auth()->user()->pembeli->point ?? 0 }}</strong> point</p>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                                <input type="number" class="form-control" id="point-usage" name="point_digunakan" placeholder="Masukkan jumlah point" min="0" max="{{ auth()->user()->pembeli->point ?? 0 }}">
                                <span class="input-group-text">point</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="text-muted">Potongan:</div>
                            <div class="fw-bold text-success fs-5" id="point-discount">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card sticky-top shadow-sm" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Item:</span>
                        <span id="checkout-item-count" class="fw-bold">{{ $itemsLoadedFromServer ? $selectedItems->count() : 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Subtotal:</span>
                        <span id="checkout-subtotal" class="fw-bold">
                            @if($itemsLoadedFromServer)
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            @else
                                Rp 0
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Ongkos Kirim:</span>
                        <span id="shipping-cost" class="fw-bold">
                            <span class="text-success">GRATIS</span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Potongan Point:</span>
                        <span id="point-discount-display" class="fw-bold text-success">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Point Diperoleh:</span>
                        <span id="point-earned" class="fw-bold text-warning">
                            <i class="fas fa-star me-1"></i>{{ floor($subtotal / 10000) }} point
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Total:</span>
                        <span class="fw-bold fs-5 text-primary" id="checkout-total">
                            @if($itemsLoadedFromServer)
                                Rp {{ number_format($total, 0, ',', '.') }}
                            @else
                                Rp 0
                            @endif
                        </span>
                    </div>
                    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="alamat_id" id="hidden-alamat-id" value="{{ $alamatTerpilih }}">
                        <input type="hidden" name="selected_items" id="hidden-selected-items" value="{{ json_encode($selectedIds) }}">
                        <input type="hidden" name="metode_pengiriman" id="hidden-metode-pengiriman" value="diambil">
                        <input type="hidden" name="subtotal" id="hidden-subtotal" value="{{ $subtotal }}">
                        <input type="hidden" name="shipping_cost" id="hidden-shipping-cost" value="0">
                        <input type="hidden" name="point_digunakan" id="hidden-point-digunakan" value="0">
                        <input type="hidden" name="point_diperoleh" id="hidden-point-diperoleh" value="{{ floor($subtotal / 10000) }}">
                        <input type="hidden" name="total_harga" id="hidden-total-harga" value="{{ $total }}">
                        <button type="submit" class="btn btn-success w-100 btn-lg" id="place-order-btn" {{ ($itemsLoadedFromServer && $selectedItems->count() > 0) ? '' : 'disabled' }}>
                            <span id="order-spinner" class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                        </button>
                    </form>
                    <!-- Security Badge -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i> Transaksi Aman & Terpercaya
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log('=== CHECKOUT PAGE LOADED ===');
    const selectedItems = @json(array_values($selectedIds));
    let currentDeliveryMethod = $('#metode_pengiriman').val() || 'diambil';
    let itemsLoadedFromServer = @json($itemsLoadedFromServer);

    // Set the hidden input value immediately on page load
    $('#hidden-metode-pengiriman').val(currentDeliveryMethod);

    // Subtotal dan threshold untuk gratis ongkir
    let subtotal = {{ $subtotal }};
    const freeShippingThreshold = 1500000;
    const standardShippingCost = 100000;

    console.log('Initial data:', {
        selectedItems: selectedItems,
        itemsLoadedFromServer: itemsLoadedFromServer,
        subtotal: subtotal,
        currentDeliveryMethod: currentDeliveryMethod,
        alamatsCount: {{ $alamats->count() }}
    });

    // Setup event handlers
    setupEventHandlers();

    // Initialize delivery method state
    updateDeliveryMethodState();

    // Point usage functionality
    setupPointUsage();

    function setupEventHandlers() {
        console.log('Setting up event handlers...');

        // Alamat dropdown change handler
        $('#alamat_dropdown').on('change', function() {
            const alamatId = $(this).val();
            console.log('   Alamat dropdown changed:', alamatId);
            $('#hidden-alamat-id').val(alamatId);
            updatePlaceOrderButton();
            if (alamatId) {
                showSuccess('Alamat pengiriman berhasil dipilih');
            }
        });

        // Delivery method change
        $('#metode_pengiriman').on('change', function() {
            currentDeliveryMethod = $(this).val();
            console.log('   Metode pengiriman dipilih:', currentDeliveryMethod);
            $('#hidden-metode-pengiriman').val(currentDeliveryMethod);
            updateDeliveryMethodState();
            updateOrderSummary();
        });

        // Form submission
        $('#checkout-form').on('submit', function(e) {
            console.log('=== FORM SUBMISSION ===');
            const selectedItemsValue = $('#hidden-selected-items').val();
            if (!selectedItemsValue || selectedItemsValue === '[]') {
                e.preventDefault();
                showError('Data item tidak valid');
                return false;
            }

            // Check if courier is selected and alamat is required
            if (currentDeliveryMethod === 'diantar') {
                const alamatId = $('#hidden-alamat-id').val();
                if (!alamatId) {
                    e.preventDefault();
                    showError('Silakan pilih alamat pengiriman untuk metode diantar kurir');
                    return false;
                }
            }

            // Show loading state
            $('#place-order-btn').prop('disabled', true);
            $('#order-spinner').removeClass('d-none');
        });
    }

    function setupPointUsage() {
        const pointInput = $('#point-usage');
        const availablePoints = parseInt($('#available-points').text()) || 0;
        
        pointInput.on('input', function() {
            let pointsToUse = parseInt($(this).val()) || 0;
            
            // Validate points
            if (pointsToUse > availablePoints) {
                pointsToUse = availablePoints;
                $(this).val(pointsToUse);
            }
            
            if (pointsToUse < 0) {
                pointsToUse = 0;
                $(this).val(pointsToUse);
            }
            
            // Calculate discount (1 point = Rp 1000)
            const discount = pointsToUse * 1000;
            
            // Update displays
            $('#point-discount').text('Rp ' + formatNumber(discount));
            $('#point-discount-display').text('Rp ' + formatNumber(discount));
            $('#hidden-point-digunakan').val(pointsToUse);
            
            // Update total
            updateOrderSummary();
        });
    }

    function updateDeliveryMethodState() {
        console.log('   Updating delivery method state:', currentDeliveryMethod);
        if (currentDeliveryMethod === 'diantar') {
            $('#courier-info').show();
            // Dropdown alamat tetap tampil, hanya info yang berubah
        } else {
            $('#courier-info').hide();
            // Clear alamat selection when switching to pickup
            $('#hidden-alamat-id').val('');
            $('#alamat_dropdown').val('');
        }
        // Update place order button state
        updatePlaceOrderButton();
    }

    function updatePlaceOrderButton() {
        const currentSubtotal = parseInt($('#hidden-subtotal').val()) || 0;
        let canPlaceOrder = false;
        let buttonText = '<i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang';
        console.log('   Updating place order button:', {
            subtotal: currentSubtotal,
            deliveryMethod: currentDeliveryMethod
        });
        if (currentSubtotal && currentSubtotal > 0) {
            if (currentDeliveryMethod === 'diambil') {
                // Pickup doesn't need alamat
                canPlaceOrder = true;
            } else if (currentDeliveryMethod === 'diantar') {
                // Courier needs alamat
                const alamatId = $('#hidden-alamat-id').val();
                canPlaceOrder = alamatId && alamatId !== '';
                if (!canPlaceOrder) {
                    buttonText = '<i class="fas fa-map-marker-alt me-2"></i>Pilih Alamat Pengiriman';
                }
                console.log('   Courier - alamat ID:', alamatId, 'Can place order:', canPlaceOrder);
            }
        } else {
            buttonText = '<i class="fas fa-shopping-cart me-2"></i>Tidak Ada Item';
        }
        const orderBtn = $('#place-order-btn');
        orderBtn.prop('disabled', !canPlaceOrder);
        orderBtn.html(buttonText);
        console.log('   Place order button enabled:', canPlaceOrder);
    }

    function updateOrderSummary() {
        const currentSubtotal = parseInt($('#hidden-subtotal').val()) || subtotal;
        const pointDiscount = parseInt($('#hidden-point-digunakan').val()) * 1000 || 0;
        let shippingCost = 0;

        // Calculate shipping cost based on delivery method
        if (currentDeliveryMethod === 'diantar') {
            shippingCost = currentSubtotal >= freeShippingThreshold ? 0 : standardShippingCost;
        } else {
            shippingCost = 0;
        }

        const total = currentSubtotal + shippingCost - pointDiscount;

        // Update shipping cost display
        const shippingCostEl = $('#shipping-cost');
        if (shippingCost === 0) {
            shippingCostEl.html('<span class="text-success">GRATIS</span>');
        } else {
            shippingCostEl.html('<span class="text-dark">Rp ' + formatNumber(shippingCost) + '</span>');
        }

        // Update total
        $('#checkout-total').text('Rp ' + formatNumber(total));
        $('#hidden-shipping-cost').val(shippingCost);
        $('#hidden-total-harga').val(total);
        
        // Update point earned (1 point per 10,000 spent)
        const pointsEarned = Math.floor(currentSubtotal / 10000);
        $('#point-earned').html('<i class="fas fa-star me-1"></i>' + pointsEarned + ' point');
        $('#hidden-point-diperoleh').val(pointsEarned);
    }

    function formatNumber(num) {
        if (!num) return '0';
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function showError(message) {
        console.error('   Showing error:', message);
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('.alert-danger, .alert-success').remove();
        $('.container').first().prepend(alertHtml);
        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 500);
    }

    function showSuccess(message) {
        console.log('   Showing success:', message);
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('.alert-danger, .alert-success').remove();
        $('.container').first().prepend(alertHtml);
        // Auto hide after 3 seconds
        setTimeout(() => {
            $('.alert-success').fadeOut();
        }, 3000);
    }
});
</script>
@endsection
