@extends('layouts.dashboard')

@section('title', 'Jadwalkan Pengiriman')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Jadwalkan Pengiriman</h2>
                    <p class="text-muted">Buat jadwal pengiriman dan tugaskan kurir untuk transaksi #{{ $transaction->transaksi_id }}</p>
                </div>
                <a href="{{ route('warehouse.shipments.show', $transaction->transaksi_id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Penjadwalan Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('warehouse.shipments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="transaksi_id" value="{{ $transaction->transaksi_id }}">
                        <input type="hidden" name="alamat_id" value="{{ $transaction->pembeli->alamat->alamat_id ?? '' }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pengirim_id" class="form-label">Pilih Kurir <span class="text-danger">*</span></label>
                                    <select name="pengirim_id" id="pengirim_id" class="form-select @error('pengirim_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kurir --</option>
                                        @foreach($couriers as $courier)
                                            <option value="{{ $courier->id }}" {{ old('pengirim_id') == $courier->id ? 'selected' : '' }}>
                                                {{ $courier->name }} - {{ $courier->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pengirim_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_kirim" class="form-label">Tanggal Pengiriman <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_kirim" id="tanggal_kirim" 
                                           class="form-control @error('tanggal_kirim') is-invalid @enderror" 
                                           value="{{ old('tanggal_kirim', date('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal_kirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        Pengiriman untuk pembelian di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_penerima" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penerima" id="nama_penerima" 
                                   class="form-control @error('nama_penerima') is-invalid @enderror" 
                                   value="{{ old('nama_penerima', $transaction->pembeli->user->name) }}" required>
                            @error('nama_penerima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan Pengiriman</label>
                            <textarea name="catatan" id="catatan" rows="3" 
                                      class="form-control @error('catatan') is-invalid @enderror" 
                                      placeholder="Catatan khusus untuk pengiriman (opsional)">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-bell"></i>
                            <strong>Notifikasi Otomatis:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pembeli akan menerima notifikasi tentang jadwal pengiriman</li>
                                <li>Penitip akan menerima notifikasi bahwa barang mereka akan dikirim</li>
                                <li>Kurir akan menerima notifikasi tugas pengiriman baru</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('warehouse.shipments.show', $transaction->transaksi_id) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Jadwalkan Pengiriman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Transaction Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Ringkasan Transaksi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>#{{ $transaction->transaksi_id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Pembeli:</strong></td>
                            <td>{{ $transaction->pembeli->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Item:</strong></td>
                            <td>{{ $transaction->detailTransaksi->count() }} barang</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Delivery Address -->
            @if($transaction->pembeli->alamat)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Alamat Pengiriman</h6>
                </div>
                <div class="card-body">
                    <address>
                        <strong>{{ $transaction->pembeli->user->name }}</strong><br>
                        {{ $transaction->pembeli->alamat->alamat_lengkap }}<br>
                        {{ $transaction->pembeli->alamat->kota }}, {{ $transaction->pembeli->alamat->provinsi }}<br>
                        {{ $transaction->pembeli->alamat->kode_pos }}<br>
                        @if($transaction->pembeli->user->phone_number)
                            <i class="fas fa-phone"></i> {{ $transaction->pembeli->user->phone_number }}
                        @endif
                    </address>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalKirimInput = document.getElementById('tanggal_kirim');
    
    tanggalKirimInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        const currentHour = today.getHours();
        
        // Check if selected date is today and current time is after 4 PM
        if (selectedDate.toDateString() === today.toDateString() && currentHour >= 16) {
            alert('Peringatan: Pengiriman untuk pembelian di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama. Silakan pilih tanggal besok atau setelahnya.');
            
            // Set minimum date to tomorrow
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            this.value = tomorrow.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
