@extends('layouts.dashboard')

@section('title', 'Detail Rating')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.consignor') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('consignor.ratings') }}">Rating</a></li>
                    <li class="breadcrumb-item active">Detail Rating</li>
                </ol>
            </nav>
            <h2>Detail Rating & Ulasan</h2>
        </div>
    </div>
    
    @include('partials.alerts')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Product Info -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <img src="{{ $rating->barang->photo_url ?? '/placeholder.svg?height=150&width=150' }}" 
                                 alt="{{ $rating->barang->nama_barang }}" 
                                 class="img-thumbnail w-100" 
                                 style="height: 150px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <h4>{{ $rating->barang->nama_barang }}</h4>
                            <p class="text-muted">{{ $rating->barang->deskripsi }}</p>
                            <p><strong>Harga:</strong> Rp {{ number_format($rating->barang->harga, 0, ',', '.') }}</p>
                            <p><strong>Kondisi:</strong> {{ ucfirst($rating->barang->kondisi) }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Rating Details -->
                    <div class="mb-4">
                        <h5>Rating & Ulasan</h5>
                        
                        <!-- Star Rating -->
                        <div class="mb-3">
                            <span class="text-warning" style="font-size: 2rem;">{{ $rating->star_display }}</span>
                            <span class="ms-2 fs-4">({{ $rating->rating }}/5)</span>
                        </div>
                        
                        @if($rating->review)
                            <div class="mb-3">
                                <h6>Ulasan:</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{{ $rating->review }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                Diberikan pada: {{ $rating->created_at->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Buyer Info -->
                    <div class="mb-4">
                        <h5>Informasi Pembeli</h5>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $rating->pembeli->nama }}</h6>
                                <small class="text-muted">Pembeli</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Info -->
                    <div class="mb-4">
                        <h5>Informasi Transaksi</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>ID Transaksi:</strong></td>
                                    <td>#{{ $rating->transaksi->transaksi_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Transaksi:</strong></td>
                                    <td>{{ $rating->transaksi->tanggal_pesan ? \Carbon\Carbon::parse($rating->transaksi->tanggal_pesan)->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-success">{{ $rating->transaksi->status_transaksi }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Harga:</strong></td>
                                    <td>Rp {{ number_format($rating->transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Rating Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6>Rating Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary">{{ $rating->rating }}/5</h3>
                        <div class="text-warning fs-4 mb-2">{{ $rating->star_display }}</div>
                        <p class="text-muted mb-0">
                            @if($rating->rating >= 5)
                                Excellent
                            @elseif($rating->rating >= 4)
                                Very Good
                            @elseif($rating->rating >= 3)
                                Good
                            @elseif($rating->rating >= 2)
                                Fair
                            @else
                                Poor
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h6>Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('consignor.ratings') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Rating
                        </a>
                        <a href="{{ route('consignor.items.show', $rating->barang->barang_id) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i> Lihat Detail Barang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
