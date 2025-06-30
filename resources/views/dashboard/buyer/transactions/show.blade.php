@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Transaksi #{{ $transaksi->id }}</h1>
        <div>
            @if($transaksi->status === 'selesai')
                <a href="#" class="btn btn-sm btn-success">
                    <i class="fas fa-download fa-sm text-white-50"></i> Download Invoice
                </a>
            @endif
            <a href="{{ route('buyer.transactions.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Transaksi</h6>
                    <div>
                        @switch($transaksi->status)
                            @case('menunggu_pembayaran')
                                <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                @break
                            @case('dikemas')
                                <span class="badge bg-info">Dikemas</span>
                                @break
                            @case('dikirim')
                                <span class="badge bg-primary">Dikirim</span>
                                @break
                            @case('selesai')
                                <span class="badge bg-success">Selesai</span>
                                @break
                            @case('batal')
                                <span class="badge bg-danger">Dibatalkan</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ $transaksi->status }}</span>
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Tanggal Transaksi</th>
                                    <td>{{ $transaksi->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Metode Pembayaran</th>
                                    <td>{{ $transaksi->metode_pembayaran }}</td>
                                </tr>
                                <tr>
                                    <th>Total Harga</th>
                                    <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Ongkir</th>
                                    <td>Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Pembayaran</th>
                                    <td><strong>Rp {{ number_format($transaksi->total_harga + $transaksi->ongkir, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Status Transaksi</h6>
                            <div class="timeline-steps">
                                <div class="timeline-step {{ in_array($transaksi->status, ['menunggu_pembayaran', 'dikemas', 'dikirim', 'selesai']) ? 'active' : '' }}">
                                    <div class="timeline-content">
                                        <div class="inner-circle"></div>
                                        <p class="h6 mt-3 mb-1">Menunggu Pembayaran</p>
                                        @if($transaksi->status === 'menunggu_pembayaran')
                                            <p class="h6 text-muted mb-0 mb-lg-0">
                                                <a href="{{ route('checkout.payment', $transaksi->id) }}" class="btn btn-sm btn-warning">
                                                    Bayar Sekarang
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="timeline-step {{ in_array($transaksi->status, ['dikemas', 'dikirim', 'selesai']) ? 'active' : '' }}">
                                    <div class="timeline-content">
                                        <div class="inner-circle"></div>
                                        <p class="h6 mt-3 mb-1">Dikemas</p>
                                        @if($transaksi->status === 'dikemas')
                                            <p class="h6 text-muted mb-0 mb-lg-0">Sedang diproses</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="timeline-step {{ in_array($transaksi->status, ['dikirim', 'selesai']) ? 'active' : '' }}">
                                    <div class="timeline-content">
                                        <div class="inner-circle"></div>
                                        <p class="h6 mt-3 mb-1">Dikirim</p>
                                        @if($transaksi->status === 'dikirim' && $transaksi->pengiriman)
                                            <p class="h6 text-muted mb-0 mb-lg-0">
                                                No. Resi: {{ $transaksi->pengiriman->no_resi }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="timeline-step {{ $transaksi->status === 'selesai' ? 'active' : '' }}">
                                    <div class="timeline-content">
                                        <div class="inner-circle"></div>
                                        <p class="h6 mt-3 mb-1">Selesai</p>
                                        @if($transaksi->status === 'dikirim')
                                            <p class="h6 text-muted mb-0 mb-lg-0">
                                                <button class="btn btn-sm btn-success">
                                                    Konfirmasi Terima
                                                </button>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="font-weight-bold">Detail Barang</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->detailTransaksi as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detail->barang->foto_barang->count() > 0)
                                                <img src="{{ asset('storage/' . $detail->barang->foto_barang->first()->path) }}" 
                                                     alt="{{ $detail->barang->nama }}" 
                                                     class="img-thumbnail mr-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary mr-3" style="width: 50px; height: 50px;"></div>
                                            @endif
                                            <div>
                                                <p class="mb-0 font-weight-bold">{{ $detail->barang->nama }}</p>
                                                <small class="text-muted">{{ $detail->barang->kategori->nama ?? 'Tanpa Kategori' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total Harga</th>
                                    <th>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Ongkir</th>
                                    <th>Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Total Pembayaran</th>
                                    <th>Rp {{ number_format($transaksi->total_harga + $transaksi->ongkir, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alamat Pengiriman</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $transaksi->alamat->nama_penerima }}</h6>
                    <p>{{ $transaksi->alamat->no_telp }}</p>
                    <p>{{ $transaksi->alamat->alamat_lengkap }}, {{ $transaksi->alamat->kota }}, {{ $transaksi->alamat->provinsi }}, {{ $transaksi->alamat->kode_pos }}</p>
                </div>
            </div>

            @if($transaksi->bukti_pembayaran)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $transaksi->bukti_pembayaran) }}" 
                         alt="Bukti Pembayaran" 
                         class="img-fluid mb-3" 
                         style="max-height: 300px;">
                    <p class="mb-0 text-muted">Diunggah pada {{ Carbon\Carbon::parse($transaksi->updated_at)->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif

            @if($transaksi->status === 'menunggu_pembayaran')
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold">Menunggu Pembayaran</h6>
                </div>
                <div class="card-body">
                    <p>Silakan selesaikan pembayaran Anda sebelum batas waktu berakhir.</p>
                    <a href="{{ route('checkout.payment', $transaksi->id) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                    </a>
                    <a href="{{ route('checkout.cancel', $transaksi->id) }}" class="btn btn-outline-danger btn-block mt-2" 
                       onclick="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')">
                        <i class="fas fa-times"></i> Batalkan Transaksi
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }
    
    .timeline-steps:before {
        content: '';
        position: absolute;
        background: #e5e5e5;
        height: 3px;
        width: 100%;
        top: 20px;
        transform: translateY(-50%);
        z-index: 0;
    }
    
    .timeline-step {
        z-index: 1;
        flex: 1;
        text-align: center;
        position: relative;
    }
    
    .timeline-step .inner-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e5e5e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .timeline-step.active .inner-circle {
        background-color: #4e73df;
    }
    
    .timeline-step.active ~ .timeline-step .inner-circle {
        background-color: #e5e5e5;
    }
</style>
@endsection
