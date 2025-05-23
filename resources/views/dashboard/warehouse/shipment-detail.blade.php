@extends('layouts.dashboard')

@section('title', 'Shipment Detail')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2>Detail Pengiriman #{{ $shipment->id }}</h2>
                <p class="text-muted">Informasi lengkap tentang pengiriman.</p>
            </div>
            <div>
                <a href="{{ route('dashboard.warehouse.shipments') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fas fa-truck"></i> Update Status
                </button>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID Pengiriman</th>
                            <td>#{{ $shipment->id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($shipment->status == 'Menunggu Pengiriman')
                                    <span class="badge bg-warning">Menunggu Pengiriman</span>
                                @elseif($shipment->status == 'Sedang Dikirim')
                                    <span class="badge bg-info">Sedang Dikirim</span>
                                @elseif($shipment->status == 'Terkirim')
                                    <span class="badge bg-success">Terkirim</span>
                                @else
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Transaksi</th>
                            <td>{{ $shipment->transaksi->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengiriman</th>
                            <td>{{ $shipment->tanggal_pengiriman ? $shipment->tanggal_pengiriman->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diterima</th>
                            <td>{{ $shipment->tanggal_diterima ? $shipment->tanggal_diterima->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Metode Pengiriman</th>
                            <td>{{ $shipment->metode_pengiriman ?? 'Reguler' }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Resi</th>
                            <td>{{ $shipment->nomor_resi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pembeli</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nama</th>
                            <td>{{ $shipment->transaksi->pembeli->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $shipment->transaksi->pembeli->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td>{{ $shipment->transaksi->pembeli->no_telp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat Pengiriman</th>
                            <td>{{ $shipment->alamat_pengiriman }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Detail Barang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipment->transaksi->details as $detail)
                                    <tr>
                                        <td>
                                            @if($detail->barang->gambar)
                                                <img src="{{ asset('storage/' . $detail->barang->gambar) }}" alt="{{ $detail->barang->nama }}" width="50" height="50" class="img-thumbnail">
                                            @else
                                                <div class="no-image">No Image</div>
                                            @endif
                                        </td>
                                        <td>{{ $detail->barang->nama }}</td>
                                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Subtotal</th>
                                    <td>Rp {{ number_format($shipment->transaksi->details->sum('subtotal'), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Biaya Pengiriman</th>
                                    <td>Rp {{ number_format($shipment->transaksi->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Pajak</th>
                                    <td>Rp {{ number_format($shipment->transaksi->tax, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <td>Rp {{ number_format($shipment->transaksi->total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Status Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.warehouse.shipment.update-status', $shipment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Menunggu Pengiriman" {{ $shipment->status == 'Menunggu Pengiriman' ? 'selected' : '' }}>Menunggu Pengiriman</option>
                            <option value="Sedang Dikirim" {{ $shipment->status == 'Sedang Dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                            <option value="Terkirim" {{ $shipment->status == 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                            <option value="Dibatalkan" {{ $shipment->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_resi" class="form-label">Nomor Resi (opsional)</label>
                        <input type="text" name="nomor_resi" id="nomor_resi" class="form-control" value="{{ $shipment->nomor_resi }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
