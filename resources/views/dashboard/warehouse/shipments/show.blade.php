@extends('layouts.dashboard')

@section('title', 'Detail Transaksi Pengiriman')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Detail Transaksi #{{ $transaction->transaksi_id }}</h2>
                    <p class="text-muted">Informasi lengkap transaksi dan pengiriman</p>
                </div>
                <a href="{{ route('warehouse.shipments') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Info -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID Transaksi:</strong></td>
                            <td>#{{ $transaction->transaksi_id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pesan:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($transaction->tanggal_pesan)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Harga:</strong></td>
                            <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><span class="badge bg-success">{{ $transaction->status_transaksi }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Metode Pengiriman:</strong></td>
                            <td>{{ $transaction->metode_pengiriman }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Buyer Info -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pembeli</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $transaction->pembeli->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $transaction->pembeli->user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Telepon:</strong></td>
                            <td>{{ $transaction->pembeli->user->phone_number ?? '-' }}</td>
                        </tr>
                        @if($transaction->pembeli->alamat)
                        <tr>
                            <td><strong>Alamat:</strong></td>
                            <td>
                                {{ $transaction->pembeli->alamat->alamat_lengkap }}<br>
                                {{ $transaction->pembeli->alamat->kota }}, {{ $transaction->pembeli->alamat->provinsi }}<br>
                                {{ $transaction->pembeli->alamat->kode_pos }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Daftar Barang (Minimal 2 Foto per Item)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Foto Barang</th>
                            <th>Nama Barang</th>
                            <th>Penitip</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->detailTransaksi as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        @if($detail->barang->foto_barang)
                                            @php
                                                $photos = is_string($detail->barang->foto_barang) 
                                                    ? json_decode($detail->barang->foto_barang, true) 
                                                    : $detail->barang->foto_barang;
                                                $photos = is_array($photos) ? $photos : [$detail->barang->foto_barang];
                                            @endphp
                                            @foreach(array_slice($photos, 0, 3) as $photo)
                                                <img src="{{ Storage::url($photo) }}" 
                                                     alt="Foto {{ $detail->barang->nama_barang }}" 
                                                     class="img-thumbnail me-1 mb-1" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @endforeach
                                            @if(count($photos) > 3)
                                                <span class="badge bg-secondary align-self-center">+{{ count($photos) - 3 }}</span>
                                            @endif
                                        @else
                                            <img src="/placeholder.svg?height=50&width=50" 
                                                 alt="No Image" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px;">
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $detail->barang->nama_barang }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($detail->barang->deskripsi, 50) }}</small>
                                </td>
                                <td>
                                    @if($detail->barang->penitip && $detail->barang->penitip->user)
                                        {{ $detail->barang->penitip->user->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Shipping Info -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Informasi Pengiriman</h5>
            @if(!$transaction->pengiriman)
                <a href="{{ route('warehouse.shipments.create', $transaction->transaksi_id) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Jadwalkan Pengiriman
                </a>
            @endif
        </div>
        <div class="card-body">
            @if($transaction->pengiriman)
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge 
                                        @if($transaction->pengiriman->status_pengiriman === 'Menunggu Pengiriman') bg-warning
                                        @elseif($transaction->pengiriman->status_pengiriman === 'Dalam Perjalanan') bg-primary
                                        @elseif($transaction->pengiriman->status_pengiriman === 'Terkirim') bg-success
                                        @else bg-danger @endif">
                                        {{ $transaction->pengiriman->status_pengiriman }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Kurir:</strong></td>
                                <td>{{ $transaction->pengiriman->pengirim->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Kirim:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($transaction->pengiriman->tanggal_kirim)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Penerima:</strong></td>
                                <td>{{ $transaction->pengiriman->nama_penerima }}</td>
                            </tr>
                            @if($transaction->pengiriman->tanggal_terima)
                            <tr>
                                <td><strong>Tanggal Terima:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($transaction->pengiriman->tanggal_terima)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <!-- Update Status Form -->
                        <form action="{{ route('warehouse.shipments.update-status', $transaction->pengiriman->pengiriman_id) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="status_pengiriman" class="form-label">Update Status</label>
                                <select name="status_pengiriman" id="status_pengiriman" class="form-select">
                                    <option value="Menunggu Pengiriman" {{ $transaction->pengiriman->status_pengiriman === 'Menunggu Pengiriman' ? 'selected' : '' }}>Menunggu Pengiriman</option>
                                    <option value="Dalam Perjalanan" {{ $transaction->pengiriman->status_pengiriman === 'Dalam Perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                    <option value="Terkirim" {{ $transaction->pengiriman->status_pengiriman === 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                                    <option value="Dibatalkan" {{ $transaction->pengiriman->status_pengiriman === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </form>

                        <!-- Assign Courier Form -->
                        <form action="{{ route('warehouse.shipments.assign-courier', $transaction->pengiriman->pengiriman_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="pengirim_id" class="form-label">Ganti Kurir</label>
                                <select name="pengirim_id" id="pengirim_id" class="form-select">
                                    @foreach($couriers as $courier)
                                        <option value="{{ $courier->id }}" {{ $transaction->pengiriman->pengirim_id == $courier->id ? 'selected' : '' }}>
                                            {{ $courier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-user-edit"></i> Ganti Kurir
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h5>Pengiriman Belum Dijadwalkan</h5>
                    <p class="text-muted">Transaksi ini belum memiliki jadwal pengiriman.</p>
                    <a href="{{ route('warehouse.shipments.create', $transaction->transaksi_id) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Jadwalkan Pengiriman
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
