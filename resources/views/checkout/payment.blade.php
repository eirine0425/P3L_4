@extends('layouts.app')

@section('title', 'Pembayaran - Transaksi #' . $transaction->transaksi_id)

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            
            <!-- Countdown Alert -->
            <div id="countdown-alert" class="alert alert-warning border-warning mb-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clock text-warning me-2"></i>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <h6 id="countdown-title" class="fw-bold mb-1 text-warning">
                                Selesaikan Pembayaran Dalam:
                            </h6>
                            <small id="countdown-subtitle" class="text-muted">
                                Transaksi akan dibatalkan otomatis jika waktu habis
                            </small>
                        </div>
                        <div class="text-end">
                            <div id="countdown-timer" class="h4 fw-bold font-monospace text-warning mb-0">
                                15:00
                            </div>
                            <small class="text-muted">menit:detik</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- Left Column: Transaction Details -->
                <div class="col-12 col-lg-8">
                    
                    <!-- Transaction Summary -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-primary bg-gradient text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-receipt me-2"></i>Detail Transaksi
                                </h5>
                                <span class="badge bg-light text-primary">
                                    #{{ $transaction->transaksi_id }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Subtotal</small>
                                </div>
                                <div class="col-6 text-end">
                                    <span class="fw-semibold">
                                        Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="col-12"><hr class="my-1"></div>

                                <div class="col-6">
                                    <small class="text-muted">Ongkos Kirim</small>
                                </div>
                                <div class="col-6 text-end">
                                    @if($transaction->ongkos_kirim == 0)
                                        <span class="badge bg-success">GRATIS</span>
                                    @else
                                        <span class="fw-semibold">Rp {{ number_format($transaction->ongkos_kirim, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <div class="col-12"><hr class="my-1"></div>

                                @if($transaction->point_discount > 0)
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-star text-warning me-1"></i>Diskon Point
                                        </small>
                                    </div>
                                    <div class="col-6 text-end">
                                        <span class="fw-semibold text-success">
                                            -Rp {{ number_format($transaction->point_discount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="col-12"><hr class="my-1"></div>
                                @endif
                            </div>

                            <div class="alert alert-primary d-flex justify-content-between align-items-center mb-0">
                                <span class="h6 fw-bold mb-0">Total Pembayaran</span>
                                <span class="h5 fw-bold text-primary mb-0">
                                    Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Shipping Info -->
                            <hr class="my-3">
                            <h6 class="fw-semibold mb-2">
                                <i class="fas fa-shipping-fast text-primary me-2"></i>Informasi Pengiriman
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <span class="badge {{ $transaction->metode_pengiriman === 'diantar' ? 'bg-primary' : 'bg-success' }} mb-2">
                                        @if($transaction->metode_pengiriman === 'diantar')
                                            <i class="fas fa-truck me-1"></i>Diantar Kurir
                                        @else
                                            <i class="fas fa-store me-1"></i>Ambil Sendiri
                                        @endif
                                    </span>

                                    @if($transaction->alamat)
                                        <div class="border rounded p-2 bg-white">
                                            <div class="fw-semibold">{{ $transaction->alamat->nama_penerima }}</div>
                                            <small class="text-muted d-block">{{ $transaction->alamat->alamat }}</small>
                                            <small class="text-muted d-block">{{ $transaction->alamat->kota }}, {{ $transaction->alamat->provinsi }} {{ $transaction->alamat->kode_pos }}</small>
                                            <small class="text-muted">
                                                <i class="fas fa-phone text-success me-1"></i>{{ $transaction->alamat->no_telepon }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-success bg-gradient text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-shopping-bag me-2"></i>Item Pesanan ({{ count($transaction->details) }} item)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div style="max-height: 300px; overflow-y: auto;">
                                @foreach($transaction->details as $detail)
                                    <div class="card bg-light mb-2">
                                        <div class="card-body p-3">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="position-relative">
                                                        <img src="{{ $detail->barang->foto_barang ?? '/placeholder.svg?height=60&width=60' }}" 
                                                             alt="{{ $detail->barang->nama_barang }}" 
                                                             class="rounded border" style="width: 60px; height: 60px; object-fit: cover;">
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                            {{ $detail->jumlah }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h6 class="fw-semibold mb-1">{{ $detail->barang->nama_barang }}</h6>
                                                    <small class="text-muted d-block">
                                                        {{ $detail->barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori' }}
                                                    </small>
                                                    <div class="d-flex gap-2 mt-1">
                                                        <span class="badge bg-secondary">{{ $detail->barang->kondisi }}</span>
                                                        <small class="text-muted">@Rp {{ number_format($detail->harga, 0, ',', '.') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-auto text-end">
                                                    <div class="fw-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Payment Instructions & Upload -->
                <div class="col-12 col-lg-4">
                    
                    <!-- Payment Instructions -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-info bg-gradient text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-credit-card me-2"></i>Instruksi Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">
                            
                            <!-- Bank Transfer Info -->
                            <div class="alert alert-info">
                                <h6 class="fw-semibold mb-2">
                                    <i class="fas fa-university me-1"></i>Transfer Bank
                                </h6>
                                <div class="row g-1 small">
                                    <div class="col-4"><strong>Bank:</strong></div>
                                    <div class="col-8">BCA</div>
                                    <div class="col-4"><strong>No. Rek:</strong></div>
                                    <div class="col-8 font-monospace">1234567890</div>
                                    <div class="col-4"><strong>A/N:</strong></div>
                                    <div class="col-8">Reusemart</div>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center bg-primary bg-opacity-10 p-2 rounded">
                                    <strong class="text-primary">Nominal Transfer:</strong>
                                    <strong class="text-primary h6 mb-0">
                                        Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>

                            <!-- Payment Steps -->
                            <div class="alert alert-success">
                                <h6 class="fw-semibold mb-2">
                                    <i class="fas fa-list-ol me-1"></i>Langkah Pembayaran:
                                </h6>
                                <ol class="small mb-0 ps-3">
                                    <li class="mb-1">Transfer sesuai nominal yang tertera</li>
                                    <li class="mb-1">Upload bukti pembayaran di bawah</li>
                                    <li class="mb-0">Tunggu konfirmasi dari admin (1x24 jam)</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Payment Proof -->
                    <div class="card shadow-sm mb-3" id="upload-section">
                        <div class="card-header bg-warning bg-gradient text-dark">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('payment.upload', $transaction->transaksi_id) }}" method="POST" enctype="multipart/form-data" id="payment-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="bukti_pembayaran" class="form-label small">Pilih File Bukti Transfer</label>
                                    <div class="border border-2 border-dashed rounded p-3 text-center" style="border-color: #dee2e6 !important;">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <div class="small text-muted mb-2">
                                            <label for="bukti_pembayaran" class="text-primary text-decoration-underline" style="cursor: pointer;">
                                                Upload file
                                            </label>
                                            atau drag and drop
                                        </div>
                                        <small class="text-muted">PNG, JPG, JPEG maksimal 2MB</small>
                                        <input id="bukti_pembayaran" name="bukti_pembayaran" type="file" accept="image/*" required class="d-none">
                                    </div>

                                    <div id="file-preview" class="mt-2 d-none">
                                        <div class="alert alert-success d-flex align-items-center py-2">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <span id="file-name" class="small"></span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="upload-btn" class="btn btn-primary w-100" disabled>
                                    <i class="fas fa-upload me-1"></i>Upload Bukti Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-1"></i>Kembali ke Beranda
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                          <button onclick="cancelTransaction()" class="btn btn-danger">
                              <i class="fas fa-times me-1"></i>Batalkan Transaksi
                          </button>
                        </a>
                    </div>

                    <!-- Help Section -->
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i class="fas fa-question-circle me-2 mt-1"></i>
                            <div>
                                <h6 class="fw-semibold mb-1">Butuh Bantuan?</h6>
                                <p class="small mb-2">
                                    Jika mengalami kesulitan dalam proses pembayaran, silakan hubungi customer service kami.
                                </p>
                                <a href="tel:+6281234567890" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-phone me-1"></i>Hubungi CS
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expired Modal -->
<div class="modal fade" id="expired-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h5 class="fw-bold mb-2">Waktu Pembayaran Habis</h5>
                <p class="text-muted mb-3">
                    Transaksi telah dibatalkan otomatis. Silakan buat pesanan baru untuk melanjutkan belanja.
                </p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment deadline from server
    const paymentDeadline = new Date('{{ \Carbon\Carbon::parse($transaction->batas_pembayaran)->format('c') }}').getTime();

    // Countdown timer
    function updateCountdown() {
        const now = new Date().getTime();
        const timeLeft = paymentDeadline - now;
        
        if (timeLeft > 0) {
            const minutes = Math.floor(timeLeft / 60000);
            const seconds = Math.floor((timeLeft % 60000) / 1000);
            
            document.getElementById('countdown-timer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Change alert style based on time left
            const alert = document.getElementById('countdown-alert');
            const title = document.getElementById('countdown-title');
            const subtitle = document.getElementById('countdown-subtitle');
            const timer = document.getElementById('countdown-timer');
            
            if (timeLeft <= 300000) { // Less than 5 minutes
                alert.className = 'alert alert-warning border-warning mb-3';
                title.textContent = '⚠️ WAKTU TERBATAS!';
                title.className = 'fw-bold mb-1 text-warning';
                subtitle.textContent = 'Segera selesaikan pembayaran!';
                subtitle.className = 'text-warning';
                timer.className = 'h4 fw-bold font-monospace text-warning mb-0';
            }
            
            if (timeLeft <= 60000) { // Less than 1 minute
                alert.className = 'alert alert-danger border-danger mb-3';
                title.textContent = '🚨 WAKTU HAMPIR HABIS!';
                title.className = 'fw-bold mb-1 text-danger';
                subtitle.textContent = 'Segera selesaikan pembayaran sekarang!';
                subtitle.className = 'text-danger';
                timer.className = 'h4 fw-bold font-monospace text-danger mb-0';
                timer.style.animation = 'blink 1s infinite';
            }
        } else {
            // Time expired
            const expiredModal = new bootstrap.Modal(document.getElementById('expired-modal'));
            expiredModal.show();
            
            document.getElementById('upload-section').style.opacity = '0.5';
            document.getElementById('upload-section').style.pointerEvents = 'none';
            
            // Update countdown display
            document.getElementById('countdown-timer').textContent = '00:00';
            const alert = document.getElementById('countdown-alert');
            const title = document.getElementById('countdown-title');
            const subtitle = document.getElementById('countdown-subtitle');
            
            alert.className = 'alert alert-danger border-danger mb-3';
            title.textContent = '❌ WAKTU HABIS!';
            title.className = 'fw-bold mb-1 text-danger';
            subtitle.textContent = 'Transaksi telah dibatalkan otomatis';
            subtitle.className = 'text-danger';
        }
    }
    
    // Update countdown every second
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);
    
    // File upload handling
    const fileInput = document.getElementById('bukti_pembayaran');
    const uploadBtn = document.getElementById('upload-btn');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            filePreview.classList.add('d-none');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i>Upload Bukti Pembayaran';
            uploadBtn.className = 'btn btn-primary w-100';
            return;
        }
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            e.target.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
            e.target.value = '';
            return;
        }
        
        // Show file preview
        fileName.textContent = file.name;
        filePreview.classList.remove('d-none');
        uploadBtn.disabled = false;
        uploadBtn.className = 'btn btn-success w-100';
        uploadBtn.innerHTML = '<i class="fas fa-check me-1"></i>File Siap - Upload Sekarang';
    });
    
    // Form submission
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        uploadBtn.disabled = true;
        uploadBtn.className = 'btn btn-secondary w-100';
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengupload...';
    });
    
    // Clear interval when page unloads
    window.addEventListener('beforeunload', function() {
        clearInterval(countdownInterval);
    });
});

function cancelTransaction() {
    const confirmCancel = confirm("Apakah Anda yakin ingin membatalkan transaksi ini?");
    
    if (!confirmCancel) return;

    const cancelUrl = @json(route('transaction.cancel', $transaction->transaksi_id));
    window.location.href = cancelUrl;
}

// Add blink animation for urgent countdown
const style = document.createElement('style');
style.textContent = `
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0.3; }
    }
`;
document.head.appendChild(style);
</script>
@endsection
