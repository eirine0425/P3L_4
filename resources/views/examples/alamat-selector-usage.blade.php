@extends('layouts.dashboard')

@section('title', 'Contoh Penggunaan Alamat Selector')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Contoh Penggunaan Fitur Pilih Alamat</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Contoh 1: Menggunakan komponen alamat display -->
                    <div class="mb-4">
                        <h5>Contoh 1: Komponen Alamat Display</h5>
                        <p class="text-muted">Komponen ini akan menampilkan alamat yang dipilih dan tombol untuk mengubahnya.</p>
                        
                        @include('components.alamat-display')
                    </div>
                    
                    <hr>
                    
                    <!-- Contoh 2: Tombol manual untuk membuka selector -->
                    <div class="mb-4">
                        <h5>Contoh 2: Tombol Manual</h5>
                        <p class="text-muted">Anda bisa membuka selector alamat dengan tombol custom.</p>
                        
                        <button type="button" class="btn btn-primary" onclick="openAlamatSelector(customAlamatHandler)">
                            <i class="fas fa-map-marker-alt me-2"></i>Pilih Alamat Custom
                        </button>
                        
                        <div id="customAlamatResult" class="mt-3"></div>
                    </div>
                    
                    <hr>
                    
                    <!-- Contoh 3: Dalam form -->
                    <div class="mb-4">
                        <h5>Contoh 3: Dalam Form</h5>
                        <p class="text-muted">Contoh penggunaan dalam form checkout atau pengiriman.</p>
                        
                        <form id="checkoutForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Produk</label>
                                <input type="text" class="form-control" value="Contoh Produk" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat Pengiriman</label>
                                @include('components.alamat-display')
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-shopping-cart me-2"></i>Proses Checkout
                            </button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modal component -->
@include('components.alamat-selector-modal')

<script>
// Custom handler untuk contoh 2
function customAlamatHandler(alamatData) {
    const resultDiv = document.getElementById('customAlamatResult');
    resultDiv.innerHTML = `
        <div class="alert alert-success">
            <h6><i class="fas fa-check-circle me-2"></i>Alamat berhasil dipilih:</h6>
            <strong>${alamatData.nama_penerima}</strong><br>
            ${alamatData.alamat}<br>
            ${alamatData.kota}, ${alamatData.provinsi} ${alamatData.kode_pos}
        </div>
    `;
}

// Handle form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedAlamatId = document.getElementById('selectedAlamatId').value;
    
    if (!selectedAlamatId) {
        alert('Silakan pilih alamat pengiriman terlebih dahulu');
        return;
    }
    
    alert('Form akan disubmit dengan alamat ID: ' + selectedAlamatId);
    // Di sini Anda bisa melanjutkan dengan submit form yang sebenarnya
});

// Listen untuk event alamat selected
document.addEventListener('alamatSelected', function(e) {
    console.log('Alamat dipilih:', e.detail);
    // Anda bisa menambahkan logic tambahan di sini
});
</script>
@endsection
