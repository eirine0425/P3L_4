@extends('layouts.dashboard')

@section('title', 'Daftar Transaksi Pengiriman')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Daftar Transaksi yang Harus Dikirim / Diambil</h2>
            <p class="text-muted">Kelola pengiriman dan penugasan kurir untuk transaksi yang sudah lunas.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.shipments') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="need_shipping" {{ request('status') === 'need_shipping' ? 'selected' : '' }}>Perlu Pengiriman</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari Pembeli</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Nama atau email pembeli" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('warehouse.shipments') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Transaksi ({{ $transactions->total() }} transaksi)</h5>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Pembeli</th>
                                <th>Tanggal Pesan</th>
                                <th>Total Harga</th>
                                <th>Jumlah Item</th>
                                <th>Status Pengiriman</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <strong>#{{ $transaction->transaksi_id }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $transaction->pembeli->user->name }}</strong><br>
                                            <small class="text-muted">{{ $transaction->pembeli->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d/m/Y H:i') }}</td>
                                    <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $transaction->detailTransaksi->count() }} item</span>
                                    </td>
                                    <td>
                                        @if($transaction->pengiriman)
                                            @if($transaction->pengiriman->status_pengiriman === 'Menunggu Pengiriman')
                                                <span class="badge bg-warning">Menunggu Pengiriman</span>
                                            @elseif($transaction->pengiriman->status_pengiriman === 'Dalam Perjalanan')
                                                <span class="badge bg-primary">Dalam Perjalanan</span>
                                            @elseif($transaction->pengiriman->status_pengiriman === 'Terkirim')
                                                <span class="badge bg-success">Terkirim</span>
                                            @else
                                                <span class="badge bg-danger">{{ $transaction->pengiriman->status_pengiriman }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Belum Dijadwalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('warehouse.shipments.show', $transaction->transaksi_id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        @if(!$transaction->pengiriman)
                                            <a href="{{ route('warehouse.shipments.create', $transaction->transaksi_id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-truck"></i> Jadwalkan
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>Tidak ada transaksi ditemukan</h5>
                    <p class="text-muted">Belum ada transaksi yang perlu diproses pengirimannya.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

.stats-card i {
    font-size: 2rem;
    margin-bottom: 10px;
}

.stats-card h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 10px 0;
}

.stats-card p {
    margin: 0;
    opacity: 0.9;
}
</style>
@endpush
