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
</script>
@endpush