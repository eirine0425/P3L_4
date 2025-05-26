@extends('layouts.dashboard')

@section('title', 'Transaksi Saya')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Transaksi Saya</h2>
            <p class="text-muted">Daftar semua transaksi pembelian Anda.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions ?? [] as $transaction)
                                    <tr>
                                        <td>#{{ $transaction->id }}</td>
                                        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                        <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if($transaction->status == 'Menunggu Pembayaran')
                                                <span class="badge bg-warning">Menunggu Pembayaran</span>
                                            @elseif($transaction->status == 'Diproses')
                                                <span class="badge bg-info">Diproses</span>
                                            @elseif($transaction->status == 'Dikirim')
                                                <span class="badge bg-primary">Dikirim</span>
                                            @elseif($transaction->status == 'Selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('buyer.transactions.show', $transaction->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            @if($transaction->status == 'Menunggu Pembayaran')
                                                <a href="#" class="btn btn-sm btn-success">
                                                    <i class="fas fa-credit-card"></i> Bayar
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($transactions) && $transactions->hasPages())
                    <div class="card-footer">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
