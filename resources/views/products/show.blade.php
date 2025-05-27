@extends('layouts.app')

@section('title', $product->nama_barang ?? 'Detail Produk')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/products') }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->nama_barang ?? 'Detail Produk' }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                </div>
                <div class="carousel-inner rounded-3">
                    <div class="carousel-item active">

                        <img src="{{ asset('assets/laptop.jpg') }}" class="d-block w-100" alt="Product Image 1">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/laptop.jpg') }}" class="d-block w-100" alt="Product Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/laptop.jpg') }}" class="d-block w-100" alt="Product Image 3">

                        <img src="{{ $product->photo_url ?? '/placeholder.svg?height=600&width=600&text=' . urlencode($product->nama_barang ?? 'Produk') }}" 
                             class="d-block w-100" alt="{{ $product->nama_barang ?? 'Product Image' }}" style="height: 400px; object-fit: cover;">

                    </div>
                </div>
            </div>
            
            <div class="row mt-3">

                <div class="col-4">
                    <img src="{{ asset('assets/laptop.jpg') }}" class="img-thumbnail" alt="Thumbnail 1" data-bs-target="#productCarousel" data-bs-slide-to="0">
                </div>
                <div class="col-4">
                    <img src="{{ asset('assets/laptop.jpg') }}" class="img-thumbnail" alt="Thumbnail 2" data-bs-target="#productCarousel" data-bs-slide-to="1">
                </div>
                <div class="col-4">
                    <img src="{{ asset('assets/laptop.jpg') }}" class="img-thumbnail" alt="Thumbnail 3" data-bs-target="#productCarousel" data-bs-slide-to="2">

                <div class="col-12">
                    <img src="{{ $product->photo_url ?? '/placeholder.svg?height=600&width=600&text=' . urlencode($product->nama_barang ?? 'Produk') }}" 
                         class="img-thumbnail w-100" alt="Product Thumbnail" style="height: 100px; object-fit: cover;">

                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">{{ $product->nama_barang ?? 'Nama Produk Tidak Tersedia' }}</h2>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        @php
                            $rating = $product->rating ?? 0;
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        @endphp
                        
                        @for($i = 0; $i < $fullStars; $i++)
                            <i class="fas fa-star text-warning"></i>
                        @endfor
                        
                        @if($hasHalfStar)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @endif
                        
                        @for($i = 0; $i < $emptyStars; $i++)
                            <i class="far fa-star text-warning"></i>
                        @endfor
                        
                        <span class="ms-1">({{ $product->jumlah_ulasan ?? 0 }} ulasan)</span>
                    </div>
                    <span class="badge {{ $product->getStatusBadgeClass() ?? 'bg-secondary' }}">
                        {{ $product->getStatusDisplayText() ?? 'Status Tidak Diketahui' }}
                    </span>
                </div>
                
                <h3 class="text-primary mb-3">{{ $product->formatted_price ?? 'Harga Tidak Tersedia' }}</h3>
                
                <div class="mb-3">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi Produk</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="150">Kondisi</td>
                            <td>: <span class="badge {{ $product->getConditionBadgeClass() ?? 'bg-secondary' }}">{{ ucfirst($product->kondisi ?? 'Tidak Diketahui') }}</span></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>: {{ $product->kategori->nama_kategori ?? 'Tidak Berkategori' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Masuk</td>
                            <td>: {{ $product->tanggal_penitipan ? \Carbon\Carbon::parse($product->tanggal_penitipan)->format('d M Y') : 'Tidak Diketahui' }}</td>
                        </tr>
                        <tr>
                            <td>Garansi</td>
                            <td>: 
                                @if($product->garansi)
                                    <span class="badge bg-success">Bergaransi</span>
                                @else
                                    <span class="badge bg-warning">Tidak Bergaransi</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                @if($product->penitip)
                <div class="mb-4">
                    <h5><i class="fas fa-user-tag me-2"></i>Informasi Penitip</h5>
                    <div class="d-flex align-items-center">
                        <img src="/placeholder.svg?height=50&width=50&text={{ urlencode(substr($product->penitip->nama_penitip ?? 'P', 0, 1)) }}" 
                             class="rounded-circle me-3" alt="Seller">
                        <div>
                            <h6 class="mb-1">{{ $product->penitip->nama_penitip ?? 'Nama Penitip Tidak Tersedia' }}</h6>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-1">(Rating Penitip)</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Debug Information (remove this in production) -->
                @if(config('app.debug'))
                <div class="alert alert-info">
                    <strong>Debug Info:</strong><br>
                    Auth Status: {{ Auth::check() ? 'Logged In' : 'Not Logged In' }}<br>
                    @auth
                    User Role: {{ auth()->user()->role->nama_role ?? 'No Role' }}<br>
                    User ID: {{ auth()->user()->id }}<br>
                    @endauth
                    Product ID: {{ $product->barang_id ?? 'No Product ID' }}<br>
                    Product Status: {{ $product->status ?? 'No Status' }}<br>
                    Product Available: {{ $product->isAvailable() ? 'Yes' : 'No' }}
                </div>
                @endif

                <!-- Cart Actions -->
                <div class="cart-actions">
                    @auth
                        @php
                            $userRole = strtolower(auth()->user()->role->nama_role ?? '');
                        @endphp
                        
                        @if($userRole === 'pembeli')
                            @if($product->isAvailable())
                                <!-- Pembeli - Show Cart Actions -->
                                <div class="d-grid gap-2">
                                    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="barang_id" value="{{ $product->barang_id }}">
                                        <input type="hidden" name="redirect_to_cart" value="1">
                                        <button type="submit" class="btn btn-success btn-lg" id="addToCartBtn">
                                            <i class="fas fa-shopping-cart me-2"></i>Tambah ke Keranjang
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-success btn-lg" onclick="buyNow()">
                                        <i class="fas fa-shopping-bag me-2"></i>Beli Sekarang
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Produk ini sudah tidak tersedia.
                                </div>
                            @endif
                        @else
                            <!-- Non-Pembeli Users -->
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>Fitur ini hanya tersedia untuk pembeli. Role Anda: {{ $userRole }}
                            </div>
                        @endif
                    @else
                        <!-- Not Authenticated -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Silakan <a href="{{ route('login') }}">login</a> untuk melakukan pembelian.
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Deskripsi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Ulasan ({{ $product->jumlah_ulasan ?? 0 }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="discussion-tab" data-bs-toggle="tab" data-bs-target="#discussion" type="button" role="tab" aria-controls="discussion" aria-selected="false">Diskusi</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <h4>Deskripsi Produk</h4>
                        <div class="description-content">
                            @if($product->deskripsi)
                                {!! nl2br(e($product->deskripsi)) !!}
                            @else
                                <p class="text-muted">Deskripsi produk belum tersedia.</p>
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <h5>Informasi Tambahan</h5>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th width="200">Nama Produk</th>
                                        <td>{{ $product->nama_barang ?? 'Tidak tersedia' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi</th>
                                        <td>{{ ucfirst($product->kondisi ?? 'Tidak diketahui') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>{{ $product->kategori->nama_kategori ?? 'Tidak berkategori' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $product->getStatusDisplayText() ?? 'Tidak diketahui' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Penitipan</th>
                                        <td>{{ $product->tanggal_penitipan ? \Carbon\Carbon::parse($product->tanggal_penitipan)->format('d M Y') : 'Tidak diketahui' }}</td>
                                    </tr>
                                    @if($product->garansi)
                                    <tr>
                                        <th>Garansi</th>
                                        <td>Tersedia</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4>Ulasan Produk</h4>
                            <div>
                                <span class="fs-4 fw-bold">{{ number_format($product->rating ?? 0, 1) }}</span>
                                @php
                                    $rating = $product->rating ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                @endphp
                                
                                @for($i = 0; $i < $fullStars; $i++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                
                                @if($hasHalfStar)
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                @endif
                                
                                @for($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star text-warning"></i>
                                @endfor
                                
                                <span class="ms-1">({{ $product->jumlah_ulasan ?? 0 }} ulasan)</span>
                            </div>
                        </div>
                        
                        @if(($product->jumlah_ulasan ?? 0) > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Fitur ulasan akan segera tersedia.
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-star-o fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada ulasan</h5>
                                <p class="text-muted">Jadilah yang pertama memberikan ulasan untuk produk ini.</p>
                            </div>
                        @endif
                        
                        @auth
                            @if(auth()->user()->role->nama_role == 'Pembeli')
                            <div class="mt-4">
                                <h5>Berikan Ulasan</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Fitur memberikan ulasan akan segera tersedia.
                                </div>
                            </div>
                            @endif
                        @endauth
                    </div>
                    
                    <div class="tab-pane fade" id="discussion" role="tabpanel" aria-labelledby="discussion-tab">
                        <h4>Diskusi Produk</h4>
                        
                        <div class="discussion-list mb-4">
                            @if($product->diskusi && $product->diskusi->count() > 0)
                                @foreach($product->diskusi->take(5) as $diskusi)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <img src="/placeholder.svg?height=40&width=40&text={{ urlencode(substr($diskusi->user->name ?? 'U', 0, 1)) }}" 
                                                 class="rounded-circle me-2" alt="User">
                                            <div>
                                                <h6 class="mb-0">{{ $diskusi->user->name ?? 'Pengguna' }}</h6>
                                                <small class="text-muted">{{ $diskusi->tanggal_diskusi ? \Carbon\Carbon::parse($diskusi->tanggal_diskusi)->format('d M Y H:i') : 'Tanggal tidak tersedia' }}</small>
                                                <p class="mt-2">{{ $diskusi->isi_diskusi ?? 'Isi diskusi tidak tersedia' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada diskusi</h5>
                                    <p class="text-muted">Jadilah yang pertama mengajukan pertanyaan tentang produk ini.</p>
                                </div>
                            @endif
                        </div>
                        
                        @auth
                        <div class="card">
                            <div class="card-body">
                                <h5>Ajukan Pertanyaan</h5>
                                <form action="{{ route('product.discussion.store', $product->barang_id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control" id="question" name="question" rows="3" placeholder="Tulis pertanyaan Anda tentang produk ini..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Kirim Pertanyaan</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Silakan <a href="{{ route('login') }}">login</a> untuk mengajukan pertanyaan.
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
@if($relatedProducts && $relatedProducts->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <h3 class="mb-4"><i class="fas fa-tags me-2"></i>Produk Terkait</h3>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        <img src="{{ $relatedProduct->photo_url ?? '/placeholder.svg?height=300&width=300&text=' . urlencode($relatedProduct->nama_barang) }}" 
                             class="card-img-top" alt="{{ $relatedProduct->nama_barang }}" style="height: 200px; object-fit: cover;">
                        <span class="badge {{ $relatedProduct->getStatusBadgeClass() }} position-absolute top-0 end-0 m-2">
                            {{ $relatedProduct->getStatusDisplayText() }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($relatedProduct->nama_barang, 50) }}</h5>
                        <p class="card-text">{{ Str::limit($relatedProduct->deskripsi ?? 'Deskripsi tidak tersedia', 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">{{ $relatedProduct->formatted_price }}</span>
                            <div>
                                @php
                                    $rating = $relatedProduct->rating ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                @endphp
                                
                                @for($i = 0; $i < $fullStars; $i++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                
                                @if($hasHalfStar)
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                @endif
                                
                                @for($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star text-warning"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid">
                            <a href="{{ route('products.show', $relatedProduct->barang_id) }}" class="btn btn-outline-primary">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Buy now function
    function buyNow() {
        // Add to cart first, then redirect to checkout
        const form = document.getElementById('add-to-cart-form');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = "{{ route('checkout.index') }}";
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: redirect to checkout anyway
            window.location.href = "{{ route('checkout.index') }}";
        });
    }

    // Handle form submission with AJAX for better UX
    document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('add-to-cart-btn');
        const originalText = btn.innerHTML;
        
        // Show loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menambahkan...';
        btn.disabled = true;
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Berhasil Ditambahkan!';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.disabled = false;
                }, 2000);
                
                // Show success alert
                showAlert('success', data.message);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = originalText;
            btn.disabled = false;
            showAlert('error', error.message || 'Terjadi kesalahan saat menambahkan ke keranjang');
        });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove existing alerts
        document.querySelectorAll('.alert').forEach(alert => alert.remove());
        
        // Add new alert at the top of the page
        const container = document.querySelector('.container') || document.querySelector('.row').parentElement;
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
</script>
@endpush
