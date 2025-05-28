<!-- Modal untuk memilih alamat -->
<div class="modal fade" id="alamatSelectorModal" tabindex="-1" aria-labelledby="alamatSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alamatSelectorModalLabel">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    Pilih Alamat Pengiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alamatSelectorContent">
                <!-- Content will be loaded here -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat alamat...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variable to store callback function
let alamatSelectionCallback = null;

// Function to open alamat selector modal
function openAlamatSelector(callback) {
    alamatSelectionCallback = callback;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('alamatSelectorModal'));
    modal.show();
    
    // Load alamat data
    loadAlamatSelector();
}

// Function to load alamat selector content
function loadAlamatSelector() {
    fetch('/buyer/alamat/select', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('alamatSelectorContent').innerHTML = data.html;
            
            // Bind confirm button event
            const confirmBtn = document.getElementById('confirmAlamatSelection');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    const selectedRadio = document.querySelector('.alamat-radio:checked');
                    if (selectedRadio) {
                        const alamatItem = selectedRadio.closest('.alamat-item');
                        const alamatData = JSON.parse(alamatItem.getAttribute('data-alamat-data'));
                        
                        // Call callback function with selected alamat data
                        if (alamatSelectionCallback) {
                            alamatSelectionCallback(alamatData);
                        }
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('alamatSelectorModal'));
                        modal.hide();
                    } else {
                        alert('Silakan pilih alamat terlebih dahulu');
                    }
                });
            }
        } else {
            document.getElementById('alamatSelectorContent').innerHTML = 
                '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + 
                (data.error || 'Terjadi kesalahan saat memuat alamat') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('alamatSelectorContent').innerHTML = 
            '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat memuat alamat</div>';
    });
}

// Function to get alamat details by ID
function getAlamatDetails(alamatId, callback) {
    fetch(`/buyer/alamat/details/${alamatId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && callback) {
            callback(data.alamat);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
