@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Contoh Penggunaan Dropdown Alamat</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold">Alamat Pengiriman</h6>
                        <div id="selectedAlamatDisplay" class="border rounded p-3 mb-3">
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                                <p>Belum ada alamat yang dipilih</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="pilihAlamatBtn">
                            <i class="fas fa-map-marker-alt me-2"></i>Pilih Alamat
                        </button>
                    </div>
                    
                    <hr>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold">Kode Contoh</h6>
                        <pre class="bg-light p-3 rounded"><code>// Tombol untuk membuka modal
&lt;button type="button" class="btn btn-outline-primary" id="pilihAlamatBtn"&gt;
    &lt;i class="fas fa-map-marker-alt me-2"&gt;&lt;/i&gt;Pilih Alamat
&lt;/button&gt;

// JavaScript untuk menangani pemilihan alamat
document.getElementById('pilihAlamatBtn').addEventListener('click', function() {
    // Buka modal pemilihan alamat
    openAlamatSelector(function(alamat) {
        // Callback akan dipanggil ketika alamat dipilih
        if (alamat) {
            // Tampilkan alamat yang dipilih
            const displayEl = document.getElementById('selectedAlamatDisplay');
            displayEl.innerHTML = `
                &lt;div class="d-flex justify-content-between align-items-start"&gt;
                    &lt;div&gt;
                        &lt;h6 class="mb-1 fw-bold"&gt;${alamat.nama_penerima}&lt;/h6&gt;
                        &lt;p class="mb-1"&gt;${alamat.no_telepon}&lt;/p&gt;
                        &lt;p class="mb-1"&gt;${alamat.alamat}&lt;/p&gt;
                        &lt;p class="mb-0 text-muted"&gt;${alamat.kota}, ${alamat.provinsi} ${alamat.kode_pos}&lt;/p&gt;
                    &lt;/div&gt;
                    &lt;div&gt;
                        &lt;span class="badge bg-success"&gt;Terpilih&lt;/span&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            `;
            
            // Simpan ID alamat untuk digunakan nanti
            document.getElementById('alamat_id').value = alamat.alamat_id;
        }
    });
});</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the alamat selector modal -->
@include('components.alamat-selector-modal')

<!-- Hidden input to store the selected alamat ID -->
<input type="hidden" id="alamat_id" name="alamat_id" value="">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up the button to open the alamat selector modal
    document.getElementById('pilihAlamatBtn').addEventListener('click', function() {
        // Open the alamat selector modal
        openAlamatSelector(function(alamat) {
            // This callback will be called when an address is selected
            if (alamat) {
                // Display the selected address
                const displayEl = document.getElementById('selectedAlamatDisplay');
                displayEl.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 fw-bold">${alamat.nama_penerima}</h6>
                            <p class="mb-1">${alamat.no_telepon}</p>
                            <p class="mb-1">${alamat.alamat}</p>
                            <p class="mb-0 text-muted">${alamat.kota}, ${alamat.provinsi} ${alamat.kode_pos}</p>
                        </div>
                        <div>
                            <span class="badge bg-success">Terpilih</span>
                        </div>
                    </div>
                `;
                
                // Store the alamat ID for later use
                document.getElementById('alamat_id').value = alamat.alamat_id;
            }
        });
    });
});
</script>
@endsection
