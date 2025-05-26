@extends('layouts.dashboard')

@section('title', 'Organization Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Organisasi</h2>
            <p class="text-muted">Selamat datang di panel kontrol Organisasi ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>{{ $totalDonations }}</h3>
                <p>Total Donasi</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-clock"></i>
                <h3>{{ $pendingRequests }}</h3>
                <p>Request Tertunda</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-check-circle"></i>
                <h3>{{ $approvedRequests }}</h3>
                <p>Request Disetujui</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Donasi Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDonations as $donation)
                                    <tr>
                                        <td>{{ $donation->barang->nama_barang }}</td>
                                        <td>{{ $donation->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($donation->status == 'Menunggu Pengambilan')
                                                <span class="badge bg-warning">Menunggu Pengambilan</span>
                                            @elseif($donation->status == 'Diproses')
                                                <span class="badge bg-info">Diproses</span>
                                            @elseif($donation->status == 'Selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.organization.donations') }}" class="btn btn-primary">Lihat Semua Donasi</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Request Donasi Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRequests as $request)
                                    <tr>
                                        <td>{{ $request->judul }}</td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($request->status == 'Menunggu Persetujuan')
                                                <span class="badge bg-warning">Menunggu Persetujuan</span>
                                            @elseif($request->status == 'Disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>
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
                    <a href="{{ route('dashboard.organization.requests') }}" class="btn btn-primary">Lihat Semua Request</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Organisasi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Umum</h6>
                            <table class="table">
                                <tr>
                                    <th>Nama Organisasi</th>
                                    <td>{{ $organization->nama_organisasi }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon</th>
                                    <td>{{ $organization->no_telepon }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $organization->alamat }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Tambahan</h6>
                            <table class="table">
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $organization->deskripsi }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Bergabung</th>
                                    <td>{{ date('d M Y', strtotime($organization->created_at)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary">Edit Profil Organisasi</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
