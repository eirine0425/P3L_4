@extends('layouts.dashboard')

@section('title', 'Daftar Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2>Request Donasi</h2>
                <p class="text-muted">Kelola semua permintaan donasi yang dibuat oleh organisasi Anda.</p>
            </div>
            <div>
                <a href="{{ route('dashboard.organization.requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Request Baru
                </a>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Filter Request</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.organization.requests') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="Menunggu Persetujuan" {{ request('status') == 'Menunggu Persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Cari judul atau deskripsi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('dashboard.organization.requests') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Request Donasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Tanggal Kebutuhan</th>
                                    <th>Jumlah Kebutuhan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>{{ $request->request_donasi_id }}</td>
                                        <td>{{ $request->judul }}</td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($request->tanggal_kebutuhan)->format('d M Y') }}</td>
                                        <td>{{ $request->jumlah_kebutuhan }}</td>
                                        <td>
                                            @if($request->status == 'Menunggu Persetujuan')
                                                <span class="badge bg-warning">Menunggu Persetujuan</span>
                                            @elseif($request->status == 'Disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard.organization.requests.show', $request->request_donasi_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            
                                            @if($request->status == 'Menunggu Persetujuan')
                                            <a href="{{ route('dashboard.organization.requests.edit', $request->request_donasi_id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data request donasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
