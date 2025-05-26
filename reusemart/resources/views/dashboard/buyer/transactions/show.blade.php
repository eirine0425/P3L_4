@extends('layouts.dashboard')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Transaksi #{{ $transaction->id }}</h2>
                    <p class="text-muted">Tanggal: {{ $transaction->created_at->format('d M Y H:i') }}</p>
                </div>
                <a href="{{ route('buyer.transactions') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Item Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaction->detailTransaksi ?? [] as $detail)
                                    <tr>
                                        <td>{{ $detail->barang->nama_barang ?? 'N/A' }}</td>
                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada detail transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="3">Total</th>
                                    <th>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>Tanggal Transaksi</th>
                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                    
                    @if($transaction->status == 'Menunggu Pembayaran')
                        <div class="mt-3">
                            <a href="#" class="btn btn-success w-100">
                                <i class="fas fa-credit-card"></i> Bayar Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($transaction->pengiriman)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $transaction->pengiriman->alamat_tujuan ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status Pengiriman</th>
                                <td>{{ $transaction->pengiriman->status ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Kirim</th>
                                <td>{{ $transaction->pengiriman->tanggal_kirim ? $transaction->pengiriman->tanggal_kirim->format('d M Y') : 'Belum dikirim' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
