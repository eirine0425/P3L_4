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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAlamatSelection">Pilih Alamat</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global variable to store callback function
    window.alamatSelectionCallback = null;
    window.selectedAlamatId = null;
    
    // Function to open alamat selector modal
    window.openAlamatSelector = function(callback) {
        window.alamatSelectionCallback = callback;
        window.selectedAlamatId = null;
        
        // Show modal
        const modalElement = document.getElementById('alamatSelectorModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Load alamat data
        loadAlamatSelector();
        
        // Ensure the confirm button is properly set up
        const confirmBtn = document.getElementById('confirmAlamatSelection');
        if (confirmBtn) {
            // Remove any existing event listeners to prevent duplicates
            confirmBtn.replaceWith(confirmBtn.cloneNode(true));
            
            // Add event listener to the new button
            document.getElementById('confirmAlamatSelection').addEventListener('click', function() {
                if (window.selectedAlamatId) {
                    getAlamatDetails(window.selectedAlamatId, function(alamatData) {
                        // Call callback function with selected alamat data
                        if (window.alamatSelectionCallback) {
                            window.alamatSelectionCallback(alamatData);
                        }
                        
                        // Close modal
                        modal.hide();
                    });
                } else {
                    alert('Silakan pilih alamat terlebih dahulu');
                }
            });
        }
    };
    
    // Function to handle address selection
    window.selectAlamat = function(alamatId) {
        window.selectedAlamatId = alamatId;
        
        // Highlight the selected address
        const alamatOptions = document.querySelectorAll('.alamat-option');
        alamatOptions.forEach(option => {
            if (option.dataset.alamatId == alamatId) {
                option.classList.add('selected');
            } else {
                option.classList.remove('selected');
            }
        });
    };
});

// Function to load alamat selector content
function loadAlamatSelector() {
    fetch('/buyer/alamat/select', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.alamats) {
            // Render the alamat options directly
            let html = '<div class="alamat-selector">';
            
            if (data.alamats.length > 0) {
                data.alamats.forEach(alamat => {
                    const isDefault = alamat.status_default === 'Y';
                    html += `
                    <div class="alamat-option border rounded p-3 mb-3" 
                         data-alamat-id="${alamat.alamat_id}"
                         onclick="selectAlamat(${alamat.alamat_id})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold">${alamat.nama_penerima}</h6>
                                    ${isDefault ? '<span class="badge bg-success ms-2">Alamat Utama</span>' : ''}
                                </div>
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-phone me-1"></i>${alamat.no_telepon}
                                </p>
                                <p class="mb-1">${alamat.alamat}</p>
                                <p class="mb-0 text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    ${alamat.kota}, ${alamat.provinsi} ${alamat.kode_pos}
                                </p>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm select-alamat-btn" 
                                        onclick="event.stopPropagation(); selectAlamat(${alamat.alamat_id})">
                                    <i class="fas fa-check me-1"></i>Pilih
                                </button>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                html += `
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Alamat</h5>
                    <p class="text-muted mb-3">Anda belum memiliki alamat pengiriman</p>
                    <a href="/dashboard/alamat/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Alamat Pertama
                    </a>
                </div>`;
            }
            
            html += '</div>';
            
            document.getElementById('alamatSelectorContent').innerHTML = html;
            
            // Add styles for the alamat options
            const style = document.createElement('style');
            style.textContent = `
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
            `;
            document.head.appendChild(style);
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
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && callback) {
            callback(data.alamat);
        } else if (callback) {
            callback(null);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (callback) {
            callback(null);
        }
    });
}
</script>
