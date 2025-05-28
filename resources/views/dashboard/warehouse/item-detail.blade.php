@extends('layouts.dashboard')

@section('title', 'Detail Barang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Barang</h2>
                    <p class="text-muted">{{ $item->nama_barang }}</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="{{ route('dashboard.warehouse.item.edit', $item->barang_id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Barang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Foto Barang</h5>
                </div>
                <div class="card-body text-center">
                    @if($item->foto_barang)
                        <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                             alt="{{ $item->nama_barang }}" 
                             class="img-fluid rounded" style="max-height: 300px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                             style="height: 300px;">
                            <div class="text-center">
                                <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Tidak ada foto</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Barang</h5>
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
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kondisi:</strong></td>
                                    <td>
                                        @if($item->kondisi == 'baru')
                                            <span class="badge bg-primary">Baru</span>
                                        @elseif($item->kondisi == 'sangat_layak')
                                            <span class="badge bg-success">Sangat Layak</span>
                                        @else
                                            <span class="badge bg-warning">Layak</span>
                                        @endif
                                    </td>
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
                                            <span class="badge bg-info">Terjual</span>
                                        @elseif($item->status == 'sold out')
                                            <span class="badge bg-secondary">Sold Out</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Penitip:</strong></td>
                                    <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Penitipan:</strong></td>
                                    <td>
                                        {{ $item->tanggal_mulai_penitipan->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Batas Penitipan:</strong></td>
                                    <td>
                                        {{ $item->batas_penitipan->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update:</strong></td>
                                    <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Durasi:</strong></td>
                                    <td>
                                        <span class="badge {{ $item->status_durasi_badge_class }}">
                                            {{ $item->status_durasi_text }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $item->formatted_sisa_waktu }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><strong>Deskripsi:</strong></h6>
                            <p class="text-muted">{{ $item->deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('dashboard.warehouse.item.update-status', $item->barang_id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <div class="input-group">
                                    <select name="status" class="form-select" required>
                                        <option value="">Ubah Status...</option>
                                        <option value="belum_terjual" {{ $item->status == 'belum_terjual' ? 'selected' : '' }}>Belum Terjual</option>
                                        <option value="terjual" {{ $item->status == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                        <option value="sold out" {{ $item->status == 'sold out' ? 'selected' : '' }}>Sold Out</option>
                                        <option value="untuk_donasi" {{ $item->status == 'untuk_donasi' ? 'selected' : '' }}>Untuk Donasi</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group w-100">
                                <a href="{{ route('products.show', $item->barang_id) }}" 
                                   class="btn btn-info" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i>Lihat di Katalog
                                </a>
                                <a href="{{ route('dashboard.warehouse.item.edit', $item->barang_id) }}" 
                                   class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>Edit Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Extend Consignment -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Perpanjang Masa Penitipan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.item.extend', $item->barang_id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" name="durasi_tambahan" class="form-control" min="1" max="90" value="30" required>
                                    <span class="input-group-text">hari</span>
                                    <button type="submit" class="btn btn-success">Perpanjang</button>
                                </div>
                                <small class="text-muted">Perpanjang masa penitipan dari tanggal batas saat ini</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Countdown Timer -->
@if(!$item->is_expired)
<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-clock me-2"></i>Countdown Penitipan
        </h5>
    </div>
    <div class="card-body text-center">
        <div id="countdown-timer" class="countdown-display">
            <div class="countdown-item">
                <span id="days" class="countdown-number">0</span>
                <span class="countdown-label">Hari</span>
            </div>
            <div class="countdown-item">
                <span id="hours" class="countdown-number">0</span>
                <span class="countdown-label">Jam</span>
            </div>
            <div class="countdown-item">
                <span id="minutes" class="countdown-number">0</span>
                <span class="countdown-label">Menit</span>
            </div>
            <div class="countdown-item">
                <span id="seconds" class="countdown-number">0</span>
                <span class="countdown-label">Detik</span>
            </div>
        </div>
        <div class="mt-3">
            <small class="text-muted">
                Berakhir pada: {{ $item->batas_penitipan->format('d M Y H:i') }}
            </small>
        </div>
    </div>
</div>
@else
<div class="card mt-3 border-danger">
    <div class="card-header bg-danger text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>Penitipan Kadaluarsa
        </h5>
    </div>
    <div class="card-body text-center">
        <i class="fas fa-calendar-times fa-3x text-danger mb-3"></i>
        <h6 class="text-danger">Masa penitipan telah berakhir</h6>
        <p class="text-muted">{{ $item->formatted_sisa_waktu }}</p>
        <div class="alert alert-warning">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                Barang ini perlu ditindaklanjuti segera
            </small>
        </div>
    </div>
</div>
@endif

<style>
.countdown-display {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.countdown-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 60px;
}

.countdown-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    line-height: 1;
}

.countdown-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-danger { background-color: #dc3545 !important; }
.badge-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge-info { background-color: #17a2b8 !important; }
.badge-success { background-color: #28a745 !important; }
.badge-secondary { background-color: #6c757d !important; }
</style>

@if(!$item->is_expired)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set the date we're counting down to
    const countDownDate = new Date("{{ $item->batas_penitipan->format('Y-m-d H:i:s') }}").getTime();
    
    // Update the count down every 1 second
    const x = setInterval(function() {
        // Get today's date and time
        const now = new Date().getTime();
        
        // Find the distance between now and the count down date
        const distance = countDownDate - now;
        
        // Time calculations for days, hours, minutes and seconds
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Display the result
        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;
        
        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown-timer").innerHTML = 
                '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Masa penitipan telah berakhir!</div>';
        }
    }, 1000);
});
</script>
@endif
@endsection
