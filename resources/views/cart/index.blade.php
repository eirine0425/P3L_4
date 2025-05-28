@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>Keranjang Belanja</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

@if(count($cartItems) > 0)
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
            </h5>
        </div>
        <div class="card-body">
            <form id="cart-form">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Produk</th>
                                <th width="150">Harga</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" 
                                               value="{{ $item->keranjang_id }}" 
                                               data-price="{{ $item->barang->harga ?? 0 }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->barang && $item->barang->foto_barang ? asset('storage/' . $item->barang->foto_barang) : '/placeholder.svg?height=60&width=60' }}" 
                                                 alt="{{ $item->barang->nama_barang ?? 'Produk' }}" 
                                                 class="img-thumbnail me-3" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $item->barang->nama_barang ?? 'Nama tidak tersedia' }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $item->barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori' }}

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Keranjang Belanja</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h2>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(isset($cartItems) && $cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Produk ({{ $cartItems->count() }} item)</h5>
                        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Apakah Anda yakin ingin mengosongkan keranjang?')">
                                <i class="fas fa-trash-alt me-1"></i> Kosongkan Keranjang
                            </button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $item)
                            <div class="border-bottom p-3" id="cart-item-{{ $item->id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->barang->foto ? asset('storage/' . $item->barang->foto) : 'https://via.placeholder.com/100x100' }}" 
                                                 alt="{{ $item->barang->nama_barang }}" 
                                                 class="img-thumbnail me-3" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $item->barang->nama_barang }}</h6>
                                                <small class="text-muted">
                                                    Kategori: {{ $item->barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori' }}

                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    Kondisi: {{ $item->barang->kondisi ?? 'Baik' }}
                                                </small>

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            Rp {{ number_format($item->barang->harga ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('cart.remove', $item->keranjang_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Hapus item ini dari keranjang?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Ringkasan Belanja -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">Item dipilih: </span>
                            <span class="fw-bold" id="selected-count">0</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllCart()">
                                <i class="fas fa-trash me-1"></i>Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Estimasi Ongkir:</span>
                                <span class="text-muted">Dihitung di checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">Total Sementara:</span>
                                <span class="fw-bold text-primary" id="total">Rp 0</span>
                            </div>

                                                <br>
                                                <small class="text-success">
                                                    Status: {{ $item->barang->status }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="fw-bold">
                                            Rp {{ number_format($item->barang->harga, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="fw-bold text-muted">
                                            Qty: 1
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="fw-bold text-primary item-total" id="total-{{ $item->id }}">
                                            Rp {{ number_format($item->barang->harga, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-auto text-center mt-2 mt-md-0">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('products.show', $item->barang->barang_id) }}" 
                                               class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')"
                                                        title="Hapus dari Keranjang">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Lanjutkan Belanja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal ({{ $cartItems->count() }} produk)</span>
                            <span class="fw-bold" id="cart-subtotal">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span class="text-muted">Dihitung saat checkout</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Diskon</span>
                            <span class="text-success">- Rp 0</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-6">Total</span>
                            <span class="fw-bold fs-5 text-primary" id="cart-total">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="promo" class="form-label">Kode Promo</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promo" placeholder="Masukkan kode promo">
                                <button class="btn btn-outline-secondary" type="button">Terapkan</button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('checkout.index') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-bag me-2"></i>Checkout
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Jaminan ReuseMart</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>100% Produk Original</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Garansi Uang Kembali</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Pengiriman Aman & Cepat</small>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-grid gap-2">
                        <button type="button" id="checkout-btn" class="btn btn-success btn-lg" disabled>
                            <i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout
                        </button>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-edit me-2"></i>Refresh Keranjang
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-shopping-bag me-2"></i>Lanjut Belanja
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h4 class="text-muted mb-3">Keranjang Belanja Kosong</h4>
            <p class="text-muted mb-4">Belum ada produk dalam keranjang belanja Anda</p>
            
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    </div>
@endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('Cart script loaded'); // Debug log

$(document).ready(function() {
    console.log('Document ready'); // Debug log
    console.log('Checkout button found:', $('#checkout-btn').length); // Debug log
    console.log('Item checkboxes found:', $('.item-checkbox').length); // Debug log

    // Handle select all checkbox
    $('#select-all').on('change', function() {
        console.log('Select all clicked'); // Debug log
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
        updateCheckoutButton();
        updateSummary();
    });

    // Handle individual checkbox changes
    $(document).on('change', '.item-checkbox', function() {
        console.log('Item checkbox changed'); // Debug log
        updateSelectAllState();
        updateCheckoutButton();
        updateSummary();
    });

    // Handle checkout button click
    $('#checkout-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Checkout button clicked!'); // Debug log
        
        const selectedItems = [];
        $('.item-checkbox:checked').each(function() {
            selectedItems.push($(this).val());
        });
        
        console.log('Selected items:', selectedItems); // Debug log
        
        if (selectedItems.length === 0) {
            alert('Pilih minimal satu item untuk checkout');
            return;
        }
        
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
        
        // Method 1: Try direct redirect with query params
        const queryParams = new URLSearchParams();
        queryParams.append('selected_items', JSON.stringify(selectedItems));
        
        console.log('Redirecting to checkout...'); // Debug log
        window.location.href = '/checkout?' + queryParams.toString();
    });

    // Test button click handler
    $('#checkout-btn').on('click', function() {
        console.log('Button clicked - any handler'); // Debug log
    });

    // Initialize on page load
    updateCheckoutButton();
    updateSummary();
});

function updateSelectAllState() {
    const totalCheckboxes = $('.item-checkbox').length;
    const checkedCheckboxes = $('.item-checkbox:checked').length;
    
    $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
}

function updateCheckoutButton() {
    const checkedItems = $('.item-checkbox:checked').length;
    const checkoutBtn = $('#checkout-btn');
    
    console.log('Updating checkout button, checked items:', checkedItems); // Debug log
    
    if (checkedItems > 0) {
        checkoutBtn.prop('disabled', false);
        checkoutBtn.html(`<i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout (${checkedItems} item)`);
    } else {
        checkoutBtn.prop('disabled', true);
        checkoutBtn.html('<i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout');
    }
}

function updateSummary() {
    let totalPrice = 0;
    let selectedCount = 0;
    
    $('.item-checkbox:checked').each(function() {
        const price = parseFloat($(this).data('price')) || 0;
        totalPrice += price;
        selectedCount++;
    });
    
    $('#selected-count').text(selectedCount);
    $('#subtotal').text('Rp ' + formatNumber(totalPrice));
    $('#total').text('Rp ' + formatNumber(totalPrice));
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function clearAllCart() {
    if (confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang?')) {
        window.location.href = '{{ route("cart.clear") }}';
    }
}

// Test function - call this in console
function testCheckout() {
    console.log('Testing checkout function');
    $('#checkout-btn').click();
}

        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3 class="mb-3">Keranjang Belanja Kosong</h3>
                <p class="text-muted mb-4">Anda belum menambahkan produk apapun ke keranjang belanja.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                </a>
                
                @if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
                    <div class="mt-5">
                        <h5 class="mb-3">Produk Rekomendasi</h5>
                        <div class="row">
                            @foreach($recommendedProducts as $product)
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ $product->foto ? asset('storage/' . $product->foto) : 'https://via.placeholder.com/200x150' }}" 
                                             class="card-img-top" alt="{{ $product->nama_barang }}" style="height: 150px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">{{ Str::limit($product->nama_barang, 50) }}</h6>
                                            <p class="card-text text-primary fw-bold">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                                            <a href="{{ route('products.show', $product->barang_id) }}" class="btn btn-outline-primary btn-sm mt-auto">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to show message
        function showMessage(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert at the top
            $('.container').prepend(alertHtml);
            
            // Auto dismiss after 3 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 3000);
        }
    });

</script>
@endpush