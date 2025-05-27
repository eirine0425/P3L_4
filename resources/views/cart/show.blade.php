@extends('layouts.app')

@section('title', 'Detail Keranjang Belanja')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Keranjang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Keranjang</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-shopping-cart me-2 text-primary"></i>Detail Keranjang Belanja</h2>
                    <p class="text-muted mb-0">Review produk sebelum melanjutkan ke checkout</p>
                </div>
                <div>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-edit me-1"></i>Edit Keranjang
                    </a>
                    <a href="{{ url('/products') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Produk
                    </a>
                </div>
            </div>
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
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($cartItems->count() > 0)
        <div class="row">
            <!-- Cart Items Detail -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Produk ({{ $cartItems->count() }} item)</h5>
                            <span class="badge bg-light text-dark">Total: {{ $cartItems->sum('jumlah') }} pcs</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $index => $item)
                            <div class="border-bottom {{ $index === $cartItems->count() - 1 ? '' : 'border-bottom' }}">
                                <div class="p-4">
                                    <div class="row align-items-center">
                                        <!-- Product Image -->
                                        <div class="col-md-3 col-sm-4 mb-3 mb-md-0">
                                            <div class="position-relative">
                                                <img src="{{ $item->barang->foto ? asset('storage/' . $item->barang->foto) : asset('images/no-image.jpg') }}" 
                                                     alt="{{ $item->barang->nama_barang }}" 
                                                     class="img-fluid rounded shadow-sm"
                                                     style="width: 100%; height: 150px; object-fit: cover;">
                                                @if($item->barang->stok < 5)
                                                    <span class="position-absolute top-0 end-0 badge bg-warning m-1">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Stok Terbatas
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Product Details -->
                                        <div class="col-md-6 col-sm-8">
                                            <div class="product-details">
                                                <h5 class="mb-2">
                                                    <a href="{{ url('/products/' . $item->barang->barang_id) }}" 
                                                       class="text-decoration-none text-dark fw-bold">
                                                        {{ $item->barang->nama_barang }}
                                                    </a>
                                                </h5>
                                                
                                                <div class="mb-2">
                                                    <span class="badge bg-light text-dark me-2">
                                                        <i class="fas fa-tag me-1"></i>{{ $item->barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori' }}
                                                    </span>
                                                    <span class="badge bg-light text-dark me-2">
                                                        <i class="fas fa-info-circle me-1"></i>{{ $item->barang->kondisi ?? 'Baik' }}
                                                    </span>
                                                    @if($item->barang->garansi)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-shield-alt me-1"></i>Bergaransi
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <p class="text-muted mb-2">{{ Str::limit($item->barang->deskripsi, 100) }}</p>
                                                
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="text-muted me-3">
                                                        <i class="fas fa-cube me-1"></i>Stok: {{ $item->barang->stok }} pcs
                                                    </span>
                                                    @if($item->barang->berat)
                                                        <span class="text-muted me-3">
                                                            <i class="fas fa-weight me-1"></i>{{ $item->barang->berat }}g
                                                        </span>
                                                    @endif
                                                    @if($item->barang->penitip)
                                                        <span class="text-muted">
                                                            <i class="fas fa-user me-1"></i>{{ $item->barang->penitip->nama_penitip }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Price and Quantity -->
                                        <div class="col-md-3 text-md-end">
                                            <div class="price-section">
                                                <div class="mb-3">
                                                    <div class="text-muted small">Harga Satuan</div>
                                                    <div class="h5 text-primary mb-0">
                                                        Rp {{ number_format($item->barang->harga, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="text-muted small">Jumlah</div>
                                                    <div class="d-flex align-items-center justify-content-md-end">
                                                        <span class="badge bg-primary fs-6 px-3 py-2">
                                                            {{ $item->jumlah }} pcs
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="text-muted small">Subtotal</div>
                                                    <div class="h4 text-success mb-0 fw-bold">
                                                        Rp {{ number_format($item->barang->harga * $item->jumlah, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('cart.remove', $item->keranjang_id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-truck me-2 text-primary"></i>Pengiriman</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Gratis ongkir untuk pembelian di atas Rp 100.000</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Estimasi pengiriman 2-3 hari kerja</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Asuransi pengiriman tersedia</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-shield-alt me-2 text-primary"></i>Garansi & Retur</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Garansi produk sesuai ketentuan</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Retur dalam 7 hari jika ada kerusakan</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pengembalian 100% jika tidak sesuai</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Items Summary -->
                        <div class="mb-3">
                            <h6 class="mb-3">Detail Harga</h6>
                            @foreach($cartItems as $item)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">{{ Str::limit($item->barang->nama_barang, 20) }} ({{ $item->jumlah }}x)</span>
                                    <span>Rp {{ number_format($item->barang->harga * $item->jumlah, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <hr>
                        
                        <!-- Price Breakdown -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal ({{ $cartItems->sum('jumlah') }} item)</span>
                                <span class="fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Biaya Pengiriman</span>
                                <span class="text-muted">Dihitung saat checkout</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Biaya Layanan</span>
                                <span>Gratis</span>
                            </div>
                            @if($subtotal >= 100000)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success">Diskon Ongkir</span>
                                    <span class="text-success">- Gratis</span>
                                </div>
                            @endif
                        </div>
                        
                        <hr>
                        
                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold">Total Belanja</span>
                            <span class="h4 fw-bold text-success">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Promo Code -->
                        <div class="mb-4">
                            <label for="promo-code" class="form-label">Kode Promo</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promo-code" placeholder="Masukkan kode promo">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-tag me-1"></i>Terapkan
                                </button>
                            </div>
                            <small class="text-muted">Gunakan kode NEWUSER untuk diskon 10%</small>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <a href="{{ url('/checkout') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout
                            </a>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Keranjang
                            </a>
                            <a href="{{ url('/products') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-bag me-2"></i>Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Security Info -->
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-lock fa-2x text-success"></i>
                        </div>
                        <h6>Transaksi Aman</h6>
                        <p class="text-muted small mb-0">
                            Data Anda dilindungi dengan enkripsi SSL 256-bit dan sistem keamanan berlapis.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recently Viewed Products -->
        @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
            <div class="mt-5">
                <h3 class="mb-4"><i class="fas fa-history me-2"></i>Produk yang Baru Dilihat</h3>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
                    @foreach($recentlyViewed as $product)
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/no-image.jpg') }}" 
                                     class="card-img-top" alt="{{ $product->nama_barang }}" 
                                     style="height: 120px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <h6 class="card-title small">{{ Str::limit($product->nama_barang, 30) }}</h6>
                                    <p class="card-text text-success fw-bold small mb-2">
                                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                                    </p>
                                    <a href="{{ url('/products/' . $product->barang_id) }}" class="btn btn-sm btn-outline-primary w-100">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
    @else
        <!-- Empty Cart -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                </div>
                <h3 class="mb-3">Keranjang Belanja Kosong</h3>
                <p class="text-muted mb-4">Anda belum menambahkan produk apapun ke keranjang belanja.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ url('/products') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .product-details h5 a:hover {
        color: #007bff !important;
    }
    
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .sticky-top {
        z-index: 1020;
    }
    
    @media (max-width: 768px) {
        .sticky-top {
            position: relative !important;
            top: auto !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Promo code functionality
        $('#promo-code').on('keypress', function(e) {
            if (e.which === 13) {
                applyPromoCode();
            }
        });
        
        $('.btn-outline-secondary').click(function() {
            applyPromoCode();
        });
        
        function applyPromoCode() {
            const promoCode = $('#promo-code').val().trim();
            
            if (promoCode === '') {
                alert('Silakan masukkan kode promo terlebih dahulu.');
                return;
            }
            
            // Here you can implement AJAX call to validate and apply promo code
            // For now, we'll just show a demo message
            if (promoCode.toLowerCase() === 'newuser') {
                alert('Kode promo berhasil diterapkan! Diskon 10% akan diterapkan saat checkout.');
                $('#promo-code').addClass('is-valid');
            } else {
                alert('Kode promo tidak valid atau sudah kadaluarsa.');
                $('#promo-code').addClass('is-invalid');
            }
        }
        
        // Remove validation classes when typing
        $('#promo-code').on('input', function() {
            $(this).removeClass('is-valid is-invalid');
        });
        
        // Smooth scroll to top when page loads
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });
</script>
@endpush
