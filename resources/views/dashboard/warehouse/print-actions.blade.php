{{-- Komponen aksi cetak yang bisa digunakan di berbagai halaman --}}
@props(['transactions' => []])

<div class="print-actions mb-3">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkPrintModal">
            <i class="fas fa-print"></i> Cetak Massal
        </button>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Include bulk print modal --}}
@include('dashboard.warehouse.bulk-print-modal', ['transactions' => $transactions])

<script>
function exportToPDF() {
    // Implement PDF export functionality
    alert('Fitur export PDF akan segera tersedia');
}

function exportToExcel() {
    // Implement Excel export functionality
    alert('Fitur export Excel akan segera tersedia');
}
</script>
