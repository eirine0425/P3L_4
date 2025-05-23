@extends('layouts.dashboard')

@section('title', 'Riwayat Komisi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Komisi</h1>
        <a href="{{ route('dashboard.hunter') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Komisi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.hunter.komisi') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="penitip_id">Penitip</label>
                    <select class="form-control" id="penitip_id" name="penitip_id">
                        <option value="">Semua Penitip</option>
                        @foreach($komisi->pluck('penitip')->unique('penitip_id') as $penitip)
                            <option value="{{ $penitip->penitip_id }}" {{ request('penitip_id') == $penitip->penitip_id ? 'selected' : '' }}>
                                {{ $penitip->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('dashboard.hunter.komisi') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Komisi Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Total Komisi</h6>
        </div>
        <div class="card-body">
            <h2 class="font-weight-bold text-primary">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</h2>
            <p class="mb-0">Total komisi dari {{ $komisi->total() }} transaksi</p>
        </div>
    </div>

    <!-- Komisi Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Komisi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Penitip</th>
                            <th>Persentase</th>
                            <th>Nominal Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($komisi as $k)
                        <tr>
                            <td>{{ $k->created_at->format('d/m/Y') }}</td>
                            <td>{{ $k->barang->nama_barang }}</td>
                            <td>{{ $k->penitip->user->name }}</td>
                            <td>{{ $k->persentase }}%</td>
                            <td>Rp {{ number_format($k->nominal_komisi, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data komisi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $komisi->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
