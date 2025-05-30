@extends('layouts.dashboard')

@section('title', 'Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Pengambilan Barang</h2>
            <p class="text-muted">Kelola pengambilan barang yang sudah melewati batas penitipan</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $expiredItems ?? 0 }}</h3>
                            <p>Barang Kadaluarsa</p>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $scheduledPickups ?? 0 }}</h3>
                            <p>Pengambilan Terjadwal</p>
                        </div>
                        <div>
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>{{ $completedPickups ?? 0 }}</h3>
                            <p>Pengambilan Selesai</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expired Items List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Barang Yang Perlu Diambil
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($items) && count($items) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Batas Penitipan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                @if($item->foto_barang)
                                                    <img src="{{ asset('storage/' . $item->foto_barang) }}" 
                                                         alt="{{ $item->nama_barang }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                <br><small class="text-muted">ID: {{ $item->barang_id }}</small>
                                            </td>
                                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                {{ $item->batas_penitipan->format('d M Y') }}
                                                <br>
                                                <span class="badge bg-danger">
                                                    Lewat {{ abs($item->sisa_hari) }} hari
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->status == 'belum_terjual')
                                                    <span class="badge bg-warning">Perlu Diambil</span>
                                                @elseif($item->status == 'diambil_kembali')
                                                    <span class="badge bg-success">Sudah Diambil</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->status != 'diambil_kembali')
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="schedulePickup({{ $item->barang_id }})" 
                                                            title="Jadwalkan Pengambilan">
                                                        <i class="fas fa-calendar-plus me-1"></i> Jadwalkan Pengambilan
                                                    </button>
                                                @else
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i> Sudah Diambil
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(isset($items) && method_exists($items, 'links'))
                            <div class="mt-3">
                                {{ $items->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>Tidak Ada Barang Yang Perlu Diambil</h5>
                            <p class="text-muted">Semua barang Anda masih dalam masa penitipan atau sudah terjual.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scheduled Pickups -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-check text-info me-2"></i>
                        Jadwal Pengambilan
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($scheduledPickupsList) && count($scheduledPickupsList) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Metode</th>
                                        <th>Alamat</th>
                                        <th>Jumlah Barang</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scheduledPickupsList as $pickup)
                                        <tr>
                                            <td>{{ $pickup->scheduled_date->format('d M Y') }}</td>
                                            <td>{{ $pickup->pickup_method_text }}</td>
                                            <td>{{ Str::limit($pickup->pickup_address, 30) }}</td>
                                            <td>{{ $pickup->total_items }}</td>
                                            <td>
                                                <span class="badge {{ $pickup->status_badge_class }}">
                                                    {{ $pickup->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>Belum Ada Jadwal Pengambilan</h5>
                            <p class="text-muted">Jadwalkan pengambilan untuk barang yang sudah melewati masa penitipan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Pickup Modal -->
<div class="modal fade" id="schedulePickupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jadwalkan Pengambilan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="schedulePickupForm" method="POST" action="{{ route('consignor.schedule-pickup') }}">
                @csrf
                <input type="hidden" name="barang_id" id="barang_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Metode Pengambilan</label>
                            <select name="pickup_method" class="form-select" required>
                                <option value="">Pilih metode pengambilan...</option>
                                <option value="self_pickup">Ambil Sendiri</option>
                                <option value="courier_delivery">Minta Dikirim</option>
                            </select>
                            <div class="form-text">Pilih cara pengambilan barang Anda</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pengambilan</label>
                            <input type="date" name="scheduled_date" class="form-control" required
                                   min="{{ date('Y-m-d') }}">
                            <div class="form-text">Pilih tanggal yang Anda inginkan</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Waktu Pengambilan</label>
                            <select name="scheduled_time" class="form-select">
                                <option value="">Pilih waktu...</option>
                                <option value="09:00 - 12:00">Pagi (09:00 - 12:00)</option>
                                <option value="13:00 - 16:00">Siang (13:00 - 16:00)</option>
                                <option value="16:00 - 18:00">Sore (16:00 - 18:00)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="contact_phone" class="form-control" 
                                   placeholder="Nomor telepon yang bisa dihubungi">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Pengambilan/Pengiriman</label>
                        <textarea name="pickup_address" class="form-control" rows="3" 
                                  placeholder="Alamat lengkap untuk pengambilan atau pengiriman barang"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Catatan tambahan untuk pengambilan (opsional)"></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> Setelah menjadwalkan pengambilan, tim kami akan mengkonfirmasi jadwal Anda.
                        Pastikan data yang dimasukkan sudah benar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-calendar-check me-1"></i>Jadwalkan Pengambilan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function schedulePickup(barangId) {
        document.getElementById('barang_id').value = barangId;
        const modal = new bootstrap.Modal(document.getElementById('schedulePickupModal'));
        modal.show();
    }
</script>
@endpush
