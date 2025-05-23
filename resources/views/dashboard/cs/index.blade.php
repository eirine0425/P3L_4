@extends('layouts.dashboard')

@section('title', 'Dashboard CS')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Customer Service</h2>
            <p class="text-muted">Selamat datang di panel kontrol Customer Service ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-user-tie"></i>
                <h3>{{ $totalPenitip ?? 0 }}</h3>
                <p>Total Penitip</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-shopping-bag"></i>
                <h3>{{ $totalPembeli ?? 0 }}</h3>
                <p>Total Pembeli</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-clipboard-check"></i>
                <h3>{{ $verifikasiTertunda ?? 0 }}</h3>
                <p>Menunggu Verifikasi</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-comments"></i>
                <h3>{{ $diskusiBelumDibalas ?? 0 }}</h3>
                <p>Diskusi Belum Dibalas</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Diskusi Produk Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Produk</th>
                                    <th>Pertanyaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($diskusiTerbaru ?? [] as $diskusi)
                                    <tr>
                                        <td>{{ $diskusi->user->name ?? 'Anonim' }}</td>
                                        <td>{{ $diskusi->barang->nama_barang ?? '-' }}</td>
                                        <td>{{ Str::limit($diskusi->pertanyaan ?? '', 30) }}</td>
                                        <td>
                                            @if($diskusi->balasan)
                                                <span class="badge bg-success">Terjawab</span>
                                            @else
                                                <span class="badge bg-warning">Belum Dijawab</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-success"><i class="fas fa-reply"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada diskusi terbaru</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.cs.discussions') }}" class="btn btn-primary">Lihat Semua Diskusi</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Barang yang Perlu Diverifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Penitip</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangUntukVerifikasi ?? [] as $barang)
                                    <tr>
                                        <td>{{ $barang->nama_barang ?? '-' }}</td>
                                        <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                                        <td>{{ $barang->penitip->user->name ?? '-' }}</td>
                                        <td>{{ $barang->created_at ? $barang->created_at->format('d M Y') : '-' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada barang yang perlu diverifikasi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Distribusi Pelanggan</h5>
                </div>
                <div class="card-body">
                    <canvas id="customerDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var customerDistributionCtx = document.getElementById('customerDistributionChart').getContext('2d');
    var customerDistributionChart = new Chart(customerDistributionCtx, {
        type: 'pie',
        data: {
            labels: ['Penitip', 'Pembeli'],
            datasets: [{
                data: [{{ $totalPenitip ?? 0 }}, {{ $totalPembeli ?? 0 }}],
                backgroundColor: ['#4CAF50', '#2196F3']
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
