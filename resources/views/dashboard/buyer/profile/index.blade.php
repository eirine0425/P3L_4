@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Profil Saya</h4>
                    <p class="text-muted mb-0">Kelola informasi profil dan pengaturan akun Anda.</p>
                </div>

                <div class="card-body">
                    @include('partials.alerts')
                    
                    <div class="row">
                        <!-- Profile Navigation -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Menu Profil</h6>
                                    <div class="list-group list-group-flush">
                                        <a href="{{ route('buyer.profile') }}" class="list-group-item list-group-item-action active">
                                            <i class="fas fa-user"></i> Profil Saya
                                        </a>
                                        <a href="{{ route('buyer.profile.transaction-history') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-history"></i> Riwayat Transaksi
                                        </a>
                                        <a href="{{ route('buyer.profile.rewards') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-gift"></i> Poin Reward
                                        </a>
                                        <a href="{{ route('buyer.profile.ratings') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-star"></i> Rating & Ulasan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Content -->
                        <div class="col-md-9">
                            <div class="row">
                                <!-- Profile Information Card -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-user me-2"></i>Informasi Profil
                                            </h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <div class="avatar-circle mx-auto mb-3">
                                                    <i class="fas fa-user fa-3x text-primary"></i>
                                                </div>
                                            </div>
                                            <h5 class="my-2">{{ $buyer->nama ?? Auth::user()->name }}</h5>
                                            <p class="text-muted mb-1">{{ $buyer->user->email ?? Auth::user()->email }}</p>
                                            <p class="text-muted mb-4">
                                                Member sejak {{ isset($buyer->tanggal_registrasi) ? \Carbon\Carbon::parse($buyer->tanggal_registrasi)->format('d M Y') : \Carbon\Carbon::parse(Auth::user()->created_at)->format('d M Y') }}
                                            </p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                                <i class="fas fa-edit me-1"></i>Edit Profil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Loyalty Points Card -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-star me-2 text-warning"></i>Poin Loyalitas
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="me-3">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                                        <i class="fas fa-coins fa-2x text-warning"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h3 class="mb-0 text-warning">{{ number_format($buyer->poin_loyalitas ?? 0) }}</h3>
                                                    <p class="text-muted mb-0">Poin tersedia</p>
                                                </div>
                                            </div>
                                            <hr>
                                            <p class="mb-2">
                                                <small class="text-muted">
                                                    Dapatkan poin dengan setiap pembelian dan tukarkan dengan diskon atau produk eksklusif!
                                                </small>
                                            </p>
                                            <a href="{{ route('buyer.profile.rewards') }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-history me-1"></i>Lihat Riwayat Poin
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Details Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Detail Akun
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-3">
                                            <p class="mb-0 fw-bold">Nama Lengkap</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $buyer->nama ?? Auth::user()->name }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-3">
                                        <div class="col-sm-3">
                                            <p class="mb-0 fw-bold">Email</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $buyer->user->email ?? Auth::user()->email }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-3">
                                        <div class="col-sm-3">
                                            <p class="mb-0 fw-bold">ID Pembeli</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">#{{ isset($buyer->pembeli_id) ? str_pad($buyer->pembeli_id, 6, '0', STR_PAD_LEFT) : str_pad(Auth::user()->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0 fw-bold">Tanggal Registrasi</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">
                                                {{ isset($buyer->tanggal_registrasi) ? \Carbon\Carbon::parse($buyer->tanggal_registrasi)->format('d M Y H:i') : \Carbon\Carbon::parse(Auth::user()->created_at)->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Stats Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>Statistik Belanja
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-1">{{ isset($transaksi) ? $transaksi->count() : 0 }}</h4>
                                                <small class="text-muted">Total Transaksi</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-1">
                                                {{ isset($transaksi) ? $transaksi->where('status_transaksi', 'Selesai')->count() : 0 }}
                                            </h4>
                                            <small class="text-muted">Selesai</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Transactions Card -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shopping-bag me-2"></i>Transaksi Terbaru
                                    </h5>
                                    <a href="{{ route('buyer.profile.transaction-history') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i>Lihat Semua
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if(isset($transaksi) && $transaksi->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID Transaksi</th>
                                                        <th>Tanggal</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transaksi->take(5) as $trx)
                                                        <tr>
                                                            <td>
                                                                <span class="fw-bold">#{{ str_pad($trx->transaksi_id, 6, '0', STR_PAD_LEFT) }}</span>
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($trx->tanggal_pesan)->format('d M Y') }}</td>
                                                            <td>
                                                                <span class="fw-bold text-success">
                                                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($trx->status_transaksi == 'Menunggu Pembayaran')
                                                                    <span class="badge bg-warning">Menunggu Pembayaran</span>
                                                                @elseif($trx->status_transaksi == 'Diproses')
                                                                    <span class="badge bg-info">Diproses</span>
                                                                @elseif($trx->status_transaksi == 'Dikirim')
                                                                    <span class="badge bg-primary">Dikirim</span>
                                                                @elseif($trx->status_transaksi == 'Selesai')
                                                                    <span class="badge bg-success">Selesai</span>
                                                                @else
                                                                    <span class="badge bg-danger">Dibatalkan</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('buyer.profile.transaction-detail', $trx->transaksi_id) }}" 
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Anda belum memiliki riwayat transaksi.</p>
                                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                                <i class="fas fa-shopping-cart me-1"></i>Mulai Belanja
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Profil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('buyer.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" 
                               value="{{ $buyer->nama ?? Auth::user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ $buyer->user->email ?? Auth::user()->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="no_telepon" name="no_telepon" 
                               value="{{ $buyer->no_telepon ?? '' }}">
                    </div>
                    <hr>
                    <h6>Ubah Password (Opsional)</h6>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" 
                               name="new_password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 80px;
    height: 80px;
    background-color: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #e9ecef;
}

.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endsection
