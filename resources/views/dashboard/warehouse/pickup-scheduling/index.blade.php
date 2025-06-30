@extends('layouts.dashboard')

@section('title', 'Penjadwalan Pengambilan Barang')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Penjadwalan Pengambilan Barang</h1>
            <p class="text-muted">Kelola jadwal pengambilan barang oleh penitip</p>
        </div>
        <a href="{{ route('warehouse.pickup.schedule.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Jadwal Baru
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Jadwal</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Menunggu</h6>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Terkonfirmasi</h6>
                            <h3 class="mb-0">{{ $stats['confirmed'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Hari Ini</h6>
                            <h3 class="mb-0">{{ $stats['today'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.pickup.scheduling') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Dijadwalkan</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Dari</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cari Penitip</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama atau email penitip..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('warehouse.pickup.scheduling') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedules Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Jadwal Pengambilan</h5>
        </div>
        <div class="card-body">
            @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Penitip</th>
                                <th>Tanggal & Waktu</th>
                                <th>Metode</th>
                                <th>Jumlah Barang</th>
                                <th>Kontak</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                            <tr>
                                <td>
                                    <strong>#{{ $schedule->id }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $schedule->penitip_name }}</strong>
                                        <br><small class="text-muted">{{ $schedule->penitip_email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ date('d/m/Y', strtotime($schedule->scheduled_date)) }}</strong>
                                        <br><small class="text-muted">{{ $schedule->scheduled_time }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $schedule->pickup_method === 'self_pickup' ? 'Ambil Sendiri' : 'Kirim via Kurir' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $schedule->total_items }} item
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-phone"></i> {{ $schedule->contact_phone }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($schedule->status) {
                                            'confirmed' => 'bg-primary',
                                            'in_progress' => 'bg-warning',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        $statusText = match($schedule->status) {
                                            'confirmed' => 'Terkonfirmasi',
                                            'in_progress' => 'Sedang Diproses',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            default => 'Dijadwalkan'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('warehouse.pickup.schedule.show', $schedule->id) }}" 
                                           class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(in_array($schedule->status, ['scheduled', 'confirmed']))
                                            <button class="btn btn-outline-warning" 
                                                    onclick="editSchedule({{ $schedule->id }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if($schedule->status !== 'completed' && $schedule->status !== 'cancelled')
                                            <button class="btn btn-outline-danger" 
                                                    onclick="cancelSchedule({{ $schedule->id }})" title="Batalkan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $schedules->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada jadwal pengambilan</h5>
                    <p class="text-muted">Belum ada jadwal pengambilan yang dibuat.</p>
                    <a href="{{ route('warehouse.pickup.schedule.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Jadwal Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal Pengambilan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengambilan <span class="text-danger">*</span></label>
                                <input type="date" name="scheduled_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Waktu Pengambilan <span class="text-danger">*</span></label>
                                <select name="scheduled_time" class="form-select" required>
                                    <option value="08:00">08:00 - 09:00</option>
                                    <option value="09:00">09:00 - 10:00</option>
                                    <option value="10:00">10:00 - 11:00</option>
                                    <option value="11:00">11:00 - 12:00</option>
                                    <option value="13:00">13:00 - 14:00</option>
                                    <option value="14:00">14:00 - 15:00</option>
                                    <option value="15:00">15:00 - 16:00</option>
                                    <option value="16:00">16:00 - 17:00</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Kontak <span class="text-danger">*</span></label>
                                <input type="text" name="contact_phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="scheduled">Dijadwalkan</option>
                                    <option value="confirmed">Dikonfirmasi</option>
                                    <option value="in_progress">Sedang Diproses</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Pengambilan</label>
                        <textarea name="pickup_address" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editSchedule(scheduleId) {
    // Set form action
    $('#editScheduleForm').attr('action', `{{ route('warehouse.pickup.scheduling') }}/${scheduleId}`);
    
    // Show modal
    $('#editScheduleModal').modal('show');
}

function cancelSchedule(scheduleId) {
    if (confirm('Apakah Anda yakin ingin membatalkan jadwal ini?')) {
        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('warehouse.pickup.scheduling') }}/${scheduleId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override
        const methodOverride = document.createElement('input');
        methodOverride.type = 'hidden';
        methodOverride.name = '_method';
        methodOverride.value = 'DELETE';
        form.appendChild(methodOverride);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
