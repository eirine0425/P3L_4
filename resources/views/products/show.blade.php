@extends('layouts.app')

@section('title', $product->nama_barang ?? 'Detail Produk')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <!-- Product Image -->
            <div class="product-image-container">
                @if($product->foto_barang && file_exists(storage_path('app/public/' . $product->foto_barang)))
                    <img src="{{ asset('storage/' . $product->foto_barang) }}" 
                         alt="{{ $product->nama_barang }}" 
                         class="img-fluid rounded shadow">
                @else
                    <div class="no-image-placeholder d-flex align-items-center justify-content-center bg-light rounded" style="height: 400px;">
                        <div class="text-center">
                            <i class="fas fa-image fa-5x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada gambar</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Debug Info (Remove in production) -->
            @if(config('app.debug'))
            <div class="mt-3 p-3 bg-light rounded">
                <h6>Debug Info:</h6>
                <small>
                    <strong>Product ID:</strong> {{ $product->barang_id }}<br>
                    <strong>Penitip ID:</strong> {{ $product->penitip_id ?? 'NULL' }}<br>
                    <strong>Penitip Exists:</strong> {{ $product->penitip ? 'YES' : 'NO' }}<br>
                    @if($product->penitip)
                        <strong>Penitip Name:</strong> {{ $product->penitip->nama ?? 'NULL' }}<br>
                        <strong>User ID:</strong> {{ $product->penitip->user_id ?? 'NULL' }}<br>
                        <strong>User Exists:</strong> {{ $product->penitip->user ? 'YES' : 'NO' }}<br>
                        @if($product->penitip->user)
                            <strong>User Name:</strong> {{ $product->penitip->user->name ?? 'NULL' }}<br>
                        @endif
                    @endif
                    <strong>Foto Path:</strong> {{ $product->foto_barang ?? 'NULL' }}<br>
                    <strong>File Exists:</strong> {{ $product->foto_barang && file_exists(storage_path('app/public/' . $product->foto_barang)) ? 'YES' : 'NO' }}<br>
                    <strong>Transaksi Penitipan:</strong> {{ $product->transaksiPenitipan ? 'YES' : 'NO' }}
                </small>
            </div>
            @endif
        </div>

        <div class="col-md-6">
            <!-- Product Details -->
            <div class="product-details">
                <h1 class="product-title">{{ $product->nama_barang }}</h1>
                
                <!-- Rating -->
                <div class="product-rating mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= ($product->rating ?? 0))
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor
                    <span class="text-muted ms-2">({{ $product->jumlah_ulasan ?? 0 }} ulasan)</span>
                </div>

                <!-- Price -->
                <div class="product-price mb-4">
                    <h2 class="text-primary">Rp {{ number_format($product->harga, 0, ',', '.') }}</h2>
                </div>

                <!-- Consignment Duration Information -->
                @if($product->transaksiPenitipan)
                <div class="consignment-info mb-4">
                    <h5><i class="fas fa-clock"></i> Informasi Durasi Penitipan</h5>
                    <div class="consignment-card p-3 border rounded">
                        @php
                            $transaksi = $product->transaksiPenitipan;
                            $statusClass = '';
                            $statusIcon = '';
                            $statusText = '';
                            
                            switch($transaksi->status_durasi) {
                                case 'expired':
                                    $statusClass = 'danger';
                                    $statusIcon = 'fas fa-exclamation-triangle';
                                    $statusText = 'Masa Penitipan Berakhir';
                                    break;
                                case 'warning':
                                    $statusClass = 'warning';
                                    $statusIcon = 'fas fa-clock';
                                    $statusText = 'Segera Berakhir';
                                    break;
                                default:
                                    $statusClass = 'success';
                                    $statusIcon = 'fas fa-check-circle';
                                    $statusText = 'Masa Penitipan Aktif';
                            }
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-2">
                                    <small class="text-muted">Tanggal Penitipan</small>
                                    <div class="fw-bold">
                                        <i class="fas fa-calendar-plus text-primary me-1"></i>
                                        {{ $transaksi->tanggal_penitipan ? \Carbon\Carbon::parse($transaksi->tanggal_penitipan)->format('d M Y') : '-' }}
                                    </div>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <small class="text-muted">Batas Penitipan</small>
                                    <div class="fw-bold">
                                        <i class="fas fa-calendar-times text-danger me-1"></i>
                                        {{ $transaksi->batas_penitipan ? \Carbon\Carbon::parse($transaksi->batas_penitipan)->format('d M Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-item mb-2">
                                    <small class="text-muted">Durasi Penitipan</small>
                                    <div class="fw-bold">
                                        <i class="fas fa-hourglass-half text-info me-1"></i>
                                        {{ $transaksi->durasi_penitipan }} Hari
                                    </div>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <small class="text-muted">Sisa Waktu</small>
                                    <div class="fw-bold">
                                        @if($transaksi->sisa_hari !== null)
                                            @if($transaksi->sisa_hari >= 0)
                                                <i class="fas fa-calendar-check text-{{ $statusClass }} me-1"></i>
                                                {{ $transaksi->sisa_hari }} Hari Lagi
                                            @else
                                                <i class="fas fa-calendar-times text-danger me-1"></i>
                                                Terlambat {{ abs($transaksi->sisa_hari) }} Hari
                                            @endif
                                        @else
                                            <i class="fas fa-question-circle text-muted me-1"></i>
                                            Tidak Diketahui
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="mt-3">
                            <span class="badge bg-{{ $statusClass }} fs-6">
                                <i class="{{ $statusIcon }} me-1"></i>
                                {{ $statusText }}
                            </span>
                            
                            @if($transaksi->status_durasi === 'warning')
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masa penitipan akan berakhir dalam {{ $transaksi->sisa_hari }} hari
                                </small>
                            @elseif($transaksi->status_durasi === 'expired')
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Produk ini sudah melewati batas masa penitipan
                                </small>
                            @endif
                        </div>
                        
                        <!-- Progress Bar -->
                        @if($transaksi->tanggal_penitipan && $transaksi->batas_penitipan)
                            @php
                                $totalDays = \Carbon\Carbon::parse($transaksi->tanggal_penitipan)->diffInDays(\Carbon\Carbon::parse($transaksi->batas_penitipan));
                                $passedDays = \Carbon\Carbon::parse($transaksi->tanggal_penitipan)->diffInDays(\Carbon\Carbon::now());
                                $progressPercentage = $totalDays > 0 ? min(($passedDays / $totalDays) * 100, 100) : 0;
                                
                                $progressClass = 'success';
                                if ($progressPercentage > 80) {
                                    $progressClass = 'danger';
                                } elseif ($progressPercentage > 60) {
                                    $progressClass = 'warning';
                                }
                            @endphp
                            
                            <div class="mt-3">
                                <small class="text-muted">Progress Masa Penitipan</small>
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $progressClass }}" 
                                         role="progressbar" 
                                         style="width: {{ $progressPercentage }}%"
                                         aria-valuenow="{{ $progressPercentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($progressPercentage, 1) }}% dari masa penitipan telah berlalu</small>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Product Information -->
                <div class="product-info mb-4">
                    <h5><i class="fas fa-info-circle"></i> Informasi Produk</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Kondisi</strong></td>
                            <td>: {{ ucfirst($product->kondisi ?? 'Tidak tersedia') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kategori</strong></td>
                            <td>: {{ $product->kategori->nama_kategori ?? 'Tidak tersedia' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Masuk</strong></td>
                            <td>: {{ $product->tanggal_penitipan ? \Carbon\Carbon::parse($product->tanggal_penitipan)->format('d M Y') : 'Tidak tersedia' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Garansi</strong></td>
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

                <!-- Seller Information -->
                <div class="seller-info mb-4">
                    <h5><i class="fas fa-user"></i> Informasi Penitip</h5>
                    <div class="seller-card p-3 border rounded">
                        @if($product->penitip)
                            <div class="d-flex align-items-center">
                                <div class="seller-avatar me-3">
                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                </div>
                                <div class="seller-details">
                                    <h6 class="mb-1">Seller</h6>
                                    <p class="mb-1">
                                        <strong>
                                            @if($product->penitip->nama)
                                                {{ $product->penitip->nama }}
                                            @elseif($product->penitip->user && $product->penitip->user->name)
                                                {{ $product->penitip->user->name }}
                                            @else
                                                Nama Penitip Tidak Tersedia
                                            @endif
                                        </strong>
                                    </p>
                                    <div class="seller-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-warning"></i>
                                        @endfor
                                        <span class="text-muted ms-1">(Rating Penitip)</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-user-slash fa-2x mb-2"></i>
                                <p>Informasi penitip tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="product-actions">
                    @auth
                        @php
                            $userRole = strtolower(auth()->user()->role->nama_role ?? '');
                        @endphp
                        
                        @if($userRole === 'pembeli')
                            @if($product->isAvailable())
                                <!-- Pembeli - Show Cart Actions -->
                                <div class="d-grid gap-2">
                                    <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="barang_id" value="{{ $product->barang_id }}">
                                        
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

    <!-- Product Description -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="product-description">
                <h5>Deskripsi Produk</h5>
                <div class="description-content p-3 border rounded bg-light">
                    <p>{!! nl2br(e($product->deskripsi)) !!}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts && $relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h5>Produk Terkait</h5>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        @if($relatedProduct->foto_barang && file_exists(storage_path('app/public/' . $relatedProduct->foto_barang)))
                            <img src="{{ asset('storage/' . $relatedProduct->foto_barang) }}" 
                                 class="card-img-top" 
                                 alt="{{ $relatedProduct->nama_barang }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">{{ $relatedProduct->nama_barang }}</h6>
                            <p class="card-text text-primary">Rp {{ number_format($relatedProduct->harga, 0, ',', '.') }}</p>
                            
                            <!-- Duration info for related products -->
                            @if($relatedProduct->transaksiPenitipan)
                                <div class="mb-2">
                                    @php $relatedTransaksi = $relatedProduct->transaksiPenitipan; @endphp
                                    @if($relatedTransaksi->status_durasi === 'expired')
                                        <small class="badge bg-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Expired
                                        </small>
                                    @elseif($relatedTransaksi->status_durasi === 'warning')
                                        <small class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>{{ $relatedTransaksi->sisa_hari }} hari lagi
                                        </small>
                                    @else
                                        <small class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>{{ $relatedTransaksi->sisa_hari }} hari lagi
                                        </small>
                                    @endif
                                </div>
                            @endif
                            
                            <a href="{{ route('products.show', $relatedProduct->barang_id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.product-image-container {
    position: sticky;
    top: 20px;
}

.seller-card, .consignment-card {
    background-color: #f8f9fa;
}

.product-actions .btn {
    min-width: 150px;
}

.description-content {
    min-height: 100px;
}

.consignment-info .info-item {
    border-left: 3px solid #e9ecef;
    padding-left: 10px;
}

.consignment-info .badge {
    font-size: 0.9em;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>
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
