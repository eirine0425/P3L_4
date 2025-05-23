@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h2><i class="fas fa-box-open me-2"></i>Produk</h2>
        <p>Menampilkan 1–12 dari 100 produk</p>
    </div>

    <!-- Product Grid -->
    <div class="row justify-content-center g-4">
        @for ($i = 1; $i <= 12; $i++)
        <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
            <div class="card h-100 w-100 shadow-sm">
                <div class="position-relative">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product {{ $i }}">
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Tersedia</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Produk {{ $i }}</h5>
                    <p class="card-text text-center small">Deskripsi singkat produk {{ $i }} yang menjelaskan kondisi dan kualitas barang.</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Rp {{ number_format(rand(100000, 1000000), 0, ',', '.') }}</span>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-1 small">({{ rand(10, 50) }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="d-grid gap-2">
                        <a href="{{ url('/products/' . $i) }}" class="btn btn-outline-primary">Detail</a>
                        @auth
                            @if(auth()->user()->role->nama_role == 'Pembeli')
                            <button class="btn btn-primary add-to-cart" data-product-id="{{ $i }}">
                                <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                            </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="#"><span>&laquo;</span></a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#"><span>&raquo;</span></a>
                </li>
            </ul>
        </nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const toastContainer = document.createElement('div');
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '11';

            toastContainer.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong class="me-auto">Berhasil</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Produk berhasil ditambahkan ke keranjang.
                    </div>
                </div>
            `;
            document.body.appendChild(toastContainer);
            setTimeout(() => toastContainer.remove(), 3000);
        });
    });
</script>
@endpush
