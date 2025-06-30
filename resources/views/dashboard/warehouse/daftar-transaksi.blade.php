@extends('layouts.dashboard')

@section('title', 'Daftar Transaksi - Gudang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Transaksi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis Pengiriman</th>
                                    <th>Status</th>
                                    <th>Detail</th>
                                    <th>Jadwalkan</th>
                                    <th>Cetak PDF</th>
                                    <th>Konfirmasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksis as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->id }}</td>
                                    <td>
                                        @if($transaksi->detailTransaksi->isNotEmpty())
                                            @foreach($transaksi->detailTransaksi as $detail)
                                                {{ $detail->barang->nama_barang ?? 'N/A' }}
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaksi->pengiriman)
                                            {{ $transaksi->pengiriman->jenis_pengiriman ?? 'Pengambilan' }}
                                        @else
                                            Pengambilan
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($transaksi->status_transaksi == 'selesai') badge-success
                                            @elseif($transaksi->status_transaksi == 'dikirim') badge-info
                                            @elseif($transaksi->status_transaksi == 'dikemas') badge-warning
                                            @elseif($transaksi->status_transaksi == 'siap_diambil') badge-primary
                                            @else badge-secondary
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $transaksi->status_transaksi)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('warehouse.shipments.show', $transaksi->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            Lihat
                                        </a>
                                    </td>
                                    <td>
                                        @if($transaksi->status_transaksi == 'dikemas')
                                            @if($transaksi->pengiriman)
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="scheduleDelivery({{ $transaksi->id }})">
                                                    Jadwal Pengiriman
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="markReadyForPickup({{ $transaksi->id }})">
                                                    Siap Diambil
                                                </button>
                                            @endif
                                        @elseif($transaksi->status_transaksi == 'siap_diambil')
                                            <span class="text-success">✓ Siap Diambil</span>
                                        @elseif($transaksi->status_transaksi == 'dikirim')
                                            <span class="text-info">✓ Sedang Dikirim</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('warehouse.print.nota', $transaksi->id) }}" 
                                           class="btn btn-sm btn-danger" target="_blank">
                                            Cetak PDF
                                        </a>
                                    </td>
                                    <td>
                                        @if($transaksi->status_transaksi == 'siap_diambil')
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="confirmPickup({{ $transaksi->id }})"
                                                    title="Konfirmasi barang telah diambil pembeli">
                                                <i class="fas fa-check"></i> Konfirmasi Pengambilan
                                            </button>
                                        @elseif($transaksi->status_transaksi == 'dikirim')
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="confirmDelivery({{ $transaksi->id }})"
                                                    title="Konfirmasi barang telah diterima pembeli">
                                                <i class="fas fa-check"></i> Konfirmasi Penerimaan
                                            </button>
                                        @elseif($transaksi->status_transaksi == 'selesai')
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($transaksis->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $transaksis->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

&lt;!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmButton">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function confirmPickup(transaksiId) {
    $('#confirmMessage').text('Apakah Anda yakin barang telah diambil oleh pembeli?');
    $('#confirmButton').off('click').on('click', function() {
        $.ajax({
            url: `/dashboard/warehouse/confirm-pickup/${transaksiId}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    alert('Pengambilan berhasil dikonfirmasi!');
                    location.reload();
                } else {
                    alert('Gagal mengkonfirmasi pengambilan: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
        $('#confirmModal').modal('hide');
    });
    $('#confirmModal').modal('show');
}

function confirmDelivery(transaksiId) {
    $('#confirmMessage').text('Apakah Anda yakin barang telah diterima oleh pembeli?');
    $('#confirmButton').off('click').on('click', function() {
        $.ajax({
            url: `/dashboard/warehouse/confirm-delivery/${transaksiId}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    alert('Penerimaan berhasil dikonfirmasi!');
                    location.reload();
                } else {
                    alert('Gagal mengkonfirmasi penerimaan: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
        $('#confirmModal').modal('hide');
    });
    $('#confirmModal').modal('show');
}

function scheduleDelivery(transaksiId) {
    window.location.href = `/dashboard/warehouse/shipments/${transaksiId}`;
}

function markReadyForPickup(transaksiId) {
    if(confirm('Tandai transaksi ini siap untuk diambil?')) {
        $.ajax({
            url: `/dashboard/warehouse/mark-ready/${transaksiId}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    alert('Transaksi berhasil ditandai siap diambil!');
                    location.reload();
                } else {
                    alert('Gagal menandai siap diambil: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    }
}
</script>
@endsection
