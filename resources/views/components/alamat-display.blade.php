<!-- Component untuk menampilkan alamat yang dipilih -->
<div class="alamat-display" id="alamatDisplay">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="card-title mb-2">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Alamat Pengiriman
                    </h6>
                    <div id="selectedAlamatInfo">
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada alamat yang dipilih
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="openAlamatSelector(handleAlamatSelection)">
                    <i class="fas fa-edit me-1"></i>Pilih Alamat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden input untuk menyimpan alamat_id yang dipilih -->
<input type="hidden" id="selectedAlamatId" name="alamat_id" value="">

<script>
// Function to handle alamat selection
function handleAlamatSelection(alamatData) {
    // Update hidden input
    document.getElementById('selectedAlamatId').value = alamatData.alamat_id;
    
    // Update display
    const alamatInfo = document.getElementById('selectedAlamatInfo');
    alamatInfo.innerHTML = `
        <div class="selected-alamat-info">
            <div class="d-flex align-items-center mb-2">
                <strong class="me-2">${alamatData.nama_penerima}</strong>
                ${alamatData.status_default == 'Y' ? '<span class="badge bg-primary"><i class="fas fa-star me-1"></i>Utama</span>' : ''}
            </div>
            <p class="text-muted mb-1">
                <i class="fas fa-phone text-success me-1"></i>
                ${alamatData.no_telepon}
            </p>
            <p class="mb-1">${alamatData.alamat}</p>
            <p class="text-muted mb-0">${alamatData.kota}, ${alamatData.provinsi} ${alamatData.kode_pos}</p>
        </div>
    `;
    
    // Trigger custom event for other components to listen
    const event = new CustomEvent('alamatSelected', {
        detail: alamatData
    });
    document.dispatchEvent(event);
}

// Auto-load default alamat on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load default alamat
    fetch('/buyer/alamat/default', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.alamat) {
            handleAlamatSelection(data.alamat);
        }
    })
    .catch(error => {
        console.log('No default alamat found or error occurred');
    });
});
</script>
