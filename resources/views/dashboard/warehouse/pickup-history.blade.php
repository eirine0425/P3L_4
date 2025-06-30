@extends('layouts.dashboard')

@section('title', 'Riwayat Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Pengambilan Barang</h1>
        <div>
            <a href="{{ route('dashboard.warehouse.item-pickup') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-box"></i> Barang Perlu Diambil
            </a>
            <a href="#" class="btn btn-sm btn-success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Barang yang Sudah Diambil</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Filter:</div>
                    <a class="dropdown-item" href="{{ route('dashboard.warehouse.pickup-history') }}?period=today">Hari Ini</a>
                    <a class="dropdown-item" href="{{ route('dashboard.warehouse.pickup-history') }}?period=week">Minggu Ini</a>
                    <a class="dropdown-item" href="{{ route('dashboard.warehouse.pickup-history') }}?period=month">Bulan Ini</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('dashboard.warehouse.pickup-history') }}">Semua</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Penitip</th>
                            <th>Tanggal Pengambilan</th>
                            <th>Pengambil</th>
                            <th>Metode</th>
                            <th>Pegawai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pickedUpItems as $item)
                            <tr>
                                <td>{{ $item->barang_id }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->penitip->user->name ?? '-' }}</td>
                                <td>{{ $item->formatted_tanggal_pengambilan }}</td>
                                <td>{{ $item->nama_pengambil ?? '-' }}</td>
                                <td>
                                    @switch($item->metode_pengambilan)
                                        @case('diambil_langsung')
                                            <span class="badge badge-success">Diambil Langsung</span>
                                            @break
                                        @case('dikirim_kurir')
                                            <span class="badge badge-info">Dikirim via Kurir</span>
                                            @break
                                        @case('dititipkan_pihak_lain')
                                            <span class="badge badge-warning">Dititipkan ke Pihak Lain</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $item->metode_pengambilan }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $item->pegawaiPickup->user->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('dashboard.warehouse.pickup-detail', $item->barang_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.warehouse.pickup-receipt', $item->barang_id) }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pengambilan barang</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $pickedUpItems->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function exportToExcel() {
        window.location.href = "{{ route('dashboard.warehouse.generate-pickup-report') }}";
    }
    
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
        });
    });
</script>
@endsection
