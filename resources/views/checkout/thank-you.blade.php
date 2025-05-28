@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Header -->
            <div class="text-center mb-5">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h1 class="text-success mb-3">Pesanan Berhasil!</h1>
                <p class="lead text-muted">Terima kasih telah berbelanja di ReuseMart</p>
                <p class="text-muted">Pesanan Anda telah berhasil dibuat dan sedang diproses</p>
            </div>

            <!-- Order Details Card -->
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Detail Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Nomor Pesanan</h6>
                            <p class="fw-bold fs-5 text-primary">#{{ $transaction->transaksi_id ?? 'TRX-' . date('YmdHis') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Tanggal Pesanan</h6>
                            <p class="fw-bold">{{ isset($transaction) ? $transaction->created_at->format('d F Y, H:i') : date('d F Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Status Pesanan</h6>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-clock me-1"></i>Menunggu Pembayaran
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Metode Pembayaran</h6>
                            <p class="fw-bold">
                                @if(isset($transaction) && $transaction->payment_method === 'bank_transfer')
                                    <i class="fas fa-university me-2"></i>Transfer Bank
                                @else
                                    <i class="fas fa-money-bill-wave me-2"></i>Bayar di Tempat (COD)
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    @if(isset($transaction) && $transaction->payment_method === 'bank_transfer')
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Bank:</strong> BCA</p>
                                <p class="mb-1"><strong>No. Rekening:</strong> 1234567890</p>
                                <p class="mb-0"><strong>Atas Nama:</strong> ReuseMart Indonesia</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Transfer:</strong></p>
                                <p class="fs-4 fw-bold text-primary mb-0">
                                    Rp {{ number_format($transaction->total ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-0 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Silakan lakukan pembayaran dalam 24 jam. Pesanan akan otomatis dibatalkan jika tidak ada pembayaran.
                        </p>
                    </div>
                    @endif

                    <!-- Items Ordered -->
                    <h6 class="border-bottom pb-2 mb-3">Item yang Dipesan</h6>
                    <div class="order-items">
                        @if(isset($transaction) && $transaction->details)
                            @foreach($transaction->details as $detail)
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-2">
                                    <img src="{{ $detail->barang->foto_barang ? asset('storage/' . $detail->barang->foto_barang) : '/placeholder.svg?height=60&width=60' }}" 
                                         alt="{{ $detail->barang->nama_barang }}" 
                                         class="img-fluid rounded" 
                                         style="height: 60px; width: 60px; object-fit: cover;">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $detail->barang->nama_barang }}</h6>
                                    <p class="text-muted mb-0 small">{{ $detail->barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori' }}</p>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="fw-bold">{{ $detail->jumlah }}x</span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <span class="fw-bold text-primary">Rp {{ number_format($detail->harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">Detail item sedang dimuat...</p>
                            </div>
                        @endif
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary mt-4 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format(($transaction->total ?? 0) - 17500, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim:</span>
                            <span>Rp 15.000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Biaya Admin:</span>
                            <span>Rp 2.500</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold text-primary fs-4">Rp {{ number_format($transaction->total ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Pengiriman
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($transaction) && $transaction->alamat)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $transaction->alamat->nama_penerima }}</h6>
                            <p class="mb-1">{{ $transaction->alamat->no_telepon }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">{{ $transaction->alamat->alamat }}</p>
                            <p class="mb-0 text-muted">{{ $transaction->alamat->kota }}, {{ $transaction->alamat->provinsi }} {{ $transaction->alamat->kode_pos }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-muted mb-0">Alamat pengiriman akan dikonfirmasi setelah pembayaran</p>
                    @endif
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-list-ol me-2"></i>Langkah Selanjutnya
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($transaction) && $transaction->payment_method === 'bank_transfer')
                    <ol class="mb-0">
                        <li class="mb-2">Lakukan pembayaran ke rekening yang tertera di atas</li>
                        <li class="mb-2">Upload bukti pembayaran melalui dashboard Anda</li>
                        <li class="mb-2">Tunggu konfirmasi pembayaran dari tim kami (1x24 jam)</li>
                        <li class="mb-2">Pesanan akan diproses dan dikirim setelah pembayaran dikonfirmasi</li>
                        <li class="mb-0">Anda akan menerima nomor resi untuk tracking pengiriman</li>
                    </ol>
                    @else
                    <ol class="mb-0">
                        <li class="mb-2">Pesanan Anda sedang diproses</li>
                        <li class="mb-2">Tim kami akan menghubungi Anda untuk konfirmasi pengiriman</li>
                        <li class="mb-2">Siapkan uang pas sesuai total pembayaran</li>
                        <li class="mb-0">Bayar kepada kurir saat barang diterima</li>
                    </ol>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center">
                <div class="d-grid gap-2 d-md-block">
                    <a href="{{ route('buyer.transactions') }}" class="btn btn-primary btn-lg me-md-2">
                        <i class="fas fa-list me-2"></i>Lihat Pesanan Saya
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg me-md-2">
                        <i class="fas fa-shopping-bag me-2"></i>Belanja Lagi
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    Butuh bantuan? 
                    <a href="#" class="text-decoration-none">Hubungi Customer Service</a> kami
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Auto redirect notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Pesanan Berhasil</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Pesanan Anda telah berhasil dibuat! Silakan cek email untuk detail lebih lanjut.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show success toast
    var successToast = new bootstrap.Toast(document.getElementById('successToast'));
    successToast.show();
    
    // Add some animation to the success icon
    $('.success-icon i').addClass('animate__animated animate__bounceIn');
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

<style>
.success-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.order-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.btn {
    border-radius: 25px;
    padding: 12px 30px;
}

.badge {
    padding: 8px 15px;
    border-radius: 20px;
}

@media (max-width: 768px) {
    .btn-lg {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .d-grid.gap-2.d-md-block .btn {
        margin-bottom: 10px;
    }
}
</style>
@endsection
