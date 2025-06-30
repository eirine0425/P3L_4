@extends('layouts.dashboard')

@section('title', 'Detail Pengiriman')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Pengiriman</h2>
                    <p class="text-muted">Transaksi ID: {{ $transaction->transaksi_id }}</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.warehouse.shipments.ready') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    @if($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Dijadwalkan')
                        <a href="{{ route('dashboard.warehouse.shipping.label', $transaction->transaksi_id) }}" 
                           class="btn btn-info" target="_blank">
                            <i class="fas fa-print me-2"></i>Print Label
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID Transaksi:</strong></td>
                            <td>{{ $transaction->transaksi_id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal:</strong></td>
                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
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
                        <tr>
                            <td><strong>Jumlah Item:</strong></td>
                            <td>{{ $transaction->detailTransaksi->count() }} item</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user me-2"></i>Informasi Pembeli
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $transaction->pembeli->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $transaction->pembeli->user->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Telepon:</strong></td>
                            <td>{{ $transaction->pembeli->user->phone ?? '-' }}</td>
                        </tr>
                        @if($transaction->pembeli->user->alamat->first())
                        <tr>
                            <td><strong>Alamat:</strong></td>
                            <td>{{ $transaction->pembeli->user->alamat->first()->alamat_lengkap }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    @if($transaction->pengiriman)
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Metode:</strong></td>
                                <td>
                                    @if($transaction->pengiriman->metode_pengiriman == 'Pickup')
                                        <span class="badge bg-success">
                                            <i class="fas fa-hand-paper me-1"></i>Pickup
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="fas fa-truck me-1"></i>{{ $transaction->pengiriman->metode_pengiriman }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($transaction->pengiriman->status_pengiriman == 'Menunggu Pengiriman')
                                        <span class="badge bg-warning">Menunggu Pengiriman</span>
                                    @elseif($transaction->pengiriman->status_pengiriman == 'Dijadwalkan')
                                        <span class="badge bg-info">Dijadwalkan</span>
                                    @elseif($transaction->pengiriman->status_pengiriman == 'Sedang Dikirim')
                                        <span class="badge bg-primary">Sedang Dikirim</span>
                                    @elseif($transaction->pengiriman->status_pengiriman == 'Terkirim')
                                        <span class="badge bg-success">Terkirim</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $transaction->pengiriman->status_pengiriman }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($transaction->pengiriman->tanggal_pengiriman)
                            <tr>
                                <td><strong>Tanggal Kirim:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($transaction->pengiriman->tanggal_pengiriman)->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @if($transaction->pengiriman->jam_pengiriman)
                            <tr>
                                <td><strong>Jam:</strong></td>
                                <td>{{ $transaction->pengiriman->jam_pengiriman }}</td>
                            </tr>
                            @endif
                            @if($transaction->pengiriman->nomor_resi)
                            <tr>
                                <td><strong>No. Resi:</strong></td>
                                <td><code>{{ $transaction->pengiriman->nomor_resi }}</code></td>
                            </tr>
                            @endif
                            @if($transaction->pengiriman->alamat_pengiriman)
                            <tr>
                                <td><strong>Alamat Kirim:</strong></td>
                                <td>{{ $transaction->pengiriman->alamat_pengiriman }}</td>
                            </tr>
                            @endif
                            @if($transaction->pengiriman->catatan)
                            <tr>
                                <td><strong>Catatan:</strong></td>
                                <td>{{ $transaction->pengiriman->catatan }}</td>
                            </tr>
                            @endif
                        </table>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <p class="text-muted">Informasi pengiriman belum tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Update Status Form -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-edit me-2"></i>Update Status Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.warehouse.shipment.update-status', $transaction->transaksi_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Status Pengiriman</label>
                            <select name="status_pengiriman" class="form-select" required>
                                <option value="">Pilih Status</option>
                                <option value="Menunggu Pengiriman" {{ ($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Menunggu Pengiriman') ? 'selected' : '' }}>Menunggu Pengiriman</option>
                                <option value="Dijadwalkan" {{ ($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Dijadwalkan') ? 'selected' : '' }}>Dijadwalkan</option>
                                <option value="Sedang Dikirim" {{ ($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Sedang Dikirim') ? 'selected' : '' }}>Sedang Dikirim</option>
                                <option value="Terkirim" {{ ($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Terkirim') ? 'selected' : '' }}>Terkirim</option>
                                <option value="Dibatalkan" {{ ($transaction->pengiriman && $transaction->pengiriman->status_pengiriman == 'Dibatalkan') ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengiriman</label>
                            <input type="date" name="tanggal_pengiriman" class="form-control" 
                                   value="{{ $transaction->pengiriman ? $transaction->pengiriman->tanggal_pengiriman : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Pengiriman</label>
                            <input type="time" name="jam_pengiriman" class="form-control" 
                                   value="{{ $transaction->pengiriman ? $transaction->pengiriman->jam_pengiriman : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Resi</label>
                            <input type="text" name="nomor_resi" class="form-control" 
                                   placeholder="Masukkan nomor resi..."
                                   value="{{ $transaction->pengiriman ? $transaction->pengiriman->nomor_resi : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" 
                                      placeholder="Catatan tambahan...">{{ $transaction->pengiriman ? $transaction->pengiriman->catatan : '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Items List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-box me-2"></i>Daftar Barang
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($itemsWithPhotos as $detail)
                        <div class="border rounded p-3 mb-3">
                            <!-- Photo Gallery -->
                            <div class="row mb-3">
                                @foreach($detail->photos->take(2) as $index => $photo)
                                    <div class="col-6">
                                        @if(isset($photo['is_placeholder']) && $photo['is_placeholder'])
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="height: 80px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/' . $photo['path']) }}" 
                                                 alt="{{ $detail->barang->nama_barang }}" 
                                                 class="img-fluid rounded cursor-pointer"
                                                 style="height: 80px; width: 100%; object-fit: cover;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#photoModal{{ $detail->detail_transaksi_id }}_{{ $index }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Item Details -->
                            <h6 class="fw-bold">{{ $detail->barang->nama_barang }}</h6>
                            <div class="small text-muted mb-2">
                                <div><strong>Kategori:</strong> {{ $detail->barang->kategori->nama_kategori ?? '-' }}</div>
                                <div><strong>Kondisi:</strong> 
                                    @if($detail->barang->kondisi == 'baru')
                                        <span class="badge bg-primary">Baru</span>
                                    @elseif($detail->barang->kondisi == 'sangat_layak')
                                        <span class="badge bg-success">Sangat Layak</span>
                                    @else
                                        <span class="badge bg-warning">Layak</span>
                                    @endif
                                </div>
                                <div><strong>Penitip:</strong> {{ $detail->barang->penitip->user->name ?? '-' }}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Rp {{ number_format($detail->harga, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-info">Qty: {{ $detail->jumlah ?? 1 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Modals -->
                        @foreach($detail->photos as $index => $photo)
                            @if(!isset($photo['is_placeholder']) || !$photo['is_placeholder'])
                                <div class="modal fade" id="photoModal{{ $detail->detail_transaksi_id }}_{{ $index }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $detail->barang->nama_barang }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $photo['path']) }}" 
                                                     alt="{{ $detail->barang->nama_barang }}" 
                                                     class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const status = this.querySelector('select[name="status_pengiriman"]').value;
        if (!status) {
            e.preventDefault();
            alert('Pilih status pengiriman terlebih dahulu');
            return;
        }
        
        if (!confirm(`Apakah Anda yakin ingin mengubah status menjadi "${status}"?`)) {
            e.preventDefault();
        }
    });
</script>
@endpush
