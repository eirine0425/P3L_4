@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card bg-dark text-white border-0 rounded-3 overflow-hidden">
            <img src="https://via.placeholder.com/1200x400" class="card-img opacity-50" alt="ReuseMart Hero">
            <div class="card-img-overlay d-flex flex-column justify-content-center text-center">
                <h1 class="card-title display-4 fw-bold">Selamat Datang di ReuseMart</h1>
                <p class="card-text fs-5">Tempat jual beli barang bekas berkualitas. Bersama kita kurangi sampah dan berikan barang kesayangan Anda kesempatan kedua.</p>
                <div class="mt-4">
                    <a href="{{ url('/products') }}" class="btn btn-primary btn-lg me-2">Lihat Produk</a>
                    <a href="{{ url('/register') }}" class="btn btn-outline-light btn-lg">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-star me-2"></i>Produk Unggulan</h2>
            <a href="{{ url('/products') }}" class="btn btn-outline-primary">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @for ($i = 1; $i <= 4; $i++)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product {{ $i }}">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Tersedia</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Produk Unggulan {{ $i }}</h5>
                        <p class="card-text">Deskripsi singkat produk unggulan {{ $i }} yang menjelaskan kondisi dan kualitas barang.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Rp {{ number_format(rand(100000, 1000000), 0, ',', '.') }}</span>
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
                            <a href="{{ url('/products/' . $i) }}" class="btn btn-outline-primary">Detail</a>
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
                ['name' => 'Lainnya', 'icon' => 'fas fa-ellipsis-h']
            ];
            @endphp
            
            @foreach ($categories as $category)
            <div class="col">
                <a href="{{ url('/products/category/' . strtolower($category['name'])) }}" class="text-decoration-none">
                    <div class="card h-100 text-center product-card">
                        <div class="card-body">
                            <i class="{{ $category['icon'] }} fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">{{ $category['name'] }}</h5>
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
        <h2 class="mb-4"><i class="fas fa-crown me-2 text-warning"></i>Top Seller Bulan Ini</h2>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @for ($i = 1; $i <= 3; $i++)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://via.placeholder.com/60" class="rounded-circle me-3" alt="Seller {{ $i }}">
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
                    'rating' => 5
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
                            <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="{{ $testimonial['name'] }}">
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
