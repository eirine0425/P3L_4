@extends('layouts.dashboard')

@section('title', 'Buyer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Dashboard Pembeli</h2>
            <p class="text-muted">Selamat datang di panel kontrol Pembeli ReuseMart.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-shopping-bag"></i>
                <h3>{{ $totalTransactions ?? 0 }}</h3>
                <p>Total Transaksi</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-clock"></i>
                <h3>{{ $pendingOrders ?? 0 }}</h3>
                <p>Pesanan Tertunda</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-check-circle"></i>
                <h3>{{ $completedOrders ?? 0 }}</h3>
                <p>Pesanan Selesai</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-star"></i>
                <h3>{{ $loyaltyPoints ?? 0 }}</h3>
                <p>Poin Loyalitas</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if($order->status == 'Menunggu Pembayaran')
                                                <span class="badge bg-warning">Menunggu Pembayaran</span>
                                            @elseif($order->status == 'Diproses')
                                                <span class="badge bg-info">Diproses</span>
                                            @elseif($order->status == 'Dikirim')
                                                <span class="badge bg-primary">Dikirim</span>
                                            @elseif($order->status == 'Selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            @if($order->status == 'Menunggu Pembayaran')
                                                <a href="#" class="btn btn-sm btn-success"><i class="fas fa-credit-card"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada pesanan terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('buyer.transactions') }}" class="btn btn-primary">Lihat Semua Pesanan</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Keranjang Belanja</h5>
                </div>
                <div class="card-body">
                    @if(count($cartItems ?? []) > 0)
                        <ul class="list-group">
                            @foreach($cartItems as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->barang->nama_barang }}
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $item->jumlah }}x</span>
                                        <span class="text-muted">Rp {{ number_format($item->barang->harga * $item->jumlah, 0, ',', '.') }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-primary w-100">Lihat Keranjang</a>
                        </div>
                    @else
                        <p class="text-center text-muted">Keranjang belanja Anda kosong.</p>
                        <div class="mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-primary w-100">Belanja Sekarang</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Nama</th>
                            <td>{{ $buyer->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $buyer->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $buyer->no_telepon ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Poin Loyalitas</th>
                            <td>{{ $buyer->poin_loyalitas ?? 0 }} poin</td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('profile.show') }}" class="btn btn-primary w-100">Edit Profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
