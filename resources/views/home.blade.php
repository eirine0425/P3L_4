@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<div class="card border-0 rounded-3 overflow-hidden" style="height: 450px;">
  <div id="reuseCarousel" class="carousel slide h-100" data-bs-ride="carousel">
    <div class="carousel-inner h-100">

      <!-- Slide 1 -->
      <div class="carousel-item active h-100">
        <div class="d-flex flex-column justify-content-center align-items-center h-100 text-black text-center p-5" style="background: url('{{ asset('assets/1.png') }}') center center / cover no-repeat;">
          <h1 class="fw-bold">Selamat Datang di ReuseMart</h1>
          <p>Kami percaya bahwa setiap barang punya cerita dan kesempatan kedua...</p>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item h-100">
        <div class="d-flex flex-column justify-content-center align-items-center h-100 text-black text-center p-5" style="background: url('{{ asset('assets/2.png') }}') center center / cover no-repeat;">
          <h1 class="fw-bold">Temukan Barang Bekas Berkualitas</h1>
          <p>Harga terjangkau, kualitas terjamin, dan ramah lingkungan.</p>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item h-100">
        <div class="d-flex flex-column justify-content-center align-items-center h-100 text-black text-center p-5" style="background: url('{{ asset('assets/3.png') }}') center center / cover no-repeat;">
          <h1 class="fw-bold">Ayo Bergabung Menjadi Donatur</h1>
          <p>Sumbangkan barang bekas yang masih layak pakai untuk bantu sesama.</p>
        </div>
      </div>

    </div>

    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#reuseCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#reuseCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>
</div>




<!-- Featured Products -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-star me-2"></i>Produk Unggulan</h2>
            <a href="{{ url('/products') }}" class="btn btn-outline-success">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @php
$images = [
    asset('assets/laptop.jpg'),
    asset('assets/laptop.jpg'),
    asset('assets/laptop.jpg'),
    asset('assets/laptop.jpg')
];
@endphp

            @for ($i = 1; $i <= 4; $i++)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        <img src="{{ asset('assets/laptop.jpg') }}" class="card-img-top" alt="Product {{ $i }}">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Tersedia</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Produk Unggulan {{ $i }}</h5>
                        <p class="card-text">Deskripsi singkat produk unggulan {{ $i }} yang menjelaskan kondisi dan kualitas barang.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Rp {{ number_format(rand(100000, 1000000), 0, ',', '.') }}</span>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-1">({{ rand(10, 50) }})</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid gap-2">
                           <a href="{{ url('/products/' . $i) }}" class="btn btn-outline-success">Detail</a>

                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<!-- Categories -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-tags me-2"></i>Kategori</h2>
        
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
            @php
            $categories = [
                ['name' => 'Elektronik', 'icon' => 'fas fa-laptop'],
                ['name' => 'Furnitur', 'icon' => 'fas fa-couch'],
                ['name' => 'Fashion', 'icon' => 'fas fa-tshirt'],
                ['name' => 'Buku', 'icon' => 'fas fa-book'],
                ['name' => 'Olahraga', 'icon' => 'fas fa-futbol'],
                ['name' => 'Hobi', 'icon' => 'fas fa-paint-brush '],
                ['name' => 'Baby', 'icon' => 'fas fa-baby'],
                ['name' => 'Otomotif', 'icon' => 'fas fa-car'],
                ['name' => 'Kesehatan', 'icon' => 'fas fa-heartbeat'],
                ['name' => 'Peralatan Taman', 'icon' => 'fas fa-tree']
            ];
            @endphp
            
            @foreach ($categories as $category)
            <div class="col">
                <a href="{{ url('/products/category/' . strtolower($category['name'])) }}" class="text-decoration-none">
                    <div class="card h-100 text-center product-card">
                        <div class="card-body">
                            <i class="{{ $category['icon'] }} fa-3x mb-3" style="color: #006400;"></i> {{-- Dark green --}}

                            <h5 style="color: #006400;">{{ $category['name'] }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Top Sellers -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-crown me-2 text-warning"></i>Top Seller 3 Bulan terakhir</h2>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @for ($i = 1; $i <= 3; $i++)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Penitip {{ $i }} <span class="badge-top-seller"><i class="fas fa-crown me-1"></i>Top Seller</span></h5>
                                <div>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="ms-1">({{ rand(50, 100) }})</span>
                                </div>
                            </div>
                        </div>
                        <p class="card-text">Penitip dengan penjualan tertinggi bulan ini. Telah menjual {{ rand(20, 50) }} barang dengan total nilai Rp {{ number_format(rand(5000000, 20000000), 0, ',', '.') }}.</p>
                        <div class="d-flex flex-wrap">
                            <span class="badge bg-light text-dark me-1 mb-1">Elektronik</span>
                            <span class="badge bg-light text-dark me-1 mb-1">Fashion</span>
                            <span class="badge bg-light text-dark me-1 mb-1">Furnitur</span>
                            <span class="badge bg-light text-dark me-1 mb-1">Otomotif</span>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<!-- Testimonials -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-quote-left me-2"></i>Testimoni</h2>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @php
            $testimonials = [
                [
                    'name' => 'Budi Santoso',
                    'role' => 'Penitip',
                    'text' => 'Saya sangat puas dengan layanan ReuseMart. Barang-barang saya yang tidak terpakai bisa menghasilkan uang tambahan. Prosesnya mudah dan transparan.',
                    'rating' => 4
                ],
                [
                    'name' => 'Siti Rahayu',
                    'role' => 'Pembeli',
                    'text' => 'Menemukan barang berkualitas dengan harga terjangkau di ReuseMart. Kondisi barang sesuai deskripsi dan pengiriman cepat. Sangat merekomendasikan!',
                    'rating' => 4
                ],
                [
                    'name' => 'Yayasan Peduli Sesama',
                    'role' => 'Organisasi',
                    'text' => 'Berkat program donasi ReuseMart, kami bisa mendapatkan barang-barang yang dibutuhkan untuk disalurkan kepada yang membutuhkan. Terima kasih ReuseMart!',
                    'rating' => 5
                ]
            ];
            @endphp
            
            @foreach ($testimonials as $testimonial)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $testimonial['rating'])
                                <i class="fas fa-star text-warning"></i>
                                @else
                                <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="card-text fs-5"><i class="fas fa-quote-left me-2 text-muted"></i>{{ $testimonial['text'] }}<i class="fas fa-quote-right ms-2 text-muted"></i></p>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex align-items-center">
                           
                            <div>
                                <h5 class="mb-0">{{ $testimonial['name'] }}</h5>
                                <small class="text-muted">{{ $testimonial['role'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
