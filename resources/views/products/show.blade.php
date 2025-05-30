@extends('layouts.app')

@section('title', $barang->nama_barang ?? 'Detail Produk')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/products') }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $barang->nama_barang ?? 'Detail Produk' }}</li>
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
                        <img src="{{ $barang->photo_url ?? '/placeholder.svg?height=600&width=600&text=' . urlencode($barang->nama_barang ?? 'Produk') }}" 
                             class="d-block w-100" alt="{{ $barang->nama_barang ?? 'Product Image' }}" style="height: 400px; object-fit: cover;">
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <img src="{{ $barang->photo_url ?? '/placeholder.svg?height=600&width=600&text=' . urlencode($barang->nama_barang ?? 'Produk') }}" 
                         class="img-thumbnail w-100" alt="Product Thumbnail" style="height: 100px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">{{ $barang->nama_barang ?? 'Nama Produk Tidak Tersedia' }}</h2>
                
                <!-- Enhanced Rating Display -->
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <span class="text-warning fs-5 me-2">{{ $barang->star_display ?? '☆☆☆☆☆' }}</span>
                        <span class="me-2">({{ number_format($barang->rating ?? 0, 1) }})</span>
                        <span class="text-muted">{{ $barang->total_ratings ?? 0 }} rating{{ ($barang->total_ratings ?? 0) != 1 ? 's' : '' }}</span>
                    </div>
                    <span class="badge {{ $barang->getStatusBadgeClass() ?? 'bg-secondary' }}">
                        {{ $barang->getStatusDisplayText() ?? 'Status Tidak Diketahui' }}
                    </span>
                </div>
                
                <h3 class="text-primary mb-3">{{ $barang->formatted_price ?? 'Harga Tidak Tersedia' }}</h3>
                
                <div class="mb-3">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi Produk</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="150">Kondisi</td>
                            <td>: <span class="badge {{ $barang->getConditionBadgeClass() ?? 'bg-secondary' }}">{{ ucfirst($barang->kondisi ?? 'Tidak Diketahui') }}</span></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>: {{ $barang->kategori->nama_kategori ?? 'Tidak Berkategori' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Masuk</td>
                            <td>: {{ $barang->tanggal_penitipan ? \Carbon\Carbon::parse($barang->tanggal_penitipan)->format('d M Y') : 'Tidak Diketahui' }}</td>
                        </tr>
                        <tr>
                            <td>Garansi</td>
                            <td>: 
                                @if($barang->garansi)
                                    <span class="badge bg-success">Bergaransi</span>
                                @else
                                    <span class="badge bg-warning">Tidak Bergaransi</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Enhanced Consignor Info with Rating -->
                @if($barang->penitip)
                <div class="mb-4">
                    <h5><i class="fas fa-user-tag me-2"></i>Informasi Penitip</h5>
                    <div class="d-flex align-items-center">
                        <img src="{{ $barang->penitip->photo_url ?? '/placeholder.svg?height=50&width=50&text=' . urlencode(substr($barang->penitip->nama ?? 'P', 0, 1)) }}" 
                             class="rounded-circle me-3" alt="Seller">
                        <div>
                            <h6 class="mb-1">{{ $barang->penitip->nama ?? 'Nama Penitip Tidak Tersedia' }}</h6>
                            <div class="d-flex align-items-center">
                                <span class="text-warning me-2">{{ $barang->penitip->star_display ?? '☆☆☆☆☆' }}</span>
                                <span class="me-2">({{ number_format($barang->penitip->average_rating ?? 0, 1) }})</span>
                                <span class="text-muted">{{ $barang->penitip->total_ratings ?? 0 }} rating{{ ($barang->penitip->total_ratings ?? 0) != 1 ? 's' : '' }}</span>
                                <span class="badge {{ $barang->penitip->rating_badge_class ?? 'bg-secondary' }} ms-2">{{ $barang->penitip->rating_text ?? 'No Ratings' }}</span>
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
                    Product ID: {{ $barang->barang_id ?? 'No Product ID' }}<br>
                    Product Status: {{ $barang->status ?? 'No Status' }}<br>
                    Product Available: {{ $barang->isAvailable() ? 'Yes' : 'No' }}
                </div>
                @endif

                <!-- Cart Actions -->
                <div class="cart-actions">
                    @auth
                        @php
                            $userRole = strtolower(auth()->user()->role->nama_role ?? '');
                        @endphp
                        
                        @if($userRole === 'pembeli')
                            @if($barang->isAvailable())
                                <!-- Pembeli - Show Cart Actions -->
                                <div class="d-grid gap-2">
                                    <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="barang_id" value="{{ $barang->barang_id }}">
                                        
                                        <button type="submit" class="btn btn-primary btn-lg w-100" id="add-to-cart-btn">
                                            <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
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
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Rating & Ulasan ({{ $barang->total_ratings ?? 0 }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="discussion-tab" data-bs-toggle="tab" data-bs-target="#discussion" type="button" role="tab" aria-controls="discussion" aria-selected="false">Diskusi</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <h4>Deskripsi Produk</h4>
                        <div class="description-content">
                            @if($barang->deskripsi)
                                {!! nl2br(e($barang->deskripsi)) !!}
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
                                        <td>{{ $barang->nama_barang ?? 'Tidak tersedia' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi</th>
                                        <td>{{ ucfirst($barang->kondisi ?? 'Tidak diketahui') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>{{ $barang->kategori->nama_kategori ?? 'Tidak berkategori' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $barang->getStatusDisplayText() ?? 'Tidak diketahui' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Penitipan</th>
                                        <td>{{ $barang->tanggal_penitipan ? \Carbon\Carbon::parse($barang->tanggal_penitipan)->format('d M Y') : 'Tidak diketahui' }}</td>
                                    </tr>
                                    @if($barang->garansi)
                                    <tr>
                                        <th>Garansi</th>
                                        <td>Tersedia</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Enhanced Rating & Reviews Tab -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        @if(($barang->total_ratings ?? 0) > 0)
                            <!-- Rating Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <h2 class="display-4">{{ number_format($barang->rating ?? 0, 1) }}</h2>
                                    <div class="text-warning fs-4">{{ $barang->star_display ?? '☆☆☆☆☆' }}</div>
                                    <p class="text-muted">{{ $barang->total_ratings ?? 0 }} rating{{ ($barang->total_ratings ?? 0) != 1 ? 's' : '' }}</p>
                                </div>
                                <div class="col-md-8">
                                    <!-- Rating Distribution -->
                                    @if($barang->rating_distribution ?? false)
                                        @foreach(array_reverse($barang->rating_distribution, true) as $star => $count)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">{{ $star }} ★</span>
                                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                    <div class="progress-bar bg-warning" 
                                                         style="width: {{ $barang->total_ratings > 0 ? ($count / $barang->total_ratings) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <span class="text-muted">{{ $count }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Individual Ratings -->
                            <div id="ratings-container">
                                <!-- Ratings will be loaded here via AJAX -->
                            </div>
                            
                            <div class="text-center">
                                <button id="load-ratings" class="btn btn-outline-primary" data-barang-id="{{ $barang->barang_id }}">
                                    Lihat Semua Rating
                                </button>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-star-o fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada rating</h5>
                                <p class="text-muted">Jadilah yang pertama memberikan rating untuk produk ini.</p>
                            </div>
                        @endif
                        
                        @auth
                            @if(auth()->user()->role->nama_role == 'pembeli')
                            <div class="mt-4">
                                <h5>Berikan Rating</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Anda dapat memberikan rating setelah membeli dan menerima produk ini.
                                </div>
                            </div>
                            @endif
                        @endauth
                    </div>
                    
                    <div class="tab-pane fade" id="discussion" role="tabpanel" aria-labelledby="discussion-tab">
                        <h4>Diskusi Produk</h4>
                        
                        <div class="discussion-list mb-4">
                            @if($barang->diskusi && $barang->diskusi->count() > 0)
                                @foreach($barang->diskusi->take(5) as $diskusi)
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
                                <form action="{{ route('product.discussion.store', $barang->barang_id) }}" method="POST">
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
@if(isset($relatedProducts) && $relatedProducts && $relatedProducts->count() > 0)
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
                            <div class="text-warning">
                                {{ $relatedProduct->star_display ?? '☆☆☆☆☆' }}
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
    // Rating loading functionality
    document.addEventListener('DOMContentLoaded', function() {
        const loadRatingsBtn = document.getElementById('load-ratings');
        const ratingsContainer = document.getElementById('ratings-container');
        
        if (loadRatingsBtn) {
            loadRatingsBtn.addEventListener('click', function() {
                const barangId = this.getAttribute('data-barang-id');
                loadRatings(barangId);
            });
        }
        
        function loadRatings(barangId) {
            fetch(`/api/ratings/item/${barangId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRatings(data.data.ratings.data || data.data);
                        loadRatingsBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading ratings:', error);
                });
        }
        
        function displayRatings(ratings) {
            let html = '';
            
            if (ratings && ratings.length > 0) {
                ratings.forEach(rating => {
                    const stars = '★'.repeat(rating.rating) + '☆'.repeat(5 - rating.rating);
                    const reviewDate = new Date(rating.created_at).toLocaleDateString('id-ID');
                    
                    html += `
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${rating.pembeli?.nama || 'Pembeli'}</strong>
                                    <div class="text-warning">${stars}</div>
                                    ${rating.review ? `<p class="mt-2 mb-1">${rating.review}</p>` : ''}
                                    <small class="text-muted">${reviewDate}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<p class="text-muted">Belum ada rating untuk produk ini.</p>';
            }
            
            ratingsContainer.innerHTML = html;
        }
    });

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
    const addToCartForm = document.getElementById('add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
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
    }

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
