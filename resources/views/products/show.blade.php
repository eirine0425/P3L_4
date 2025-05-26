@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/products') }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Produk</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
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
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
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
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">Laptop Bekas Berkualitas</h2>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star-half-alt text-warning"></i>
                        <span class="ms-1">(25 ulasan)</span>
                    </div>
                    <span class="badge bg-success">Tersedia</span>
                </div>
                
                <h3 class="text-primary mb-3">Rp 3.500.000</h3>
                
                <div class="mb-3">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi Produk</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="150">Kondisi</td>
                            <td>: <span class="badge bg-info">Sangat Layak</span></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>: Elektronik</td>
                        </tr>
                        <tr>
                            <td>Tanggal Masuk</td>
                            <td>: 15 Mei 2023</td>
                        </tr>
                        <tr>
                            <td>Garansi</td>
                            <td>: <span class="badge bg-success">Bergaransi (30 hari)</span></td>
                        </tr>
                        <tr>
                                <td colspan="2" class="text-center pt-3">
                                    <a href="{{ route('cart.index') }}" class="btn btn-primary">
                                        Tambah ke Keranjang
                                    </a>
                                </td>
                            </tr>
                    </table>
                </div>
                
                <div class="mb-4">
                    <h5><i class="fas fa-user-tag me-2"></i>Informasi Penitip</h5>
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Seller">
                        <div>
                            <h6 class="mb-1">Budi Santoso <span class="badge-top-seller"><i class="fas fa-crown me-1"></i>Top Seller</span></h6>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-1">(78)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @auth
                    @if(auth()->user()->role->nama_role == 'Pembeli')
                    <div class="d-grid gap-2">
    <form action="{{ route('cart.add') }}" method="POST">
        @csrf
        <input type="hidden" name="barang_id" value="{{ $barang->barang_id }}">
        <input type="hidden" name="jumlah" value="1"> {{-- atau input dinamis --}}
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
        </button>
    </form>

    <form action="{{ route('cart.buyNow') }}" method="POST">
        @csrf
        <input type="hidden" name="barang_id" value="{{ $barang->barang_id }}">
        <input type="hidden" name="jumlah" value="1">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-shopping-bag me-2"></i>Beli Sekarang
        </button>
    </form>
