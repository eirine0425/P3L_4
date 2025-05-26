@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('buyer.profile.index') }}">Profil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Transaksi</li>
                </ol>
            </nav>
            <h2>Riwayat Transaksi</h2>
            <p class="text-muted">Daftar lengkap semua transaksi pembelian Anda.</p>
        </div>
    </div>
    
    @include('partials.alerts')
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-shopping-bag fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-primary">{{ $stats['total_transactions'] }}</h4>
                            <small class="text-muted">Total Transaksi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-success">{{ $stats['completed_transactions'] }}</h4>
                            <small class="text-muted">Transaksi Selesai</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-clock fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-warning">{{ $stats['pending_transactions'] }}</h4>
                            <small class="text-muted">Transaksi Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-money-bill-wave fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-info">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</h6>
                            <small class="text-muted">Total Belanja</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Transaksi
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('buyer.profile.transaction-history') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="Menunggu Pembayaran" {{ request('status') == 'Menunggu Pembayaran' ? 'selected' : '' }}>
                                Menunggu Pembayaran
                            </option>
                            <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>
                            <option value="Dikirim" {{ request('status') == 'Dikirim' ? 'selected' : '' }}>
                                Dikirim
                            </option>
                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                            <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('buyer.profile.transaction-history') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Transactions List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Daftar Transaksi
            </h5>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Total Harga</th>
                                <th>Poin</th>
                                <th>Status</th>
                                <th>Metode Pengiriman</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="fw-bold">#{{ str_pad($transaction->transaksi_id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <div>{{ \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d M Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            @if($transaction->point_diperoleh > 0)
                                                <small class="text-success">
                                                    <i class="fas fa-plus me-1"></i>{{ $transaction->point_diperoleh }}
                                                </small>
                                            @endif
                                            @if($transaction->point_digunakan > 0)
                                                <small class="text-primary">
                                                    <i class="fas fa-minus me-1"></i>{{ $transaction->point_digunakan }}
                                                </small>
                                            @endif
                                            @if($transaction->point_diperoleh == 0 && $transaction->point_digunakan == 0)
                                                <small class="text-muted">-</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($transaction->status_transaksi == 'Menunggu Pembayaran')
                                            <span class="badge bg-warning">Menunggu Pembayaran</span>
                                        @elseif($transaction->status_transaksi == 'Diproses')
                                            <span class="badge bg-info">Diproses</span>
                                        @elseif($transaction->status_transaksi == 'Dikirim')
                                            <span class="badge bg-primary">Dikirim</span>
                                        @elseif($transaction->status_transaksi == 'Selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $transaction->metode_pengiriman ?? 'Belum ditentukan' }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('buyer.profile.transaction-detail', $transaction->transaksi_id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($transaction->status_transaksi == 'Menunggu Pembayaran')
                                                <button class="btn btn-sm btn-outline-success" title="Bayar Sekarang">
                                                    <i class="fas fa-credit-card"></i>
                                                </button>
                                            @endif
                                            @if($transaction->status_transaksi == 'Selesai')
                                                <button class="btn btn-sm btn-outline-info" title="Beli Lagi">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Transaksi</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'date_from', 'date_to']))
                            Tidak ada transaksi yang sesuai dengan filter yang dipilih.
                        @else
                            Anda belum memiliki riwayat transaksi.
                        @endif
                    </p>
                    @if(!request()->hasAny(['status', 'date_from', 'date_to']))
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-1"></i>Mulai Belanja
                        </a>
                    @else
                        <a href="{{ route('buyer.profile.transaction-history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Hapus Filter
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
