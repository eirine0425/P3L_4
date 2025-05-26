@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Dashboard Admin</h1>
                <div class="btn-group">
                    <a href="{{ route('dashboard.admin.penitips.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Daftarkan Penitip
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalUsers ?? 0 }}</h4>
                            <p class="mb-0">Total Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.admin.users') }}" class="text-white">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalPenitips ?? 0 }}</h4>
                            <p class="mb-0">Total Penitip</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.admin.penitips') }}" class="text-white">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalEmployees ?? 0 }}</h4>
                            <p class="mb-0">Total Pegawai</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.admin.employees') }}" class="text-white">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalOrganizations ?? 0 }}</h4>
                            <p class="mb-0">Total Organisasi</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.admin.organizations') }}" class="text-white">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard.admin.penitips.create') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                                Daftarkan Penitip Baru
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard.admin.penitips') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-list fa-2x d-block mb-2"></i>
                                Kelola Penitip
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard.admin.users') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                Kelola Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard.admin.roles') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-user-tag fa-2x d-block mb-2"></i>
                                Kelola Roles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Users Terbaru</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentUsers) && $recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ $user->role->nama_role ?? 'No Role' }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-success">Verified</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada user yang terdaftar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-block {
    display: block;
    width: 100%;
    text-align: center;
    padding: 1rem;
    height: auto;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-footer {
    background-color: rgba(0, 0, 0, 0.03);
}

.card-footer a {
    text-decoration: none;
}

.card-footer a:hover {
    text-decoration: underline;
}
</style>
@endpush
