@extends('layouts.dashboard')

@section('title', 'Kelola Penitip')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Penitip</h3>
                        <a href="{{ route('dashboard.admin.penitips.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Daftarkan Penitip Baru
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(isset($penitips) && $penitips->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. KTP</th>
                                        <th>Badge</th>
                                        <th>Tanggal Registrasi</th>
                                        <th>Point Donasi</th>
                                        <th>Saldo</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penitips as $penitip)
                                        <tr>
                                            <td>{{ $penitip->penitip_id }}</td>
                                            <td>{{ $penitip->nama }}</td>
                                            <td>{{ $penitip->user->email ?? 'N/A' }}</td>
                                            <td>{{ $penitip->no_ktp }}</td>
                                            <td>
                                                @if($penitip->badge && $penitip->badge !== 'no')
                                                    <span class="badge badge-{{ $penitip->badge === 'gold' ? 'warning' : ($penitip->badge === 'silver' ? 'secondary' : ($penitip->badge === 'bronze' ? 'info' : 'primary')) }}">
                                                        {{ ucfirst($penitip->badge) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-light">No Badge</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($penitip->tanggal_registrasi)->format('d/m/Y') }}</td>
                                            <td>{{ number_format($penitip->point_donasi ?? 0) }}</td>
                                            <td>Rp {{ number_format($penitip->saldo ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                @if($penitip->user && $penitip->user->email_verified_at)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dashboard.admin.penitips.show', $penitip->penitip_id) }}" 
                                                       class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.admin.penitips.edit', $penitip->penitip_id) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('dashboard.admin.penitips.destroy', $penitip->penitip_id) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus penitip ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $penitips->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada penitip yang terdaftar</h5>
                            <p class="text-muted">Klik tombol "Daftarkan Penitip Baru" untuk menambahkan penitip pertama.</p>
                            <a href="{{ route('dashboard.admin.penitips.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Daftarkan Penitip Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
