@extends('layouts.dashboard')

@section('title', 'Form Laporan Penjualan per Kategori dengan Hunter')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Form Laporan Penjualan per Kategori dengan Hunter
                    </h3>
                </div>
                
                <form action="{{ route('dashboard.owner.sales-report-category-hunter') }}" method="POST" id="salesReportForm">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Date Range Selection -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">
                                    <strong>Tanggal Mulai <span class="text-danger">*</span></strong>
                                </label>
                                <input type="date" 
                                       name="start_date" 
                                       id="start_date" 
                                       class="form-control" 
                                       value="{{ old('start_date', date('Y-01-01')) }}" 
                                       required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">
                                    <strong>Tanggal Selesai <span class="text-danger">*</span></strong>
                                </label>
                                <input type="date" 
                                       name="end_date" 
                                       id="end_date" 
                                       class="form-control" 
                                       value="{{ old('end_date', date('Y-12-31')) }}" 
                                       required>
                            </div>
                            <div class="col-md-4">
                                <label for="min_products" class="form-label">
                                    <strong>Minimal Produk <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" 
                                       name="min_products" 
                                       id="min_products" 
                                       class="form-control" 
                                       value="{{ old('min_products', 3) }}" 
                                       min="1" 
                                       max="100" 
                                       required>
                                <small class="form-text text-muted">Minimal jumlah produk per kategori</small>
                            </div>
                        </div>

                        <!-- Information Box -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle mr-2"></i>Informasi Laporan:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Hunter:</strong> Pegawai dengan role "hunter" yang bertugas mengambil barang dari penitip</li>
                                        <li><strong>Kategori yang Ditampilkan:</strong> Hanya kategori yang memiliki hunter dan minimal {{ old('min_products', 3) }} produk</li>
                                        <li><strong>Data Penjualan:</strong> Berdasarkan transaksi dengan status "selesai" pada periode yang dipilih</li>
                                        <li><strong>Informasi Hunter:</strong> Menampilkan jumlah dan nama hunter per kategori</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Preview -->
                        @if($categories->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-tags mr-2"></i>
                                    Kategori Tersedia ({{ $categories->count() }} kategori)
                                </h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th>Nama Kategori</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categories->take(10) as $index => $category)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $category->nama_kategori }}</td>
                                                </tr>
                                            @endforeach
                                            @if($categories->count() > 10)
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">
                                                        <em>... dan {{ $categories->count() - 10 }} kategori lainnya</em>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Instructions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle mr-2"></i>Petunjuk Penggunaan:</h6>
                                    <ol class="mb-0">
                                        <li>Pilih rentang tanggal untuk periode laporan</li>
                                        <li>Tentukan minimal jumlah produk per kategori (default: 3)</li>
                                        <li>Klik "Tampilkan Laporan" untuk melihat hasil di browser</li>
                                        <li>Klik "Download PDF" untuk mengunduh laporan dalam format PDF</li>
                                        <li>Laporan hanya menampilkan kategori yang memiliki hunter dan memenuhi kriteria minimal produk</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" name="action" value="display" class="btn btn-primary">
                                    <i class="fas fa-eye mr-2"></i>
                                    Tampilkan Laporan
                                </button>
                                
                                <button type="submit" name="action" value="pdf" class="btn btn-danger ml-2">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    Download PDF
                                </button>
                                
                                <a href="{{ route('dashboard.owner.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#salesReportForm').on('submit', function(e) {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const minProducts = $('#min_products').val();
        
        if (!startDate || !endDate || !minProducts) {
            e.preventDefault();
            alert('Semua field wajib diisi!');
            return false;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            e.preventDefault();
            alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!');
            $('#start_date').focus();
            return false;
        }
        
        if (parseInt(minProducts) < 1 || parseInt(minProducts) > 100) {
            e.preventDefault();
            alert('Minimal produk harus antara 1-100!');
            $('#min_products').focus();
            return false;
        }
        
        // Show loading indicator
        const submitBtn = $(this).find('button[type="submit"]:focus');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
        
        // Re-enable button after 10 seconds (fallback)
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 10000);
    });
    
    // Update min products info dynamically
    $('#min_products').on('input', function() {
        const value = $(this).val();
        $('.alert-info li:nth-child(2)').html('<strong>Kategori yang Ditampilkan:</strong> Hanya kategori yang memiliki hunter dan minimal ' + value + ' produk');
    });
    
    // Set max date to today
    const today = new Date().toISOString().split('T')[0];
    $('#start_date, #end_date').attr('max', today);
    
    // Auto-adjust end date when start date changes
    $('#start_date').on('change', function() {
        const startDate = $(this).val();
        $('#end_date').attr('min', startDate);
        
        if ($('#end_date').val() < startDate) {
            $('#end_date').val(startDate);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.table-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    color: white;
}
</style>
@endpush
@endsection
