@extends('layouts.dashboard')

@section('title', 'Rating yang Diterima')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Rating & Ulasan yang Diterima</h2>
            <p class="text-muted">Lihat semua rating dan ulasan dari pembeli untuk barang titipan Anda.</p>
        </div>
    </div>
    
    @include('partials.alerts')
    
    <!-- Rating Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2 class="display-4">{{ number_format($ratingStats['average_rating'], 1) }}</h2>
                    <div class="fs-4">{{ $ratingStats['star_display'] }}</div>
                    <p class="mb-0">Rating Rata-rata</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2 class="display-4">{{ $ratingStats['total_ratings'] }}</h2>
                    <p class="mb-0">Total Rating</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $ratingStats['rating_text'] }}</h3>
                    <p class="mb-0">Status Rating</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ $ratingStats['rating_distribution'][5] ?? 0 }}</h3>
                    <p class="mb-0">Rating 5 Bintang</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ratings List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Daftar Rating & Ulasan</h5>
                </div>
                <div class="card-body">
                    @if($ratings->count() > 0)
                        @foreach($ratings as $rating)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="{{ $rating->barang->photo_url ?? '/placeholder.svg?height=80&width=80' }}" 
                                             alt="{{ $rating->barang->nama_barang }}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-10">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $rating->barang->nama_barang }}</h6>
                                                <p class="text-muted small mb-2">
                                                    Oleh: {{ $rating->pembeli->nama }} | 
                                                    Transaksi: #{{ $rating->transaksi->transaksi_id }}
                                                </p>
                                                
                                                <!-- Star Rating -->
                                                <div class="mb-2">
                                                    <span class="text-warning fs-5">{{ $rating->star_display }}</span>
                                                    <span class="ms-2 text-muted">({{ $rating->rating }}/5)</span>
                                                </div>
                                                
                                                @if($rating->review)
                                                    <p class="mb-2">{{ $rating->review }}</p>
                                                @endif
                                                
                                                <small class="text-muted">
                                                    {{ $rating->created_at->format('d M Y H:i') }}
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ route('consignor.ratings.show', $rating->rating_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $ratings->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h5>Belum Ada Rating</h5>
                            <p class="text-muted">
                                @if(request()->has('rating_filter') || request()->has('barang_filter'))
                                    Tidak ada rating yang sesuai dengan filter yang dipilih.
                                @else
                                    Anda belum menerima rating dari pembeli.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
