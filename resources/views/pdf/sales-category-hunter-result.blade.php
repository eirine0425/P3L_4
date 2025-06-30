@extends('layouts.dashboard')

@section('title', 'Hasil Laporan Penjualan per Kategori dengan Hunter')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Laporan Penjualan per Kategori dengan Hunter
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.owner.sales-report-category-hunter-pdf') }}?start_date={{ $filters['start_date'] }}&end_date={{ $filters['end_date'] }}&min_products={{ $filters['min_products'] }}" 
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf mr-1"></i>
                            Download PDF
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Report Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Periode Laporan</span>
                                    <span class="info-box-number">{{ $filters['period_text'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-boxes"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Minimal Produk</span>
                                    <span class="info-box-number">{{ $filters['min_products'] }} produk</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $summary['total_categories'] }}</h3>
                                    <p>Total Kategori</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($summary['total_products']) }}</h3>
                                    <p>Total Produk</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-cube"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($summary['total_sold']) }}</h3>
                                    <p>Produk Terjual</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ number_format($summary['total_hunters']) }}</h3>
                                    <p>Total Hunter</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle mr-2"></i>Ringkasan Laporan</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Pendapatan:</strong><br>
                                        <span class="h4 text-success">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Persentase Penjualan:</strong><br>
                                        <span class="h4 text-primary">{{ $summary['sales_percentage'] }}%</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Rata-rata Produk/Kategori:</strong><br>
                                        <span class="h4 text-warning">{{ $summary['avg_products_per_category'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($sales_by_category->count() > 0)
                        <!-- Sales Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="salesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 20%;">Kategori</th>
                                        <th style="width: 8%;">Total<br>Produk</th>
                                        <th style="width: 8%;">Terjual</th>
                                        <th style="width: 8%;">Tidak<br>Terjual</th>
                                        <th style="width: 12%;">Total<br>Pendapatan</th>
                                        <th style="width: 10%;">Rata-rata<br>Harga</th>
                                        <th style="width: 8%;">Jumlah<br>Hunter</th>
                                        <th style="width: 21%;">Nama Hunter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales_by_category as $index => $category)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><strong>{{ $category->nama_kategori }}</strong></td>
                                        <td class="text-center">{{ number_format($category->total_products) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">{{ number_format($category->items_sold) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-warning">{{ number_format($category->items_unsold) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <strong>Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</strong>
                                        </td>
                                        <td class="text-right">
                                            Rp {{ number_format($category->average_price ?: 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ $category->hunter_count }}</span>
                                        </td>
                                        <td class="hunter-names">
                                            {{ $category->hunter_names ?: '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                        <td class="text-center"><strong>{{ number_format($summary['total_products']) }}</strong></td>
                                        <td class="text-center"><strong>{{ number_format($summary['total_sold']) }}</strong></td>
                                        <td class="text-center"><strong>{{ number_format($summary['total_unsold']) }}</strong></td>
                                        <td class="text-right"><strong>Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</strong></td>
                                        <td class="text-center">-</td>
                                        <td class="text-center"><strong>{{ number_format($summary['total_hunters']) }}</strong></td>
                                        <td class="text-center">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <h4><i class="fas fa-exclamation-triangle mr-2"></i>Tidak Ada Data</h4>
                            <p>Tidak ditemukan kategori yang memenuhi kriteria:</p>
                            <ul class="list-unstyled">
                                <li>✓ Memiliki hunter</li>
                                <li>✓ Minimal {{ $filters['min_products'] }} produk</li>
                                <li>✓ Pada periode {{ $filters['period_text'] }}</li>
                            </ul>
                            <p class="mb-0">Silakan coba dengan kriteria yang berbeda.</p>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Laporan dibuat pada: {{ date('d/m/Y H:i:s') }}
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('dashboard.owner.sales-report-category-hunter-form') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Kembali ke Form
                            </a>
                            <a href="{{ route('dashboard.owner.index') }}" class="btn btn-primary ml-2">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable if there's data
    @if($sales_by_category->count() > 0)
    $('#salesTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }, // No and Hunter names columns
            { "className": "text-center", "targets": [0, 2, 3, 4, 7] },
            { "className": "text-right", "targets": [5, 6] }
        ],
        "order": [[ 5, "desc" ]], // Sort by total revenue descending
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm'
            }
        ]
    });
    @endif
});
</script>
@endpush

@push('styles')
<style>
.hunter-names {
    font-size: 0.85em;
    color: #007bff;
    font-weight: 500;
}

.table th, .table td {
    vertical-align: middle;
}

.small-box .inner h3 {
    font-size: 2.2rem;
}

.info-box-number {
    font-size: 1.1rem;
    font-weight: bold;
}

.badge {
    font-size: 0.8em;
}

.table-dark {
    background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
}

.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .hunter-names {
        font-size: 0.75em;
    }
}
</style>
@endpush
@endsection
