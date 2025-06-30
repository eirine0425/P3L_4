@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">
        <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
        @if($cartItems->count() > 0)
            <span class="badge bg-primary">{{ $cartItems->count() }} item</span>
        @endif
    </h2>

    {{-- Flash Messages --}}
    @foreach (['success', 'error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                <i class="fas {{ $msg == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>{{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    @if($cartItems->count() > 0)
    <div class="row">
        {{-- KONTEN UTAMA --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Item dalam Keranjang</h5>
                    {{-- <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light">
                            <i class="fas fa-trash me-1"></i>Kosongkan Keranjang
                        </button>
                    </form> --}}
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                <th>Produk</th>
                                <th width="150">Harga</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input item-checkbox"
                                           value="{{ $item->keranjang_id }}"
                                           data-price="{{ $item->barang->harga ?? 0 }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->barang->foto_barang ? asset('storage/' . $item->barang->foto_barang) : '/placeholder.svg?height=60&width=60' }}"
                                             class="img-thumbnail me-3" width="60" height="60" style="object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">{{ $item->barang->nama_barang ?? 'Nama tidak tersedia' }}</h6>
                                            <small class="text-muted">
                                                {{ $item->barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori' }} | Kondisi: {{ $item->barang->kondisi ?? 'Baik' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold text-primary">Rp {{ number_format($item->barang->harga ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $item->keranjang_id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RINGKASAN --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Belanja</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Item dipilih:</span>
                        <span id="selected-count" class="fw-bold">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal" class="fw-bold">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ongkos Kirim:</span>
                        <span class="text-muted">Dihitung di checkout</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span id="total" class="fw-bold text-primary">Rp 0</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button id="checkout-btn" class="btn btn-success btn-lg" disabled>
                            <i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-bag me-2"></i>Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- KERANJANG KOSONG --}}
    <div class="card text-center shadow-sm">
        <div class="card-body py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-4"></i>
            <h4 class="text-muted mb-3">Keranjang Belanja Kosong</h4>
            <p class="text-muted mb-4">Belum ada produk dalam keranjang Anda.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle select all
    $('#select-all').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
        updateCheckoutSummary();
    });

    // Handle individual checkbox
    $('.item-checkbox').on('change', function() {
        updateCheckoutSummary();
    });

    function updateCheckoutSummary() {
        let total = 0;
        let count = 0;
        $('.item-checkbox:checked').each(function() {
            total += parseFloat($(this).data('price')) || 0;
            count++;
        });
        $('#selected-count').text(count);
        $('#subtotal').text('Rp ' + formatNumber(total));
        $('#total').text('Rp ' + formatNumber(total));

        $('#checkout-btn').prop('disabled', count === 0);
        $('#checkout-btn').html('<i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout' + (count > 0 ? ` (${count} item)` : ''));
    }

    function formatNumber(num) {
        return num.toLocaleString('id-ID');
    }

    $('#checkout-btn').on('click', function(e) {
        e.preventDefault();
        let selected = [];
        $('.item-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        if (selected.length === 0) {
            alert('Pilih item terlebih dahulu.');
            return;
        }
        window.location.href = '/checkout?selected_items=' + encodeURIComponent(JSON.stringify(selected));
    });
});
</script>
@endpush
