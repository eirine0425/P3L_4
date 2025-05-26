@extends('layouts.dashboard')

@section('title', 'Poin Reward')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('buyer.profile.index') }}">Profil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Poin Reward</li>
                </ol>
            </nav>
            <h2>Poin Reward Saya</h2>
            <p class="text-muted">Kelola dan pantau poin loyalitas Anda.</p>
        </div>
    </div>
    
    @include('partials.alerts')
    
    <div class="row">
        <!-- Current Points Summary -->
        <div class="col-lg-4">
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="card-title mb-0 text-warning">
                        <i class="fas fa-star me-2"></i>Poin Saat Ini
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-4 mx-auto" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-coins fa-3x text-warning"></i>
                        </div>
                    </div>
                    <h2 class="text-warning mb-2">{{ number_format($rewardData['current_points']) }}</h2>
                    <p class="text-muted mb-0">Poin tersedia untuk digunakan</p>
                </div>
            </div>
            
            <!-- Points Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Poin
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Total Poin Diperoleh</span>
                            <span class="fw-bold text-success">+{{ number_format($rewardData['total_earned']) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Total Poin Digunakan</span>
                            <span class="fw-bold text-danger">-{{ number_format($rewardData['total_used']) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ $rewardData['total_earned'] > 0 ? ($rewardData['total_used'] / $rewardData['total_earned']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            Saldo Poin: {{ number_format($rewardData['current_points']) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Points History -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Riwayat Poin
                    </h5>
                </div>
                <div class="card-body">
                    @if($rewardData['point_history']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Transaksi</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center">Poin Diperoleh</th>
                                        <th class="text-center">Poin Digunakan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rewardData['point_history'] as $history)
                                        <tr>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($history->tanggal_pesan)->format('d M Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">#{{ str_pad($history->transaksi_id, 6, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>
                                                @if($history->point_diperoleh > 0)
                                                    <span class="text-success">
                                                        <i class="fas fa-plus-circle me-1"></i>
                                                        Poin dari pembelian
                                                    </span>
                                                @endif
                                                @if($history->point_digunakan > 0)
                                                    <span class="text-primary">
                                                        <i class="fas fa-minus-circle me-1"></i>
                                                        Poin digunakan untuk diskon
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($history->point_diperoleh > 0)
                                                    <span class="badge bg-success">+{{ number_format($history->point_diperoleh) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($history->point_digunakan > 0)
                                                    <span class="badge bg-primary">-{{ number_format($history->point_digunakan) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($history->status_transaksi == 'Selesai')
                                                    <span class="badge bg-success">{{ $history->status_transaksi }}</span>
                                                @elseif($history->status_transaksi == 'Menunggu Pembayaran')
                                                    <span class="badge bg-warning">{{ $history->status_transaksi }}</span>
                                                @elseif($history->status_transaksi == 'Diproses')
                                                    <span class="badge bg-info">{{ $history->status_transaksi }}</span>
                                                @elseif($history->status_transaksi == 'Dikirim')
                                                    <span class="badge bg-primary">{{ $history->status_transaksi }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $history->status_transaksi }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Riwayat Poin</h5>
                            <p class="text-muted">Mulai berbelanja untuk mendapatkan poin loyalitas!</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-1"></i>Mulai Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- How to Earn Points Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Cara Mendapatkan Poin
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-shopping-cart text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Berbelanja</h6>
                                    <small class="text-muted">Dapatkan 1 poin untuk setiap Rp 1.000 pembelian</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-gift text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Bonus Transaksi</h6>
                                    <small class="text-muted">Poin bonus untuk transaksi tertentu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-2">Cara Menggunakan Poin:</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>100 poin = Rp 1.000 diskon</small>
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Gunakan poin saat checkout untuk mendapatkan diskon</small>
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Poin tidak memiliki masa kadaluarsa</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
