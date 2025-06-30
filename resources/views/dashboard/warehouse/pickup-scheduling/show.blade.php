@extends('layouts.dashboard')

@section('title', 'Detail Jadwal Pengambilan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Jadwal Pengambilan #{{ $schedule->id }}</h1>
            <p class="text-muted">Informasi lengkap jadwal pengambilan barang</p>
        </div>
        <div>
            <a href="{{ route('warehouse.pickup.scheduling') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if(in_array($schedule->status, ['scheduled', 'confirmed']))
                <button class="btn btn-warning" onclick="editSchedule()">
                    <i class="fas fa-edit"></i> Edit
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Schedule Information -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Jadwal</h5>
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
                    <span class="badge {{ $statusClass }} fs-6">
                        {{ $statusText }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Penitip:</td>
                                    <td>{{ $schedule->penitip->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>{{ $schedule->penitip->user->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal:</td>
                                    <td>{{ $schedule->scheduled_date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Waktu:</td>
                                    <td>{{ $schedule->scheduled_time }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Metode:</td>
                                    <td>{{ $schedule->pickup_method === 'self_pickup' ? 'Ambil Sendiri' : 'Kirim via Kurir' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kontak:</td>
                                    <td>{{ $schedule->contact_phone }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Barang:</td>
                                    <td>{{ $schedule->total_items }} item</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Dibuat:</td>
                                    <td>{{ $schedule->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($schedule->pickup_address)
                        <div class="mt-3">
                            <h6>Alamat Pengambilan:</h6>
                            <p class="text-muted">{{ $schedule->pickup_address }}</p>
                        </div>
                    @endif

                    @if($schedule->notes)
                        <div class="mt-3">
                            <h6>Catatan:</h6>
                            <p class="text-muted">{{ $schedule->notes }}</p>
                        </div>
                    @endif

                    @if($schedule->completed_at)
                        <div class="mt-3">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Pengambilan Selesai</strong><br>
                                Diselesaikan pada: {{ $schedule->completed_at->format('d/m/Y H:i') }}
                                @if($schedule->completedBy)
                                    oleh {{ $schedule->completedBy->name }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Barang</h5>
                </div>
                <div class="card-body">
                    @if($schedule->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedule->items as $item)
                                    <tr>
                                        <td>{{ $item->barang_id }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                @if($item->foto_barang)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-image"></i> Ada foto
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($item->kondisi) }}</span>
                                        </td>
                                        <td>
                                            @if($item->status === 'diambil_kembali')
                                                <span class="badge bg-success">Sudah Diambil</span>
                                            @elseif($item->status === 'menunggu_pengambilan')
                                                <span class="badge bg-warning">Menunggu Pengambilan</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada barang dalam jadwal ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    @if($schedule->status === 'scheduled')
                        <button class="btn btn-primary w-100 mb-2" onclick="confirmSchedule()">
                            <i class="fas fa-check"></i> Konfirmasi Jadwal
                        </button>
                    @endif
                    
                    @if(in_array($schedule->status, ['scheduled', 'confirmed']))
                        <button class="btn btn-success w-100 mb-2" onclick="markAsCompleted()">
                            <i class="fas fa-check-circle"></i> Tandai Selesai
                        </button>
                        <button class="btn btn-warning w-100 mb-2" onclick="editSchedule()">
                            <i class="fas fa-edit"></i> Edit Jadwal
                        </button>
                    @endif
                    
                    @if($schedule->status !== 'cancelled' && $schedule->status !== 'completed')
                        <button class="btn btn-danger w-100 mb-2" onclick="cancelSchedule()">
                            <i class="fas fa-times"></i> Batalkan Jadwal
                        </button>
                    @endif
                    
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="contactPenitip()">
                        <i class="fas fa-phone"></i> Hubungi Penitip
                    </button>
                    
                    <button class="btn btn-outline-secondary w-100" onclick="printSchedule()">
                        <i class="fas fa-print"></i> Cetak Jadwal
                    </button>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Timeline Aktivitas</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Jadwal Dibuat</h6>
                                <p class="timeline-text">{{ $schedule->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($schedule->status === 'confirmed')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Jadwal Dikonfirmasi</h6>
                                <p class="timeline-text">Status diperbarui</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($schedule->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Pengambilan Selesai</h6>
                                <p class="timeline-text">{{ $schedule->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($schedule->status === 'cancelled')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Jadwal Dibatalkan</h6>
                                <p class="timeline-text">Jadwal dibatalkan</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
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
            <form method="POST" action="{{ route('warehouse.pickup.schedule.update', $schedule->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengambilan <span class="text-danger">*</span></label>
                                <input type="date" name="scheduled_date" class="form-control" 
                                       value="{{ $schedule->scheduled_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Waktu Pengambilan <span class="text-danger">*</span></label>
                                <select name="scheduled_time" class="form-select" required>
                                    <option value="08:00" {{ $schedule->scheduled_time === '08:00' ? 'selected' : '' }}>08:00 - 09:00</option>
                                    <option value="09:00" {{ $schedule->scheduled_time === '09:00' ? 'selected' : '' }}>09:00 - 10:00</option>
                                    <option value="10:00" {{ $schedule->scheduled_time === '10:00' ? 'selected' : '' }}>10:00 - 11:00</option>
                                    <option value="11:00" {{ $schedule->scheduled_time === '11:00' ? 'selected' : '' }}>11:00 - 12:00</option>
                                    <option value="13:00" {{ $schedule->scheduled_time === '13:00' ? 'selected' : '' }}>13:00 - 14:00</option>
                                    <option value="14:00" {{ $schedule->scheduled_time === '14:00' ? 'selected' : '' }}>14:00 - 15:00</option>
                                    <option value="15:00" {{ $schedule->scheduled_time === '15:00' ? 'selected' : '' }}>15:00 - 16:00</option>
                                    <option value="16:00" {{ $schedule->scheduled_time === '16:00' ? 'selected' : '' }}>16:00 - 17:00</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Kontak <span class="text-danger">*</span></label>
                                <input type="text" name="contact_phone" class="form-control" 
                                       value="{{ $schedule->contact_phone }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="scheduled" {{ $schedule->status === 'scheduled' ? 'selected' : '' }}>Dijadwalkan</option>
                                    <option value="confirmed" {{ $schedule->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="in_progress" {{ $schedule->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $schedule->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Pengambilan</label>
                        <textarea name="pickup_address" class="form-control" rows="3">{{ $schedule->pickup_address }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $schedule->notes }}</textarea>
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

<!-- Complete Schedule Modal -->
<div class="modal fade" id="completeScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tandai Pengambilan Selesai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('warehouse.pickup.schedule.update', $schedule->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <input type="hidden" name="scheduled_date" value="{{ $schedule->scheduled_date->format('Y-m-d') }}">
                <input type="hidden" name="scheduled_time" value="{{ $schedule->scheduled_time }}">
                <input type="hidden" name="contact_phone" value="{{ $schedule->contact_phone }}">
                <input type="hidden" name="pickup_address" value="{{ $schedule->pickup_address }}">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Apakah Anda yakin pengambilan barang telah selesai dilakukan?
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Penyelesaian</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Tambahkan catatan tentang proses pengambilan...">{{ $schedule->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Ya, Tandai Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
function editSchedule() {
    $('#editScheduleModal').modal('show');
}

function confirmSchedule() {
    if (confirm('Apakah Anda yakin ingin mengkonfirmasi jadwal ini?')) {
        updateScheduleStatus('confirmed');
    }
}

function markAsCompleted() {
    $('#completeScheduleModal').modal('show');
}

function cancelSchedule() {
    if (confirm('Apakah Anda yakin ingin membatalkan jadwal ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("warehouse.pickup.schedule.cancel", $schedule->id) }}';
        
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

function updateScheduleStatus(status) {
    // Create form for status update
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("warehouse.pickup.schedule.update", $schedule->id) }}';
    
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
    methodOverride.value = 'PUT';
    form.appendChild(methodOverride);
    
    // Add form data
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);
    
    const dateInput = document.createElement('input');
    dateInput.type = 'hidden';
    dateInput.name = 'scheduled_date';
    dateInput.value = '{{ $schedule->scheduled_date->format("Y-m-d") }}';
    form.appendChild(dateInput);
    
    const timeInput = document.createElement('input');
    timeInput.type = 'hidden';
    timeInput.name = 'scheduled_time';
    timeInput.value = '{{ $schedule->scheduled_time }}';
    form.appendChild(timeInput);
    
    const phoneInput = document.createElement('input');
    phoneInput.type = 'hidden';
    phoneInput.name = 'contact_phone';
    phoneInput.value = '{{ $schedule->contact_phone }}';
    form.appendChild(phoneInput);
    
    const addressInput = document.createElement('input');
    addressInput.type = 'hidden';
    addressInput.name = 'pickup_address';
    addressInput.value = '{{ $schedule->pickup_address }}';
    form.appendChild(addressInput);
    
    const notesInput = document.createElement('input');
    notesInput.type = 'hidden';
    notesInput.name = 'notes';
    notesInput.value = '{{ $schedule->notes }}';
    form.appendChild(notesInput);
    
    // Submit form
    document.body.appendChild(form);
    form.submit();
}

function contactPenitip() {
    const phone = '{{ $schedule->contact_phone }}';
    if (phone) {
        window.open(`tel:${phone}`);
    } else {
        alert('Nomor telepon tidak tersedia.');
    }
}

function printSchedule() {
    window.print();
}
</script>
@endpush
