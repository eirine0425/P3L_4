<div class="alamat-selector">
    @if($alamats && $alamats->count() > 0)
        @foreach($alamats as $alamat)
            <div class="alamat-option border rounded p-3 mb-3" 
                 data-alamat-id="{{ $alamat->alamat_id }}"
                 data-alamat="{{ json_encode($alamat) }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">{{ $alamat->nama_penerima }}</h6>
                            @if($alamat->status_default === 'Y')
                                <span class="badge bg-success ms-2">Alamat Utama</span>
                            @endif
                        </div>
                        <p class="mb-1 text-muted">
                            <i class="fas fa-phone me-1"></i>{{ $alamat->no_telepon }}
                        </p>
                        <p class="mb-1">{{ $alamat->alamat }}</p>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $alamat->kota }}, {{ $alamat->provinsi }} {{ $alamat->kode_pos }}
                        </p>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm select-alamat-btn">
                            <i class="fas fa-check me-1"></i>Pilih
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-4">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Alamat</h5>
            <p class="text-muted mb-3">Anda belum memiliki alamat pengiriman</p>
            <a href="{{ route('buyer.alamat.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Alamat Pertama
            </a>
        </div>
    @endif
</div>

<script>
$(document).ready(function() {
    // Handle alamat selection
    $('.alamat-option').on('click', function() {
        const alamatId = $(this).data('alamat-id');
        const alamatData = $(this).data('alamat');
        
        // Remove previous selection
        $('.alamat-option').removeClass('selected');
        $(this).addClass('selected');
        
        // Trigger selection event
        if (typeof selectAlamat === 'function') {
            selectAlamat(alamatId, alamatData);
        }
    });
    
    // Handle select button click
    $('.select-alamat-btn').on('click', function(e) {
        e.stopPropagation();
        $(this).closest('.alamat-option').trigger('click');
    });
});
</script>

<style>
.alamat-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6 !important;
}

.alamat-option:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.alamat-option.selected {
    background-color: #e3f2fd;
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.select-alamat-btn {
    transition: all 0.3s ease;
}

.alamat-option:hover .select-alamat-btn {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>