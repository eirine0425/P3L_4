@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="row">
    <!-- Sidebar Filter -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter</h5>
            </div>
            <div class="card-body">
                <form action="{{ url('/products') }}" method="GET">
                    <!-- Search -->
                    <div class="mb-3">
                        <label for="search" class="form-label">Cari Produk</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama produk...">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        @foreach ($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->nama_kategori }}" id="category-{{ $category->id }}" {{ in_array($category->nama_kategori, request('categories', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="category-{{ $category->id }}">
                                {{ $category->nama_kategori }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Condition -->
                    <div class="mb-3">
                        <label class="form-label">Kondisi</label>
                        @php
                        $conditions = [
                            'baru' => 'Baru',
                            'sangat_layak' => 'Sangat Layak',
                            'layak' => 'Layak'
                        ];
                        @endphp
                        
                        @foreach ($conditions as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="conditions[]" value="{{ $value }}" id="condition-{{ $loop->index }}" {{ in_array($value, request('conditions', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="condition-{{ $loop->index }}">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-3">
                        <label class="form-label">Rentang Harga</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rating -->
                    <div class="mb-3">
                        <label class="form-label">Rating Minimum</label>
                        <select class="form-select" name="rating">
                            <option value="">Semua Rating</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Bintang</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Bintang</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ Bintang</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1+ Bintang</option>
                        </select>
                    </div>
                    
                    <!-- Sort -->
                    <div class="mb-3">
                        <label class="form-label">Urutkan</label>
                        <select class="form-select" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Terapkan Filter
                        </button>
                        <a href="{{ url('/products') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Product Listing -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-box-open me-2"></i>Produk</h2>
            <span>Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</span>
        </div>
        
        @if($products->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                            <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="{{ $product->nama_barang }}">
                            <span class="badge bg-{{ $product->status == 'tersedia' ? 'success' : 'secondary' }} position-absolute top-0 end-0 m-2">
                                {{ ucfirst($product->status) }}
                            </span>
                            @if($product->kondisi)
                                <span class="badge bg-info position-absolute top-0 start-0 m-2">
                                    {{ ucwords(str_replace('_', ' ', $product->kondisi)) }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->nama_barang }}</h5>
                            <p class="card-text">{{ Str::limit($product->deskripsi, 100) }}</p>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i>{{ $product->kategori->nama_kategori ?? 'Tidak ada kategori' }}
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                                <div>
                                    @if($product->rating)
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($product->rating))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($i <= $product->rating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-1">({{ number_format($product->rating, 1) }})</span>
                                    @else
                                        <span class="text-muted">Belum ada rating</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-grid gap-2">
                                <a href="{{ url('/products/' . $product->barang_id) }}" class="btn btn-outline-primary">Detail</a>
                                @auth
                                    @if(auth()->user()->role->nama_role == 'Pembeli')
                                    <button class="btn btn-primary add-to-cart" data-product-id="{{ $product->barang_id }}">
                                        <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                    </button>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Tidak ada produk ditemukan</h4>
                <p class="text-muted">Coba ubah filter pencarian Anda atau <a href="{{ url('/products') }}">lihat semua produk</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            
            // You would typically make an AJAX request to add the item to the cart
            // For demonstration, we'll just show an alert
            
            // Create a toast notification
            const toastContainer = document.createElement('div');
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '11';
            
            toastContainer.innerHTML = `
                <div id="liveToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong class="me-auto">Berhasil</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Produk berhasil ditambahkan ke keranjang.
                    </div>
                </div>
            `;
            
            document.body.appendChild(toastContainer);
            
            // Remove the toast after 3 seconds
            setTimeout(() => {
                toastContainer.remove();
            }, 3000);
        });
    });
</script>
@endpush