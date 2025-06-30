@extends('layouts.app')

@section('title', 'Pembayaran Berhasil - Thrift Shop')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="success-icon bg-success bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
                        </div>
                        <h2 class="fw-bold text-success">Pembayaran Berhasil!</h2>
                        <p class="text-muted">Bukti pembayaran Anda telah diterima dan sedang diproses</p>
                    </div>

                    <div class="order-info bg-light p-4 rounded-4 mb-4 text-start">
                        <h5 class="fw-bold mb-3">Detail Pesanan</h5>
                        <div class="row mb-2">
                            <div class="col-5">Nomor Pesanan</div>
                            <div class="col-7 fw-bold">{{ $transaction->transaksi_id }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">Status</div>
                            <div class="col-7">
                                <span class="badge bg-success">Dikemas</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">Tanggal Pembayaran</div>
                            <div class="col-7">{{ $transaction->tanggal_pelunasan ? $transaction->tanggal_pelunasan->format('d M Y, H:i') : '-' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">Total Pembayaran</div>
                            <div class="col-7 fw-bold">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">Metode Pengiriman</div>
                            <div class="col-7">{{ $transaction->metode_pengiriman == 'diantar' ? 'Diantar ke Alamat' : 'Diambil di Toko' }}</div>
                        </div>
                        @if($transaction->point_diperoleh > 0)
                        <div class="row mb-2">
                            <div class="col-5">Point Diperoleh</div>
                            <div class="col-7 text-success">+{{ $transaction->point_diperoleh }} point</div>
                        </div>
                        @endif
                    </div>

                    <div class="next-steps mb-4">
                        <h5 class="fw-bold mb-3">Langkah Selanjutnya</h5>
                        <div class="steps">
                            <div class="step-item d-flex align-items-center mb-3">
                                <div class="step-icon bg-primary bg-opacity-10 rounded-circle me-3 p-2">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <div class="step-text text-start">
                                    <p class="mb-0">Pesanan Anda sedang dikemas oleh tim kami</p>
                                </div>
                            </div>
                            <div class="step-item d-flex align-items-center mb-3">
                                <div class="step-icon bg-primary bg-opacity-10 rounded-circle me-3 p-2">
                                    <i class="fas fa-shipping-fast text-primary"></i>
                                </div>
                                <div class="step-text text-start">
                                    <p class="mb-0">
                                        @if($transaction->metode_pengiriman == 'diantar')
                                            Pesanan akan dikirim ke alamat Anda
                                        @else
                                            Pesanan dapat diambil di toko setelah dikonfirmasi siap
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="step-item d-flex align-items-center">
                                <div class="step-icon bg-primary bg-opacity-10 rounded-circle me-3 p-2">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div class="step-text text-start">
                                    <p class="mb-0">Anda akan menerima notifikasi saat pesanan siap</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('buyer.transactions') }}" class="btn btn-primary">
                            <i class="fas fa-list-ul me-2"></i>Lihat Daftar Transaksi
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-bag me-2"></i>Lanjutkan Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.success-icon {
    animation: pulse-success 2s infinite;
}

@keyframes pulse-success {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.step-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
