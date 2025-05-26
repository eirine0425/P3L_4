@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Profil Saya</h2>
            <p class="text-muted">Kelola informasi profil dan pengaturan akun Anda.</p>
        </div>
    </div>
    
    @include('partials.alerts')
    
    <div class="row">
        <!-- Profile Information Card -->
        <div class="col-lg-4">
            <div class="card mb-4">
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
                    <h5 class="my-2">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h5>
                    <p class="text-muted mb-1">{{ Auth::user()->email }}</p>
                    <p class="text-muted mb-4">
                        <span class="badge bg-primary">{{ Auth::user()->role->nama_role ?? 'User' }}</span>
                    </p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i>Edit Profil
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-8">
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
                            <p class="text-muted mb-0">{{ Auth::user()->name ?? 'Belum diisi' }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <p class="mb-0 fw-bold">Email</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <p class="mb-0 fw-bold">Role</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ Auth::user()->role->nama_role ?? 'User' }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0 fw-bold">Tanggal Registrasi</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">
                                {{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y H:i') : 'Tidak tersedia' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Role-specific Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Aksi Khusus Role
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $role = strtolower(Auth::user()->role->nama_role ?? '');
                    @endphp
                    
                    @if($role == 'pembeli')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('buyer.profile.transaction-history') }}" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-bag me-1"></i>Riwayat Transaksi
                            </a>
                            <a href="{{ route('buyer.profile.rewards') }}" class="btn btn-outline-warning">
                                <i class="fas fa-star me-1"></i>Poin Reward
                            </a>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-shopping-cart me-1"></i>Keranjang Belanja
                            </a>
                        </div>
                    @elseif($role == 'penitip' || $role == 'penjual')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('consignor.items') }}" class="btn btn-outline-primary">
                                <i class="fas fa-box me-1"></i>Barang Saya
                            </a>
                            <a href="{{ route('consignor.transactions') }}" class="btn btn-outline-success">
                                <i class="fas fa-chart-line me-1"></i>Transaksi Penitipan
                            </a>
                        </div>
                    @elseif($role == 'pegawai' || $role == 'gudang')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('dashboard.warehouse.inventory') }}" class="btn btn-outline-primary">
                                <i class="fas fa-warehouse me-1"></i>Inventori
                            </a>
                            <a href="{{ route('dashboard.warehouse.shipments') }}" class="btn btn-outline-info">
                                <i class="fas fa-truck me-1"></i>Pengiriman
                            </a>
                        </div>
                    @elseif($role == 'cs')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('cs.discussions') }}" class="btn btn-outline-primary">
                                <i class="fas fa-comments me-1"></i>Diskusi Produk
                            </a>
                            <a href="{{ route('cs.payment.verifications') }}" class="btn btn-outline-warning">
                                <i class="fas fa-credit-card me-1"></i>Verifikasi Pembayaran
                            </a>
                        </div>
                    @elseif($role == 'admin')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('admin.employees') }}" class="btn btn-outline-primary">
                                <i class="fas fa-users me-1"></i>Kelola Pegawai
                            </a>
                            <a href="{{ route('admin.organizations') }}" class="btn btn-outline-info">
                                <i class="fas fa-building me-1"></i>Kelola Organisasi
                            </a>
                        </div>
                    @elseif($role == 'owner')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('owner.reports.sales') }}" class="btn btn-outline-primary">
                                <i class="fas fa-chart-bar me-1"></i>Laporan Penjualan
                            </a>
                            <a href="{{ route('owner.donations') }}" class="btn btn-outline-success">
                                <i class="fas fa-heart me-1"></i>Kelola Donasi
                            </a>
                        </div>
                    @elseif($role == 'hunter')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('dashboard.hunter.riwayat-penjemputan') }}" class="btn btn-outline-primary">
                                <i class="fas fa-motorcycle me-1"></i>Riwayat Penjemputan
                            </a>
                            <a href="{{ route('dashboard.hunter.komisi') }}" class="btn btn-outline-success">
                                <i class="fas fa-money-bill me-1"></i>Komisi
                            </a>
                        </div>
                    @elseif($role == 'organisasi')
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('organization.donation.requests') }}" class="btn btn-outline-primary">
                                <i class="fas fa-hand-holding-heart me-1"></i>Request Donasi
                            </a>
                            <a href="{{ route('organization.received.donations') }}" class="btn btn-outline-success">
                                <i class="fas fa-gift me-1"></i>Donasi Diterima
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Tidak ada aksi khusus untuk role ini.</p>
                    @endif
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
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ Auth::user()->email }}" required>
                    </div>
                    <hr>
                    <h6>Ubah Password (Opsional)</h6>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation">
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
</style>
@endsection
