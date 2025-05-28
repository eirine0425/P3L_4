@extends('layouts.dashboard')

@section('title', 'Detail Barang - ' . $item->nama_barang)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.consignor') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('consignor.items') }}">Barang Saya</a></li>
                    <li class="breadcrumb-item active">{{ $item->nama_barang }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Item Image -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($item->foto_barang)
                        <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                             alt="{{ $item->nama_barang }}" 
                             class="img-fluid rounded" style="max-height: 400px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                             style="height: 300px;">
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Item Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $item->nama_barang }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Barang:</strong></td>
                                    <td>{{ $item->barang_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Barang:</strong></td>
                                    <td>{{ $item->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori:</strong></td>
                                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga:</strong></td>
                                    <td>{{ $item->formatted_price }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kondisi:</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($item->kondisi) }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($item->status == 'belum_terjual')
                                            <span class="badge bg-success">Belum Terjual</span>
                                        @elseif($item->status == 'terjual')
                                            <span class="badge bg-primary">Terjual</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Penitipan:</strong></td>
                                    <td>{{ $item->tanggal_mulai_penitipan->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update:</strong></td>
                                    <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Batas Penitipan:</strong></td>
                                    <td>
                                        @if($item->batas_penitipan)
                                            {{ $item->batas_penitipan->format('d M Y') }}
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><strong>Deskripsi:</strong></h6>
                            <p>{{ $item->deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if($item->status != 'terjual')
    <form action="{{ route('items.extend', $item->barang_id) }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-warning">Perpanjang Masa Penitipan</button>
</form>

@endif

                            @if($item->status != 'terjual')
                                <a href="{{ route('consignor.items.edit', $item->barang_id) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-1"></i>Edit Detail
                                </a>
                            @endif
                            <a href="{{ route('products.show', $item->barang_id) }}" class="btn btn-info me-2" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Lihat di Katalog
                            </a>
                            <a href="{{ route('consignor.items') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Countdown Section -->
    @if($item->batas_penitipan && !$item->is_expired)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Countdown Penitipan
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-3">
                            <div class="countdown-item">
                                <div class="countdown-number" id="days">0</div>
                                <div class="countdown-label">Hari</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="countdown-item">
                                <div class="countdown-number" id="hours">0</div>
                                <div class="countdown-label">Jam</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="countdown-item">
                                <div class="countdown-number" id="minutes">0</div>
                                <div class="countdown-label">Menit</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="countdown-item">
                                <div class="countdown-number" id="seconds">0</div>
                                <div class="countdown-label">Detik</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted mb-0">Berakhir pada: <strong>{{ $item->batas_penitipan->format('d M Y H:i') }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($item->is_expired)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Masa penitipan telah berakhir!</strong>
                    </div>
                    <p class="text-muted">Berakhir pada: <strong>{{ $item->batas_penitipan->format('d M Y H:i') }}</strong></p>
                    <p class="text-danger">{{ $item->formatted_sisa_waktu }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.countdown-item {
    padding: 20px;
}

.countdown-number {
    font-size: 3rem;
    font-weight: bold;
    color: #007bff;
    line-height: 1;
}

.countdown-label {
    font-size: 1rem;
    color: #6c757d;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .countdown-number {
        font-size: 2rem;
    }
    
    .countdown-label {
        font-size: 0.9rem;
    }
}
</style>
@endsection

@push('scripts')
@if($item->batas_penitipan && !$item->is_expired)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set the date we're counting down to
    const countDownDate = new Date("{{ $item->batas_penitipan->format('Y-m-d H:i:s') }}").getTime();

    // Update the count down every 1 second
    const countdownTimer = setInterval(function() {
        // Get today's date and time
        const now = new Date().getTime();
        
        // Find the distance between now and the count down date
        const distance = countDownDate - now;
        
        // Time calculations for days, hours, minutes and seconds
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Display the result in the elements
        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;
        
        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(countdownTimer);
            document.querySelector('.countdown-item').innerHTML = '<div class="alert alert-danger">Masa penitipan telah berakhir!</div>';
            // Reload page to show expired state
            setTimeout(function() {
                location.reload();
            }, 2000);
        }
    }, 1000);
});
</script>
@endif
@endpush
