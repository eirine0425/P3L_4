@extends('layouts.dashboard')

@section('title', 'Detail Penjemputan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Penjemputan #{{ $penjemputan->transaksi_penitipan_id }}</h1>
        <a href="{{ route('dashboard.hunter.riwayat-penjemputan') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Alert Messages -->
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

    <div class="row">
        <!-- Status Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Penjemputan</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($penjemputan->status == 'Menunggu Penjemputan')
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <h5 class="mt-3 font-weight-bold text-warning">Menunggu Penjemputan</h5>
                        @elseif($penjemputan->status == 'Dalam Proses')
                            <div class="icon-circle bg-info">
                                <i class="fas fa-spinner text-white"></i>
                            </div>
                            <h5 class="mt-3 font-weight-bold text-info">Dalam Proses</h5>
                        @elseif($penjemputan->status == 'Selesai')
                            <div class="icon-circle bg-success">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <h5 class="mt-3 font-weight-bold text-success">Selesai</h5>
                        @elseif($penjemputan->status == 'Dibatalkan')
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-times-circle text-white"></i>
                            </div>
                            <h5 class="mt-3 font-weight-bold text-danger">Dibatalkan</h5>
                        @endif
                    </div>

                    @if($penjemputan->status != 'Selesai' && $penjemputan->status != 'Dibatalkan')
                    <form action="{{ route('dashboard.hunter.update-status-penjemputan', $penjemputan->transaksi_penitipan_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Menunggu Penjemputan" {{ $penjemputan->status == 'Menunggu Penjemputan' ? 'selected' : '' }}>Menunggu Penjemputan</option>
                                <option value="Dalam Proses" {{ $penjemputan->status == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                    </form>
                    @endif

                    <div class="timeline mt-4">
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">Dibuat</div>
                                <div class="timeline-item-marker-indicator bg-primary"></div>
                            </div>
                            <div class="timeline-item-content">
                                {{ $penjemputan->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">Dijadwalkan</div>
                                <div class="timeline-item-marker-indicator {{ $penjemputan->tanggal_penjemputan ? 'bg-primary' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="timeline-item-content">
                                {{ $penjemputan->tanggal_penjemputan ? \Carbon\Carbon::parse($penjemputan->tanggal_penjemputan)->format('d M Y, H:i') : 'Belum dijadwalkan' }}
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">Selesai</div>
                                <div class="timeline-item-marker-indicator {{ $penjemputan->tanggal_selesai ? 'bg-success' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="timeline-item-content">
                                {{ $penjemputan->tanggal_selesai ? \Carbon\Carbon::parse($penjemputan->tanggal_selesai)->format('d M Y, H:i') : 'Belum selesai' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Penjemputan -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penjemputan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Informasi Barang</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td>Nama Barang</td>
                                    <td>: {{ $penjemputan->barang->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <td>Kategori</td>
                                    <td>: {{ $penjemputan->barang->kategori->nama_kategori ?? 'Tidak ada kategori' }}</td>
                                </tr>
                                <tr>
                                    <td>Kondisi</td>
                                    <td>: {{ $penjemputan->barang->kondisi }}</td>
                                </tr>
                                <tr>
                                    <td>Harga</td>
                                    <td>: Rp {{ number_format($penjemputan->barang->harga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Informasi Penitip</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td>Nama</td>
                                    <td>: {{ $penjemputan->penitip->user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>: {{ $penjemputan->penitip->user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Telepon</td>
                                    <td>: {{ $penjemputan->penitip->user->phone_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="font-weight-bold">Alamat Penjemputan</h5>
                            <p>{{ $penjemputan->alamat_penjemputan }}</p>
                            
                            <div class="embed-responsive embed-responsive-16by9 mt-3">
                                <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0537222574994!2d106.82862231476932!3d-6.259291595467284!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f22d2a8d3b6b%3A0x2a5fc2f2a2d3b6b5!2sJakarta%2C%20Indonesia!5e0!3m2!1sen!2sid!4v1621500000000!5m2!1sen!2sid" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="font-weight-bold">Catatan</h5>
                            <p>{{ $penjemputan->catatan ?? 'Tidak ada catatan' }}</p>
                        </div>
                    </div>

                    @if($penjemputan->status == 'Selesai')
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="font-weight-bold">Komisi</h5>
                            <div class="alert alert-success">
                                <p class="mb-0">Komisi yang Anda dapatkan dari penjemputan ini: <strong>Rp {{ number_format($penjemputan->barang->komisi->nominal_komisi ?? 0, 0, ',', '.') }}</strong></p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 4rem;
    width: 4rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.icon-circle i {
    font-size: 2rem;
}

.timeline {
    position: relative;
    padding-left: 1rem;
    margin-left: 1rem;
    border-left: 1px solid #dee2e6;
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-item-marker {
    position: absolute;
    left: -1.5rem;
    width: 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.timeline-item-marker-text {
    font-size: 0.75rem;
    color: #a2acba;
    margin-bottom: 0.25rem;
}

.timeline-item-marker-indicator {
    height: 0.75rem;
    width: 0.75rem;
    border-radius: 100%;
}

.timeline-item-content {
    padding-left: 1.5rem;
    padding-bottom: 1rem;
}
</style>
@endsection
