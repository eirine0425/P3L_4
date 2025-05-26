@extends('layouts.dashboard')

@section('title', 'Riwayat Penjemputan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Penjemputan</h1>
        <a href="{{ route('dashboard.hunter') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Penjemputan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.hunter.riwayat-penjemputan') }}" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Penjemputan" {{ request('status') == 'Menunggu Penjemputan' ? 'selected' : '' }}>Menunggu Penjemputan</option>
                        <option value="Dalam Proses" {{ request('status') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('dashboard.hunter.riwayat-penjemputan') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Penjemputan Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penjemputan</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export:</div>
                    <a class="dropdown-item" href="#"><i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>PDF</a>
                    <a class="dropdown-item" href="#"><i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Penitip</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPenjemputan as $penjemputan)
                        <tr>
                            <td>{{ $penjemputan->transaksi_penitipan_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($penjemputan->tanggal_penjemputan)->format('d/m/Y') }}</td>
                            <td>{{ $penjemputan->barang->nama_barang }}</td>
                            <td>{{ $penjemputan->penitip->user->name }}</td>
                            <td>{{ Str::limit($penjemputan->alamat_penjemputan, 30) }}</td>
                            <td>
                                @if($penjemputan->status == 'Menunggu Penjemputan')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($penjemputan->status == 'Dalam Proses')
                                    <span class="badge badge-info">Proses</span>
                                @elseif($penjemputan->status == 'Selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($penjemputan->status == 'Dibatalkan')
                                    <span class="badge badge-danger">Batal</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dashboard.hunter.detail-penjemputan', $penjemputan->transaksi_penitipan_id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data penjemputan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $riwayatPenjemputan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Status Summary -->
    <div class="row">
        <!-- Menunggu Penjemputan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Penjemputan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $riwayatPenjemputan->where('status', 'Menunggu Penjemputan')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dalam Proses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Dalam Proses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $riwayatPenjemputan->where('status', 'Dalam Proses')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $riwayatPenjemputan->where('status', 'Selesai')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dibatalkan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Dibatalkan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $riwayatPenjemputan->where('status', 'Dibatalkan')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
