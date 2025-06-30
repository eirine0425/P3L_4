@extends('layouts.dashboard')

@section('title', 'Transaksi Siap Diambil')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Transaksi Siap Diambil</h4>
                    <p class="card-category">Kelola transaksi yang siap diambil oleh pembeli</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Pembeli</th>
                                    <th>Tanggal Pesan</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Detail Barang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksis as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->transaksi_id }}</td>
                                    <td>
                                        {{ $transaksi->pembeli->user->name ?? 'Pembeli #' . $transaksi->pembeli_id }}
                                        <br>
                                        <small class="text-muted">{{ $transaksi->pembeli->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_pesan)->format('d M Y H:i') }}</td>
                                    <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $transaksi->status_transaksi }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#detailModal{{ $transaksi->transaksi_id }}">
                                            Lihat Detail
                                        </button>
                                        
                                        <!-- Modal Detail -->
                                        <div class="modal fade" id="detailModal{{ $transaksi->transaksi_id }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $transaksi->transaksi_id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="detailModalLabel{{ $transaksi->transaksi_id }}">Detail Transaksi #{{ $transaksi->transaksi_id }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6>Informasi Pembeli:</h6>
                                                        <p>
                                                            <strong>Nama:</strong> {{ $transaksi->pembeli->user->name ?? 'Pembeli #' . $transaksi->pembeli_id }}<br>
                                                            <strong>Email:</strong> {{ $transaksi->pembeli->user->email ?? 'Tidak tersedia' }}<br>
                                                            <strong>Telepon:</strong> {{ $transaksi->pembeli->no_telepon ?? 'Tidak tersedia' }}
                                                        </p>
                                                        
                                                        <h6 class="mt-4">Daftar Barang:</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Nama Barang</th>
                                                                        <th>Harga</th>
                                                                        <th>Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($transaksi->detailTransaksi as $detail)
                                                                    <tr>
                                                                        <td>{{ $detail->barang->nama_barang }}</td>
                                                                        <td>Rp {{ number_format($detail->barang->harga, 0, ',', '.') }}</td>
                                                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th colspan="2" class="text-right">Total:</th>
                                                                        <th>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('warehouse.confirm.pickup', $transaksi->transaksi_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengonfirmasi pengambilan barang ini?')">
                                                Konfirmasi Pengambilan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada transaksi yang siap diambil</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transaksis->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Tambahkan script tambahan jika diperlukan
    });
</script>
@endsection
