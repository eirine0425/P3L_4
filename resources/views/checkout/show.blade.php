@extends('layouts.app')

@section('title', 'Checkout - ReuseMart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>
                    Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('cart.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Keranjang</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Checkout</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-shopping-cart text-blue-600 mr-3"></i>
            Checkout
        </h1>
        <p class="text-gray-600">Tinjau pesanan Anda dan lengkapi pembayaran</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items to Checkout -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Item yang Akan Dibeli
                </h2>
                
                <div id="checkout-items" class="space-y-4">
                    @if($cartItems->count() > 0)
                        @foreach($cartItems as $item)
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @if($item->barang->foto_barang)
                                    <img src="{{ asset('storage/' . $item->barang->foto_barang) }}" 
                                         alt="{{ $item->barang->nama_barang }}" 
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item->barang->nama_barang }}</h3>
                                <p class="text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                        {{ $item->barang->kategori->nama_kategori ?? 'Kategori' }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($item->barang->kondisi) }}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Jumlah: {{ $item->jumlah }}</p>
                            </div>
                            
                            <!-- Price -->
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">
                                    Rp {{ number_format($item->barang->harga, 0, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Total: Rp {{ number_format($item->barang->harga * $item->jumlah, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada item untuk checkout</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Alamat Pengiriman
                </h2>
                
                @if($defaultAlamat)
                    <div id="selected-address" class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $defaultAlamat->nama_penerima }}</h3>
                                <p class="text-gray-600">{{ $defaultAlamat->no_telepon }}</p>
                                <p class="text-gray-600 mt-2">{{ $defaultAlamat->alamat }}</p>
                                <p class="text-gray-600">{{ $defaultAlamat->kota }}, {{ $defaultAlamat->provinsi }} {{ $defaultAlamat->kode_pos }}</p>
                                @if($defaultAlamat->status_default == 'Y')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                        <i class="fas fa-check mr-1"></i>
                                        Alamat Utama
                                    </span>
                                @endif
                            </div>
                            <button type="button" 
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                    onclick="openAddressModal()">
                                Ubah Alamat
                            </button>
                        </div>
                    </div>
                @else
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <i class="fas fa-map-marker-alt text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500 mb-4">Belum ada alamat pengiriman</p>
                        <button type="button" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                                onclick="openAddressModal()">
                            <i class="fas fa-plus mr-2"></i>
                            Pilih Alamat
                        </button>
                    </div>
                @endif
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                    Metode Pembayaran
                </h2>
                
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="transfer" class="mr-3" checked>
                        <div class="flex items-center">
                            <i class="fas fa-university text-blue-600 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">Transfer Bank</p>
                                <p class="text-sm text-gray-600">Transfer ke rekening ReuseMart</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="cod" class="mr-3">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave text-green-600 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">COD (Cash on Delivery)</p>
                                <p class="text-sm text-gray-600">Bayar saat barang diterima</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-receipt text-blue-600 mr-2"></i>
                    Ringkasan Pesanan
                </h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} item)</span>
                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ongkos Kirim</span>
                        <span class="font-medium">
                            @if($shippingCost == 0)
                                <span class="text-green-600">GRATIS</span>
                            @else
                                Rp {{ number_format($shippingCost, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                    
                    @if($subtotal > 1500000)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm text-green-800 flex items-center">
                                <i class="fas fa-gift mr-2"></i>
                                Selamat! Anda mendapat gratis ongkir
                            </p>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Belanja Rp {{ number_format(1500000 - $subtotal, 0, ',', '.') }} lagi untuk gratis ongkir
                            </p>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Admin</span>
                        <span class="font-medium">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                    </div>
                    
                    <hr class="border-gray-200">
                    
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span>
                        <span class="text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="selected_items" value="{{ implode(',', $selectedItems) }}">
                    <input type="hidden" name="alamat_id" id="alamat_id" value="{{ $defaultAlamat->alamat_id ?? '' }}">
                    <input type="hidden" name="payment_method" id="payment_method_input" value="transfer">
                    <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                    <input type="hidden" name="shipping_cost" value="{{ $shippingCost }}">
                    <input type="hidden" name="admin_fee" value="{{ $adminFee }}">
                    <input type="hidden" name="total" value="{{ $total }}">
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                            id="checkout-btn"
                            {{ !$defaultAlamat ? 'disabled' : '' }}>
                        <i class="fas fa-lock mr-2"></i>
                        Bayar Sekarang
                    </button>
                </form>
                
                <p class="text-xs text-gray-500 text-center mt-3">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Transaksi Anda aman dan terlindungi
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Address Selection Modal -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Pilih Alamat Pengiriman</h3>
                    <button type="button" onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="address-list" class="space-y-3">
                    @foreach($alamats as $alamat)
                    <div class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 address-option" 
                         data-alamat-id="{{ $alamat->alamat_id }}"
                         data-alamat-data="{{ json_encode($alamat) }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $alamat->nama_penerima }}</h4>
                                <p class="text-gray-600">{{ $alamat->no_telepon }}</p>
                                <p class="text-gray-600 mt-1">{{ $alamat->alamat }}</p>
                                <p class="text-gray-600">{{ $alamat->kota }}, {{ $alamat->provinsi }} {{ $alamat->kode_pos }}</p>
                                @if($alamat->status_default == 'Y')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                        <i class="fas fa-check mr-1"></i>
                                        Alamat Utama
                                    </span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <input type="radio" name="selected_alamat" value="{{ $alamat->alamat_id }}" 
                                       {{ $alamat->status_default == 'Y' ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6 flex justify-between">
                    <a href="{{ route('buyer.alamat.create') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Alamat Baru
                    </a>
                    
                    <div class="space-x-3">
                        <button type="button" 
                                onclick="closeAddressModal()" 
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="button" 
                                onclick="selectAddress()" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Pilih Alamat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentMethodInput = document.getElementById('payment_method_input');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            paymentMethodInput.value = this.value;
        });
    });
    
    // Form validation
    const checkoutForm = document.getElementById('checkout-form');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    checkoutForm.addEventListener('submit', function(e) {
        const alamatId = document.getElementById('alamat_id').value;
        
        if (!alamatId) {
            e.preventDefault();
            alert('Silakan pilih alamat pengiriman terlebih dahulu');
            return false;
        }
        
        // Show loading state
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    });
});

function openAddressModal() {
    document.getElementById('addressModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function selectAddress() {
    const selectedRadio = document.querySelector('input[name="selected_alamat"]:checked');
    
    if (!selectedRadio) {
        alert('Silakan pilih alamat terlebih dahulu');
        return;
    }
    
    const alamatId = selectedRadio.value;
    const addressOption = document.querySelector(`[data-alamat-id="${alamatId}"]`);
    const alamatData = JSON.parse(addressOption.dataset.alamatData);
    
    // Update selected address display
    const selectedAddressDiv = document.getElementById('selected-address');
    selectedAddressDiv.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-semibold text-gray-900">${alamatData.nama_penerima}</h3>
                <p class="text-gray-600">${alamatData.no_telepon}</p>
                <p class="text-gray-600 mt-2">${alamatData.alamat}</p>
                <p class="text-gray-600">${alamatData.kota}, ${alamatData.provinsi} ${alamatData.kode_pos}</p>
                ${alamatData.status_default == 'Y' ? `
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                        <i class="fas fa-check mr-1"></i>
                        Alamat Utama
                    </span>
                ` : ''}
            </div>
            <button type="button" 
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                    onclick="openAddressModal()">
                Ubah Alamat
            </button>
        </div>
    `;
    
    // Update hidden input
    document.getElementById('alamat_id').value = alamatId;
    
    // Enable checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.disabled = false;
    
    closeAddressModal();
}

// Address option click handler
document.addEventListener('click', function(e) {
    if (e.target.closest('.address-option')) {
        const addressOption = e.target.closest('.address-option');
        const alamatId = addressOption.dataset.alamatId;
        const radio = addressOption.querySelector('input[type="radio"]');
        radio.checked = true;
    }
});
</script>
@endpush
@endsection
