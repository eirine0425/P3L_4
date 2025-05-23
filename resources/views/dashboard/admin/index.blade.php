@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Admin</h2>
            <p class="text-muted">Selamat datang di panel kontrol Admin ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-users"></i>
                <h3>{{ $totalUsers }}</h3>
                <p>Total Pengguna</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-user-tag"></i>
                <h3>{{ $totalRoles }}</h3>
                <p>Total Peran</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-user-tie"></i>
                <h3>{{ $totalEmployees }}</h3>
                <p>Total Pegawai</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-building"></i>
                <h3>{{ $totalOrganizations }}</h3>
                <p>Total Organisasi</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengguna Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge bg-info">{{ $user->role->nama_role }}</span></td>
                                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.admin.users') }}" class="btn btn-primary">Lihat Semua Pengguna</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-user-plus text-success me-2"></i>
                                    <span>Pengguna baru terdaftar</span>
                                </div>
                                <small class="text-muted">5 menit yang lalu</small>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-edit text-primary me-2"></i>
                                    <span>Peran diperbarui</span>
                                </div>
                                <small class="text-muted">1 jam yang lalu</small>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-trash text-danger me-2"></i>
                                    <span>Pengguna dihapus</span>
                                </div>
                                <small class="text-muted">3 jam yang lalu</small>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-user-shield text-warning me-2"></i>
                                    <span>Hak akses diubah</span>
                                </div>
                                <small class="text-muted">5 jam yang lalu</small>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-user-plus text-success me-2"></i>
                                    <span>Pegawai baru ditambahkan</span>
                                </div>
                                <small class="text-muted">1 hari yang lalu</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Distribusi Peran Pengguna</h5>
                </div>
                <div class="card-body">
                    <canvas id="userRolesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data untuk grafik distribusi peran pengguna
    var userRolesCtx = document.getElementById('userRolesChart').getContext('2d');
    var userRolesChart = new Chart(userRolesCtx, {
        type: 'pie',
        data: {
            labels: ['Admin', 'Pegawai Gudang', 'CS', 'Penitip', 'Pembeli', 'Organisasi', 'Owner'],
            datasets: [{
                data: [
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'Admin'); })->count() }},
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'Pegawai Gudang'); })->count() }},
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'CS'); })->count() }},
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'Penitip'); })->count() }},
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'Pembeli'); })->count() }},
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'Organisasi'); })->count() }},
                    {{ \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'Owner'); })->count() }}
                ],
                backgroundColor: [
                    '#4CAF50', '#2196F3', '#FFC107', '#9C27B0', '#F44336', '#FF9800', '#795548'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
