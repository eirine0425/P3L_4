@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
                @if($cartItems->count() > 0)
                    <span class="badge bg-primary">{{ $cartItems->count() }} item</span>
                @endif
            </h2>
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
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Item dalam Keranjang</h5>
                        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Kosongkan Keranjang
                            </button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $item)
                            <div class="border-bottom p-3" id="cart-item-{{ $item->keranjang_id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if($item->barang && $item->barang->foto_barang)
                                            <img src="{{ asset('storage/' . $item->barang->foto_barang) }}" 
                                                 alt="{{ $item->barang->nama_barang }}" 
                                                 class="img-fluid rounded" 
                                                 style="height: 80px; width: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 80px; width: 80px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1">
                                            {{ $item->barang->nama_barang ?? 'Nama tidak tersedia' }}
                                        </h6>
                                        <p class="text-muted mb-1">
                                            @if($item->barang && $item->barang->kategoriBarang)
                                                {{ $item->barang->kategoriBarang->nama_kategori }}
                                            @else
                                                Tanpa Kategori
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            Kondisi: {{ ucfirst($item->barang->kondisi ?? 'Baik') }}
                                        </small>
                                        @if($item->barang && $item->barang->deskripsi)
                                            <p class="text-muted small mb-0">
                                                {{ Str::limit($item->barang->deskripsi, 100) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="fw-bold text-primary">
                                            Rp {{ number_format($item->barang->harga ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <form action="{{ route('cart.remove', $item->keranjang_id) }}" method="POST" class="d-inline remove-item-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('Hapus item ini dari keranjang?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal ({{ $cartItems->count() }} item):</span>
                            <span class="fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Ongkos Kirim:</span>
                            <span class="text-muted">Akan dihitung di checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary fs-5">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Checkout
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">Keranjang Belanja Kosong</h4>
                        <p class="text-muted mb-4">Belum ada produk yang ditambahkan ke keranjang Anda.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        @if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h4 class="mb-3">Produk Rekomendasi</h4>
                    <div class="row">
                        @foreach($recommendedProducts as $product)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 shadow-sm">
                                    @if($product->foto_barang)
                                        <img src="{{ asset('storage/' . $product->foto_barang) }}" 
                                             class="card-img-top" alt="{{ $product->nama_barang }}" 
                                             style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">{{ Str::limit($product->nama_barang, 50) }}</h6>
                                        <p class="card-text text-primary fw-bold">
                                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                                        </p>
                                        <div class="mt-auto">
                                            <a href="{{ route('products.show', $product->barang_id) }}" 
                                               class="btn btn-outline-primary btn-sm w-100">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<!-- Debug Information (remove in production) -->
@if(config('app.debug'))
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Debug Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Cart Items Count:</strong> {{ $cartItems->count() }}</p>
                <p><strong>Subtotal:</strong> {{ $subtotal }}</p>
                <p><strong>User ID:</strong> {{ auth()->id() }}</p>
                <p><strong>User Role:</strong> {{ auth()->user()->role->nama_role ?? 'No Role' }}</p>
                
                <div class="mt-3">
                    <a href="{{ route('cart.debug') }}" class="btn btn-info btn-sm" target="_blank">
                        View Debug Data
                    </a>
                </div>
                
                @if($cartItems->count() > 0)
                    <details class="mt-3">
                        <summary>Cart Items Data</summary>
                        <pre>{{ json_encode($cartItems->toArray(), JSON_PRETTY_PRINT) }}</pre>
                    </details>
                @endif
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle remove item with AJAX
    $('.remove-item-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Hapus item ini dari keranjang?')) {
            return;
        }
        
        const form = $(this);
        const itemRow = form.closest('[id^="cart-item-"]');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Remove item from DOM
                itemRow.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Reload page to update totals
                    location.reload();
                });
                
                // Show success message
                showAlert('success', 'Item berhasil dihapus dari keranjang');
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat menghapus item';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert('error', message);
            }
        });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${iconClass} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('.alert').remove();
        $('.container').first().prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection
