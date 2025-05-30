@extends('layouts.dashboard')

@section('title', 'Rating & Ulasan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rating & Ulasan Saya</h3>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" id="ratingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="my-ratings-tab" data-bs-toggle="tab" data-bs-target="#my-ratings" type="button" role="tab">
                                Rating Saya ({{ $ratings->total() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ratable-items-tab" data-bs-toggle="tab" data-bs-target="#ratable-items" type="button" role="tab">
                                Belum Dirating ({{ $ratableItems->count() }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="ratingTabsContent">
                        <!-- My Ratings Tab -->
                        <div class="tab-pane fade show active" id="my-ratings" role="tabpanel">
                            @if($ratings->count() > 0)
                                <div class="row">
                                    @foreach($ratings as $rating)
                                        <div class="col-md-6 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start">
                                                        <img src="{{ $rating->barang->photo_url }}" 
                                                             alt="{{ $rating->barang->nama_barang }}" 
                                                             class="img-thumbnail me-3" 
                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ $rating->barang->nama_barang }}</h6>
                                                            <p class="text-muted small mb-2">
                                                                Transaksi: #{{ $rating->transaksi->transaksi_id }}
                                                            </p>
                                                            
                                                            <!-- Star Rating Display -->
                                                            <div class="mb-2">
                                                                <span class="text-warning fs-5">{{ $rating->star_display }}</span>
                                                                <span class="ms-2 text-muted">({{ $rating->rating }}/5)</span>
                                                            </div>
                                                            
                                                            @if($rating->review)
                                                                <p class="mb-2">{{ $rating->review }}</p>
                                                            @endif
                                                            
                                                            <small class="text-muted">
                                                                {{ $rating->created_at->format('d M Y H:i') }}
                                                            </small>
                                                            
                                                            <!-- Edit Button -->
                                                            <div class="mt-2">
                                                                <button class="btn btn-sm btn-outline-primary" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#editRatingModal{{ $rating->rating_id }}">
                                                                    Edit Rating
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Rating Modal -->
                                        <div class="modal fade" id="editRatingModal{{ $rating->rating_id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('buyer.rating.update', $rating->rating_id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Rating</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Rating</label>
                                                                <div class="rating-input">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <input type="radio" name="rating" value="{{ $i }}" 
                                                                               id="edit_star{{ $rating->rating_id }}_{{ $i }}"
                                                                               {{ $rating->rating == $i ? 'checked' : '' }}>
                                                                        <label for="edit_star{{ $rating->rating_id }}_{{ $i }}" class="star">★</label>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="review" class="form-label">Ulasan (Opsional)</label>
                                                                <textarea class="form-control" name="review" rows="3" 
                                                                          placeholder="Tulis ulasan Anda...">{{ $rating->review }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Update Rating</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $ratings->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                    <h5>Belum Ada Rating</h5>
                                    <p class="text-muted">Anda belum memberikan rating untuk barang apapun.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Ratable Items Tab -->
                        <div class="tab-pane fade" id="ratable-items" role="tabpanel">
                            @if($ratableItems->count() > 0)
                                <div class="row">
                                    @foreach($ratableItems as $item)
                                        <div class="col-md-6 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start">
                                                        <img src="{{ $item->barang->photo_url }}" 
                                                             alt="{{ $item->barang->nama_barang }}" 
                                                             class="img-thumbnail me-3" 
                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ $item->barang->nama_barang }}</h6>
                                                            <p class="text-muted small mb-2">
                                                                Transaksi: #{{ $item->transaksi->transaksi_id }}
                                                            </p>
                                                            <p class="text-success small mb-2">
                                                                <i class="fas fa-check-circle"></i> Transaksi Selesai
                                                            </p>
                                                            
                                                            <button class="btn btn-primary btn-sm" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#ratingModal{{ $item->detail_transaksi_id }}">
                                                                <i class="fas fa-star"></i> Beri Rating
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Rating Modal -->
                                        <div class="modal fade" id="ratingModal{{ $item->detail_transaksi_id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('buyer.rating.submit') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="barang_id" value="{{ $item->barang_id }}">
                                                        <input type="hidden" name="transaksi_id" value="{{ $item->transaksi_id }}">
                                                        
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Beri Rating</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center mb-3">
                                                                <img src="{{ $item->barang->photo_url }}" 
                                                                     alt="{{ $item->barang->nama_barang }}" 
                                                                     class="img-thumbnail" 
                                                                     style="width: 100px; height: 100px; object-fit: cover;">
                                                                <h6 class="mt-2">{{ $item->barang->nama_barang }}</h6>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Rating</label>
                                                                <div class="rating-input text-center">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <input type="radio" name="rating" value="{{ $i }}" 
                                                                               id="star{{ $item->detail_transaksi_id }}_{{ $i }}" required>
                                                                        <label for="star{{ $item->detail_transaksi_id }}_{{ $i }}" class="star">★</label>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="review" class="form-label">Ulasan (Opsional)</label>
                                                                <textarea class="form-control" name="review" rows="3" 
                                                                          placeholder="Tulis ulasan Anda tentang barang ini..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Kirim Rating</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5>Semua Sudah Dirating</h5>
                                    <p class="text-muted">Anda sudah memberikan rating untuk semua barang yang dibeli.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    justify-content: center;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input .star {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-input input[type="radio"]:checked ~ .star,
.rating-input .star:hover,
.rating-input .star:hover ~ .star {
    color: #ffc107;
}

.rating-input input[type="radio"]:checked + .star {
    color: #ffc107;
}

/* Fix for proper star highlighting */
.rating-input {
    flex-direction: row-reverse;
}

.rating-input .star:hover,
.rating-input .star:hover ~ .star,
.rating-input input[type="radio"]:checked + .star,
.rating-input input[type="radio"]:checked + .star ~ .star {
    color: #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle star rating clicks
    document.querySelectorAll('.rating-input').forEach(function(ratingContainer) {
        const stars = ratingContainer.querySelectorAll('.star');
        const radios = ratingContainer.querySelectorAll('input[type="radio"]');
        
        stars.forEach(function(star, index) {
            star.addEventListener('click', function() {
                radios[index].checked = true;
                updateStarDisplay(ratingContainer, index + 1);
            });
            
            star.addEventListener('mouseover', function() {
                updateStarDisplay(ratingContainer, index + 1);
            });
        });
        
        ratingContainer.addEventListener('mouseleave', function() {
            const checkedRadio = ratingContainer.querySelector('input[type="radio"]:checked');
            const checkedValue = checkedRadio ? parseInt(checkedRadio.value) : 0;
            updateStarDisplay(ratingContainer, checkedValue);
        });
    });
    
    function updateStarDisplay(container, rating) {
        const stars = container.querySelectorAll('.star');
        stars.forEach(function(star, index) {
            if (index < rating) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '#ddd';
            }
        });
    }
});
</script>
@endsection
