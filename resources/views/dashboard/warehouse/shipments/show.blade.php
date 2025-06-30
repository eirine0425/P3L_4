@extends('layouts.dashboard')

@section('title', 'Detail Pengiriman - #' . $transaction->transaksi_id)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Pengiriman</h2>
                    <p class="text-muted">Transaksi #{{ $transaction->transaksi_id }}</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.warehouse.shipments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if(!$transaction->pengiriman)
                        <a href="{{ route('dashboard.warehouse.shipments.create', $transaction->transaksi_id) }}" 
                           class="btn btn-success">
                            <i class="fas fa-calendar-plus"></i> Jadwalkan Pengiriman
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Details -->
        <div class="col-md-8">
            <!-- Transaction Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Transaksi:</strong></td>
                                    <td>#{{ $transaction->transaksi_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Pesan:</strong></td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d/m/Y H:i') }}
                                        @if(\Carbon\Carbon::parse($transaction->tanggal_pesan)->hour >= 16)
                                            <br><small class="text-warning">
                                                <i class="fas fa-clock"></i> Dipesan setelah jam 16:00
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Transaksi:</strong></td>
                                    <td>
                                        <span class="badge bg-success">{{ $transaction->status_transaksi }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Harga:</strong></td>
                                    <td><strong>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Pembeli:</strong></td>
                                    <td>{{ $transaction->pembeli->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $transaction->pembeli->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telepon:</strong></td>
                                    <td>{{ $transaction->pembeli->user->phone_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Metode Pengiriman:</strong></td>
                                    <td>{{ $transaction->metode_pengiriman ?? 'Delivery' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            @if($transaction->pengiriman)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Informasi Pengiriman</h5>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                <i class="fas fa-edit"></i> Update Status
                            </button>
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#assignCourierModal">
                                <i class="fas fa-user-edit"></i> Ganti Kurir
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Status Pengiriman:</strong></td>
                                        <td>
                                            @php
                                                $statusClass = match($transaction->pengiriman->status_pengiriman) {
                                                    'Dijadwalkan' => 'info',
                                                    'Menunggu Pengiriman' => 'warning',
                                                    'Dalam Perjalanan' => 'primary',
                                                    'Terkirim' => 'success',
                                                    'Dibatalkan' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ $transaction->pengiriman->status_pengiriman }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kurir:</strong></td>
                                        <td>{{ $transaction->pengiriman->pengirim->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Kirim:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->pengiriman->tanggal_kirim)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Penerima:</strong></td>
                                        <td>{{ $transaction->pengiriman->nama_penerima }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if($transaction->pengiriman->tanggal_terima)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tanggal Terima:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->pengiriman->tanggal_terima)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                @endif
                                
                                @if($transaction->pengiriman->catatan)
                                    <div class="mt-3">
                                        <strong>Catatan:</strong>
                                        <p class="text-muted">{{ $transaction->pengiriman->catatan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                        <h5>Pengiriman Belum Dijadwalkan</h5>
                        <p class="text-muted">Transaksi ini belum memiliki jadwal pengiriman.</p>
                        <a href="{{ route('dashboard.warehouse.shipments.create', $transaction->transaksi_id) }}" 
                           class="btn btn-success">
                            <i class="fas fa-calendar-plus"></i> Jadwalkan Pengiriman
                        </a>
                    </div>
                </div>
            @endif

            <!-- Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Barang yang Dipesan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Penitip</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->detailTransaksi as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($detail->barang->foto_barang)
                                                    <img src="{{ Storage::url($detail->barang->foto_barang) }}" 
                                                         alt="{{ $detail->barang->nama_barang }}" 
                                                         class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $detail->barang->nama_barang }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $detail->barang->deskripsi }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $detail->barang->penitip->user->name ?? '-' }}</td>
                                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Delivery Address -->
            @if($transaction->pembeli->alamat)
                <div class="card mb-4">
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

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($transaction->pengiriman)
                            <a href="{{ route('dashboard.warehouse.shipments.courier-note', $transaction->transaksi_id) }}" 
                               class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-file-pdf"></i> Cetak Nota Kurir
                            </a>
                        @endif
                        
                        <a href="{{ route('dashboard.warehouse.transaction.sales-note', $transaction->transaksi_id) }}" 
                           class="btn btn-outline-secondary" target="_blank">
                            <i class="fas fa-receipt"></i> Cetak Nota Penjualan
                        </a>
                        
                        @if($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Dalam Perjalanan')
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#confirmReceivedModal">
                                <i class="fas fa-check"></i> Konfirmasi Diterima
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shipping Rules Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title">Aturan Pengiriman</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Aturan Jam 4 Sore:</strong>
                        <p class="mb-0 mt-2">
                            Pengiriman untuk pembelian di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama.
                        </p>
                    </div>
                    
                    @if(\Carbon\Carbon::parse($transaction->tanggal_pesan)->hour >= 16)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian:</strong>
                            <p class="mb-0 mt-2">
                                Transaksi ini dipesan setelah jam 16:00, sehingga pengiriman tidak bisa dijadwalkan di hari yang sama.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
@if($transaction->pengiriman)
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.warehouse.shipments.status.update', $transaction->pengiriman->pengiriman_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status_pengiriman" class="form-label">Status Pengiriman</label>
                        <select name="status_pengiriman" id="status_pengiriman" class="form-select" required>
                            <option value="Dijadwalkan" {{ $transaction->pengiriman->status_pengiriman == 'Dijadwalkan' ? 'selected' : '' }}>
                                Dijadwalkan
                            </option>
                            <option value="Menunggu Pengiriman" {{ $transaction->pengiriman->status_pengiriman == 'Menunggu Pengiriman' ? 'selected' : '' }}>
                                Menunggu Pengiriman
                            </option>
                            <option value="Dalam Perjalanan" {{ $transaction->pengiriman->status_pengiriman == 'Dalam Perjalanan' ? 'selected' : '' }}>
                                Dalam Perjalanan
                            </option>
                            <option value="Terkirim" {{ $transaction->pengiriman->status_pengiriman == 'Terkirim' ? 'selected' : '' }}>
                                Terkirim
                            </option>
                            <option value="Dibatalkan" {{ $transaction->pengiriman->status_pengiriman == 'Dibatalkan' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Courier Modal -->
<div class="modal fade" id="assignCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.warehouse.shipments.courier.assign', $transaction->pengiriman->pengiriman_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Ganti Kurir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pengirim_id" class="form-label">Pilih Kurir Baru</label>
                        <select name="pengirim_id" id="pengirim_id" class="form-select" required>
                            <option value="">-- Pilih Kurir --</option>
                            @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}" 
                                        {{ $transaction->pengiriman->pengirim_id == $courier->id ? 'selected' : '' }}>
                                    {{ $courier->name }} - {{ $courier->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Kurir lama dan baru akan menerima notifikasi tentang perubahan penugasan ini.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Ganti Kurir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Received Modal -->
<div class="modal fade" id="confirmReceivedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.warehouse.transaction.confirm-received', $transaction->transaksi_id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Penerimaan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Konfirmasi Penerimaan</strong>
                        <p class="mb-0 mt-2">
                            Apakah Anda yakin barang telah diterima oleh pembeli? 
                            Tindakan ini akan mengubah status pengiriman menjadi "Terkirim" dan menyelesaikan transaksi.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Konfirmasi Diterima</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update page when status changes
    const statusSelect = document.getElementById('status_pengiriman');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'Terkirim') {
                const confirmMsg = 'Mengubah status menjadi "Terkirim" akan menyelesaikan transaksi. Lanjutkan?';
                if (!confirm(confirmMsg)) {
                    this.value = '{{ $transaction->pengiriman->status_pengiriman }}';
                }
            }
        });
    }
});
</script>
@endpush
