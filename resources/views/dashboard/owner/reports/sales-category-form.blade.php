@extends('layouts.dashboard')

@section('title', 'Form Laporan Penjualan per Kategori')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Form Laporan Penjualan per Kategori Barang
                    </h3>
                </div>
                
                <form action="{{ route('dashboard.owner.sales-report-category-print') }}" method="POST" id="salesReportForm">
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

                        <!-- Year Selection -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="year" class="form-label">
                                    <strong>Tahun Laporan <span class="text-danger">*</span></strong>
                                </label>
                                <select name="year" id="year" class="form-control" required>
                                    <option value="">Pilih Tahun</option>
                                    @for($i = 2020; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ old('year', date('Y')) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Categories Data -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-tags mr-2"></i>
                                    Data Penjualan per Kategori
                                </h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 50%;">Kategori Barang</th>
                                                <th style="width: 25%;">Jumlah Item Terjual</th>
                                                <th style="width: 25%;">Jumlah Item Gagal Terjual</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoriesTable">
                                            @foreach($categories as $index => $category)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="categories[{{ $index }}][nama_kategori]" value="{{ $category->nama_kategori }}">
                                                        <strong>{{ $category->nama_kategori }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="categories[{{ $index }}][items_sold]" 
                                                               class="form-control items-sold" 
                                                               min="0" 
                                                               value="{{ old('categories.'.$index.'.items_sold', 0) }}"
                                                               placeholder="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="categories[{{ $index }}][items_unsold]" 
                                                               class="form-control items-unsold" 
                                                               min="0" 
                                                               value="{{ old('categories.'.$index.'.items_unsold', 0) }}"
                                                               placeholder="0">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th><strong>Total</strong></th>
                                                <th><span id="totalSold">0</span></th>
                                                <th><span id="totalUnsold">0</span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle mr-2"></i>Petunjuk Pengisian:</h6>
                                    <ul class="mb-0">
                                        <li>Pilih tahun laporan yang ingin dibuat</li>
                                        <li>Isi jumlah item terjual dan gagal terjual untuk setiap kategori</li>
                                        <li>Kosongkan atau isi 0 jika tidak ada data untuk kategori tertentu</li>
                                        <li>Total akan dihitung otomatis</li>
                                        <li>Klik "Lihat Print" untuk melihat hasil atau "Download PDF" untuk mengunduh</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" name="action" value="print" class="btn btn-primary">
                                    <i class="fas fa-print mr-2"></i>
                                    Lihat Print Preview
                                </button>
                                
                                <button type="submit" name="action" value="pdf" class="btn btn-danger ml-2">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    Download PDF
                                </button>
                                
                                <a href="{{ route('dashboard.owner.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
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
    // Calculate totals when input changes
    function calculateTotals() {
        let totalSold = 0;
        let totalUnsold = 0;
        
        $('.items-sold').each(function() {
            totalSold += parseInt($(this).val()) || 0;
        });
        
        $('.items-unsold').each(function() {
            totalUnsold += parseInt($(this).val()) || 0;
        });
        
        $('#totalSold').text(totalSold);
        $('#totalUnsold').text(totalUnsold);
    }
    
    // Bind events
    $('.items-sold, .items-unsold').on('input change', calculateTotals);
    
    // Calculate initial totals
    calculateTotals();
    
    // Form validation
    $('#salesReportForm').on('submit', function(e) {
        const year = $('#year').val();
        if (!year) {
            e.preventDefault();
            alert('Silakan pilih tahun laporan terlebih dahulu!');
            $('#year').focus();
            return false;
        }
        
        // Check if at least one category has data
        let hasData = false;
        $('.items-sold, .items-unsold').each(function() {
            if (parseInt($(this).val()) > 0) {
                hasData = true;
                return false;
            }
        });
        
        if (!hasData) {
            const confirm = window.confirm('Tidak ada data yang diisi. Apakah Anda yakin ingin melanjutkan?');
            if (!confirm) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.table th, .table td {
    vertical-align: middle;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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

.table-dark {
    background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
}
</style>
@endpush
@endsection