</div>

                    @endif
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Silakan <a href="{{ url('/login') }}">login</a> untuk melakukan pembelian.
                </div>
                @endauth
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
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab" aria-controls="specifications" aria-selected="false">Spesifikasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Ulasan (25)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="discussion-tab" data-bs-toggle="tab" data-bs-target="#discussion" type="button" role="tab" aria-controls="discussion" aria-selected="false">Diskusi</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <h4>Deskripsi Produk</h4>
                        <p>Laptop bekas berkualitas dengan kondisi sangat layak. Laptop ini telah melalui proses pengecekan dan perbaikan oleh tim teknisi kami untuk memastikan kualitasnya.</p>
                        <p>Fitur utama:</p>
                        <ul>
                            <li>Prosesor Intel Core i5 generasi ke-10</li>
                            <li>RAM 8GB DDR4</li>
                            <li>SSD 256GB</li>
                            <li>Layar 14 inch Full HD</li>
                            <li>Baterai tahan hingga 6 jam</li>
                        </ul>
                        <p>Cocok untuk kebutuhan kerja, kuliah, atau penggunaan sehari-hari. Laptop ini masih dalam kondisi sangat baik dengan sedikit tanda pemakaian normal.</p>
                    </div>
                    <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
                        <h4>Spesifikasi Produk</h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="200">Merek</th>
                                    <td>Lenovo</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>ThinkPad X390</td>
                                </tr>
                                <tr>
                                    <th>Prosesor</th>
                                    <td>Intel Core i5-10210U (1.6 GHz, up to 4.2 GHz)</td>
                                </tr>
                                <tr>
                                    <th>RAM</th>
                                    <td>8GB DDR4</td>
                                </tr>
                                <tr>
                                    <th>Penyimpanan</th>
                                    <td>SSD 256GB</td>
                                </tr>
                                <tr>
                                    <th>Layar</th>
                                    <td>14 inch Full HD (1920 x 1080)</td>
                                </tr>
                                <tr>
                                    <th>Kartu Grafis</th>
                                    <td>Intel UHD Graphics</td>
                                </tr>
                                <tr>
                                    <th>Baterai</th>
                                    <td>Lithium-ion 3-cell, 45Wh</td>
                                </tr>
                                <tr>
                                    <th>Sistem Operasi</th>
                                    <td>Windows 10 Pro</td>
                                </tr>
                                <tr>
                                    <th>Kondisi Fisik</th>
                                    <td>Sangat Layak (sedikit tanda pemakaian normal)</td>
                                </tr>
                                <tr>
                                    <th>Kelengkapan</th>
                                    <td>Unit laptop, charger original</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4>Ulasan Produk</h4>
                            <div>
                                <span class="fs-4 fw-bold">4.5</span>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-1">(25 ulasan)</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>5 Bintang (20)</span>
                                <span>80%</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 12%" aria-valuenow="12" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>4 Bintang (3)</span>
                                <span>12%</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 8%" aria-valuenow="8" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>3 Bintang (2)</span>
                                <span>8%</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>2 Bintang (0)</span>
                                <span>0%</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>1 Bintang (0)</span>
                                <span>0%</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Review List -->
                        <div class="review-list">
                            @for ($i = 1; $i <= 3; $i++)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
                                            <div>
                                                <h6 class="mb-0">Pembeli {{ $i }}</h6>
                                                <small class="text-muted">{{ date('d M Y', strtotime('-' . $i . ' days')) }}</small>
                                            </div>
                                        </div>
                                        <div>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                    </div>
                                    <p>Laptop ini sangat bagus dan sesuai dengan deskripsi. Kondisinya masih sangat layak dan performanya memuaskan. Pengiriman juga cepat dan aman. Sangat puas dengan pembelian ini!</p>
                                </div>
                            </div>
                            @endfor
                            
                            <div class="d-grid gap-2 col-md-4 mx-auto mt-4">
                                <button class="btn btn-outline-primary" type="button">Lihat Semua Ulasan</button>
                            </div>
                        </div>
                        
                        @auth
                            @if(auth()->user()->role->nama_role == 'Pembeli')
                            <div class="mt-4">
                                <h5>Berikan Ulasan</h5>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="rating">
                                            <i class="far fa-star fs-4 me-1 rating-star" data-rating="1"></i>
                                            <i class="far fa-star fs-4 me-1 rating-star" data-rating="2"></i>
                                            <i class="far fa-star fs-4 me-1 rating-star" data-rating="3"></i>
                                            <i class="far fa-star fs-4 me-1 rating-star" data-rating="4"></i>
                                            <i class="far fa-star fs-4 me-1 rating-star" data-rating="5"></i>
                                            <input type="hidden" name="rating" id="rating-value" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="review" class="form-label">Ulasan</label>
                                        <textarea class="form-control" id="review" name="review" rows="3" placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                                </form>
                            </div>
                            @endif
                        @endauth
                    </div>
                    <div class="tab-pane fade" id="discussion" role="tabpanel" aria-labelledby="discussion-tab">
                        <h4>Diskusi Produk</h4>
                        
                        <div class="discussion-list mb-4">
                            @for ($i = 1; $i <= 3; $i++)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
                                        <div>
                                            <h6 class="mb-0">Penanya {{ $i }}</h6>
                                            <small class="text-muted">{{ date('d M Y H:i', strtotime('-' . $i . ' days')) }}</small>
                                            <p class="mt-2">Apakah laptop ini masih bergaransi? Dan apakah ada garansi dari ReuseMart?</p>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5 ps-2 border-start">
                                        <div class="d-flex align-items-start mb-2">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Seller">
                                            <div>
                                                <h6 class="mb-0">Penitip <span class="badge bg-primary">CS</span></h6>
                                                <small class="text-muted">{{ date('d M Y H:i', strtotime('-' . $i . ' days +2 hours')) }}</small>
                                                <p class="mt-2">Ya, laptop ini masih memiliki garansi pabrik hingga 3 bulan ke depan. Selain itu, ReuseMart juga memberikan garansi toko selama 30 hari untuk memastikan kepuasan Anda.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        
                        @auth
                        <div class="card">
                            <div class="card-body">
                                <h5>Ajukan Pertanyaan</h5>
                                <form>
                                    <div class="mb-3">
                                        <textarea class="form-control" id="question" name="question" rows="3" placeholder="Tulis pertanyaan Anda tentang produk ini..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Kirim Pertanyaan</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Silakan <a href="{{ url('/login') }}">login</a> untuk mengajukan pertanyaan.
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<div class="row mt-4">
    <div class="col-12">
        <h3 class="mb-4"><i class="fas fa-tags me-2"></i>Produk Terkait</h3>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @for ($i = 1; $i <= 4; $i++)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Related Product {{ $i }}">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Tersedia</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Produk Terkait {{ $i }}</h5>
                        <p class="card-text">Deskripsi singkat produk terkait {{ $i }}.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Rp {{ number_format(rand(100000, 1000000), 0, ',', '.') }}</span>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid">
                            <a href="{{ url('/products/' . ($i + 10)) }}" class="btn btn-outline-primary">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Thumbnail click handler
    document.querySelectorAll('.img-thumbnail').forEach(function(thumb, index) {
        thumb.addEventListener('click', function() {
            const carousel = new bootstrap.Carousel(document.getElementById('productCarousel'));
            carousel.to(index);
        });
    });
    
    // Rating system
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingValue = document.getElementById('rating-value');
    
    ratingStars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            highlightStars(rating);
        });
        
        star.addEventListener('mouseout', function() {
            const currentRating = parseInt(ratingValue.value);
            highlightStars(currentRating);
        });
        
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            ratingValue.value = rating;
            highlightStars(rating);
        });
    });
    
    function highlightStars(rating) {
        ratingStars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            if (starRating <= rating) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }
    
    // Add to cart button
    document.getElementById('addToCartBtn').addEventListener('click', function() {
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
    
    // Buy now button
    document.getElementById('buyNowBtn').addEventListener('click', function() {
        window.location.href = "{{ url('/checkout') }}";
    });
</script>
@endpush
