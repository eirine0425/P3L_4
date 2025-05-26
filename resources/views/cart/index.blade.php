@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
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
@endsection
