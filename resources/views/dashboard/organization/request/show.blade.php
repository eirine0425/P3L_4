@extends('layouts.dashboard')

@section('title', 'Detail Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Detail Request Donasi</h2>
            <p class="text-muted">Informasi lengkap tentang permintaan donasi.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Request</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">ID Request</div>
                        <div class="col-md-8">{{ $request->request_donasi_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Judul</div>
                        <div class="col-md-8">{{ $request->judul }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Deskripsi</div>
                        <div class="col-md-8">{{ $request->deskripsi }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Jumlah Kebutuhan</div>
                        <div class="col-md-8">{{ $request->jumlah_kebutuhan }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Kebutuhan</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($request->tanggal_kebutuhan)->format('d M Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Dibuat</div>
                        <div class="col-md-8">{{ $request->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status</div>
                        <div class="col-md-8">
                            @if($request->status == 'Menunggu Persetujuan')
                                <span class="badge bg-warning">Menunggu Persetujuan</span>
                            @elseif($request->status == 'Disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($request->gambar_path)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Gambar</div>
                        <div class="col-md-8">
                            <img src="{{ asset($request->gambar_path) }}" alt="{{ $request->judul }}" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>
                    @endif
                    
                    @if($request->alasan_penolakan && $request->status == 'Ditolak')
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Alasan Penolakan</div>
                        <div class="col-md-8 text-danger">{{ $request->alasan_penolakan }}</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.organization.requests') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    
                    @if($request->status == 'Menunggu Persetujuan')
                    <a href="{{ route('dashboard.organization.requests.edit', $request->request_donasi_id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Request
                    </a>
                    @endif
                </div>
            </div>
            
            @if(count($relatedDonations) > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Donasi Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Barang</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($relatedDonations as $donation)
                                    <tr>
                                        <td>{{ $donation->donasi_id }}</td>
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
                                            <a href="{{ route('dashboard.organization.donations.show', $donation->donasi_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Request</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Request Dibuat</h6>
                                <p class="timeline-date">{{ $request->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </li>
                        
                        @if($request->status != 'Menunggu Persetujuan')
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">
                                    @if($request->status == 'Disetujui')
                                        Request Disetujui
                                    @else
                                        Request Ditolak
                                    @endif
                                </h6>
                                <p class="timeline-date">{{ $request->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </li>
                        @endif
                        
                        @if($request->status == 'Disetujui' && count($relatedDonations) > 0)
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Donasi Diterima</h6>
                                <p class="timeline-date">{{ $relatedDonations->first()->created_at->format('d M Y H:i') }}</p>
                                <p>Jumlah donasi: {{ count($relatedDonations) }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Jumlah Kebutuhan:</div>
                        <div class="fw-bold">{{ $request->jumlah_kebutuhan }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Donasi Diterima:</div>
                        <div class="fw-bold">{{ count($relatedDonations) }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Sisa Kebutuhan:</div>
                        <div class="fw-bold">{{ max(0, $request->jumlah_kebutuhan - count($relatedDonations)) }}</div>
                    </div>
                    
                    @if($request->status == 'Disetujui')
                    <div class="progress mt-3">
                        @php
                            $percentage = min(100, (count($relatedDonations) / $request->jumlah_kebutuhan) * 100);
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ round($percentage) }}%</div>
                    </div>
                    <p class="text-center mt-2 small">Progress Pemenuhan Kebutuhan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
