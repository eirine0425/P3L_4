@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="text-center mb-4">
        <h2><i class="fas fa-box-open me-2"></i>Produk</h2>
        <p>Menampilkan 1–12 dari 100 produk</p>
    </div>

    <!-- Product Grid -->
    <div class="row justify-content-center g-4">
        @php
    $images = [
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
        asset('assets/laptop.jpg'),
    ];
@endphp

        @for ($i = 1; $i <= 12; $i++)
        <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
            <div class="card h-100 w-100 shadow-sm">
                <div class="position-relative">
                     <img src="{{ asset('assets/laptop.jpg') }}" class="card-img-top" alt="Product {{ $i }}">
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Tersedia</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Produk {{ $i }}</h5>
                    <p class="card-text text-center small">Deskripsi singkat produk {{ $i }} yang menjelaskan kondisi dan kualitas barang.</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Rp {{ number_format(rand(100000, 1000000), 0, ',', '.') }}</span>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-1 small">({{ rand(10, 50) }})</span>

<div class="container">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter</h5>
                </div>
                <div class="card-body">
                    <form action="{{ url('/products') }}" method="GET">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Produk</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama produk...">
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
                            <div class="row">
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
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            <a href="{{ url('/products') }}" class="btn btn-outline-secondary">Reset Filter</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Product Listing -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Produk</h2>
                <span>Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</span>
            </div>
            
            @if($products->count() > 0)
                <div class="row">
                    @foreach ($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                @if($product->foto_barang)
                                    <img src="{{ asset('storage/' . $product->foto_barang) }}" class="card-img-top" alt="{{ $product->nama_barang }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="{{ $product->nama_barang }}" style="height: 200px; object-fit: cover;">
                                @endif
                                
                                <span class="badge bg-{{ $product->status == 'belum_terjual' ? 'success' : ($product->status == 'terjual' ? 'warning' : 'secondary') }} position-absolute top-0 end-0 m-2">
                                    @if($product->status == 'belum_terjual')
                                        Tersedia
                                    @elseif($product->status == 'terjual')
                                        Terjual
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                                    @endif
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
                                        Kategori: {{ $product->kategori->nama_kategori ?? 'Tidak ada kategori' }}
                                    </small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                                    <div>
                                        @if($product->rating && $product->rating > 0)
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
                                        @if(auth()->user()->role->nama_role == 'Pembeli' && $product->status == 'belum_terjual')
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->barang_id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary w-100">Tambah ke Keranjang</button>
                                        </form>
                                        @endif
                                    @endauth
                                </div>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>

                <div class="card-footer bg-white border-top-0">
                    <div class="d-grid gap-2">
                        <a href="{{ url('/products/' . $i) }}" class="btn btn-outline-success">Detail</a>
                        @auth
                            @if(auth()->user()->role->nama_role == 'Pembeli')
                            <button class="btn btn-primary add-to-cart" data-product-id="{{ $i }}">
                                <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                            </button>
                            @endif
                        @endauth

            @else
                <div class="text-center py-5">
                    <h4 class="text-muted">Tidak ada produk ditemukan</h4>
                    <p class="text-muted">Coba ubah filter pencarian Anda atau <a href="{{ url('/products') }}">lihat semua produk</a>.</p>
                    
                    @if(config('app.debug'))
                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Debug Info:</strong><br>
                            Total barang di database: {{ \App\Models\Barang::count() }}<br>
                            Barang dengan status 'belum_terjual': {{ \App\Models\Barang::where('status', 'belum_terjual')->count() }}<br>
                            Kategori tersedia: {{ $categories->count() }}
                        </small>

                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Berhasil</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            {{ session('error') }}
        </div>
    </div>
</div>
@endif
@endsection
