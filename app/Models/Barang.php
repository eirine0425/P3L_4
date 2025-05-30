<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use App\Models\Garansi;
use App\Models\DiskusiProduk;
use App\Models\DetailTransaksi;
use App\Models\TransaksiPenitipan;
use App\Models\KeranjangBelanja;
use App\Models\Rating;
use Carbon\Carbon;

class Barang extends Model
{
    use HasFactory;

    // Table configuration
    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

    // Merged fillable fields from both models
    protected $fillable = [
        'penitip_id',
        'kategori_id',
        'status',
        'kondisi',
        'nama_barang',
        'deskripsi',
        'deskripsi_barang', // Alternative description field
        'harga',
        'stok',
        'rating',
        'tanggal_penitipan',
        'batas_penitipan',
        'garansi_id',
        'foto_barang',
        'gambar', // Alternative image field
        'penjual_id', // For backward compatibility
    ];

    // Cast attributes
    protected $casts = [
        'tanggal_penitipan' => 'date',
        'batas_penitipan' => 'date',
        'harga' => 'decimal:2',
        'rating' => 'decimal:1',
    ];

    // RELATIONSHIPS
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'penitip_id');
    }

    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id', 'kategori_id');
    }

    public function kategori()
    {
        return $this->kategoriBarang();
    }

    public function garansi()
    {
        return $this->belongsTo(Garansi::class, 'garansi_id');
    }

    public function diskusi()
    {
        return $this->hasMany(DiskusiProduk::class, 'barang_id');
    }

    public function keranjangBelanja()
    {
        return $this->hasMany(KeranjangBelanja::class, 'barang_id', 'barang_id');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'barang_id', 'barang_id');
    }

    public function transaksiPenitipan()
    {
        return $this->hasOne(TransaksiPenitipan::class, 'barang_id', 'barang_id');
    }

    // RATING RELATIONSHIPS AND METHODS
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'barang_id', 'barang_id');
    }

    /**
     * Get calculated average rating from ratings table
     */
    public function getRatingAttribute($value)
    {
        // If we have ratings in the ratings table, calculate average
        $averageRating = $this->ratings()->avg('rating');
        if ($averageRating) {
            return round($averageRating, 1);
        }
        
        // Fallback to stored rating value or 0
        return $value ?? 0;
    }

    /**
     * Get total number of ratings
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get rating distribution (count for each star level)
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Get star display for average rating
     */
    public function getStarDisplayAttribute()
    {
        $rating = $this->rating;
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        
        $stars = str_repeat('★', $fullStars);
        if ($hasHalfStar) {
            $stars .= '☆';
            $fullStars++;
        }
        $stars .= str_repeat('☆', 5 - $fullStars);
        
        return $stars;
    }

    /**
     * Check if user can rate this item
     */
    public function canBeRatedBy($pembeliId)
    {
        // Check if buyer has purchased this item and transaction is completed
        $hasPurchased = DetailTransaksi::whereHas('transaksi', function($query) use ($pembeliId) {
            $query->where('pembeli_id', $pembeliId)
                  ->where('status_transaksi', 'Selesai');
        })->where('barang_id', $this->barang_id)->exists();
        
        if (!$hasPurchased) {
            return false;
        }
        
        // Check if already rated
        $alreadyRated = Rating::where('pembeli_id', $pembeliId)
                         ->where('barang_id', $this->barang_id)
                         ->exists();
        
        return !$alreadyRated;
    }

    /**
     * Get user's rating for this item
     */
    public function getUserRating($pembeliId)
    {
        return Rating::where('pembeli_id', $pembeliId)
                 ->where('barang_id', $this->barang_id)
                 ->first();
    }

    // DISCUSSION METHODS
    public function getJumlahUlasanAttribute()
    {
        return $this->diskusi()->count();
    }

    public function getFormattedRatingAttribute()
    {
        $rating = $this->rating;
        return number_format($rating, 1);
    }

    public function hasDiscussions()
    {
        return $this->diskusi()->exists();
    }

    public function getRecentDiscussions($limit = 5)
    {
        return $this->diskusi()
            ->orderBy('tanggal_diskusi', 'desc')
            ->limit($limit)
            ->get();
    }

    // CONSIGNMENT STATUS METHODS
    public function getConsignmentStatusAttribute()
    {
        if (!$this->transaksiPenitipan) {
            return null;
        }
        
        return [
            'tanggal_penitipan' => $this->transaksiPenitipan->tanggal_penitipan,
            'batas_penitipan' => $this->transaksiPenitipan->batas_penitipan,
            'durasi_penitipan' => $this->transaksiPenitipan->durasi_penitipan,
            'sisa_hari' => $this->transaksiPenitipan->sisa_hari,
            'is_expired' => $this->transaksiPenitipan->is_expired,
            'status_durasi' => $this->transaksiPenitipan->status_durasi,
        ];
    }

    // SCOPES
    public function scopeAvailable($query)
    {
        return $query->where('status', 'belum_terjual');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'terjual');
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) <= 7')
                     ->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) >= 0');
    }

    public function scopeExpired($query)
    {
        return $query->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) < 0');
    }

    public function scopeNeedsAttention($query)
    {
        return $query->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) <= 7');
    }

    // STATUS AND CONDITION METHODS
    public function isAvailable()
    {
        return $this->status === 'belum_terjual';
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'belum_terjual':
                return 'badge-success';
            case 'terjual':
                return 'badge-info';
            case 'sold out':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    public function getStatusDisplayText()
    {
        switch ($this->status) {
            case 'belum_terjual':
                return 'Tersedia';
            case 'terjual':
                return 'Terjual';
            case 'sold out':
                return 'Sold Out';
            default:
                return ucfirst($this->status);
        }
    }

    public function getConditionBadgeClass()
    {
        switch (strtolower($this->kondisi)) {
            case 'baru':
                return 'badge-primary';
            case 'bekas':
                return 'badge-warning';
            case 'rusak':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    // FORMATTING METHODS
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getPhotoUrlAttribute()
    {
        // Check both possible image fields
        $imageField = $this->foto_barang ?? $this->gambar;
        
        if ($imageField && file_exists(storage_path('app/public/' . $imageField))) {
            return asset('storage/' . $imageField);
        }
        
        return '/placeholder.svg?height=200&width=200&text=' . urlencode($this->nama_barang);
    }

    // DATE HANDLING METHODS
    public function setTanggalPenitipanAttribute($value)
    {
        $this->attributes['tanggal_penitipan'] = $value;
        
        if ($value) {
            $this->attributes['batas_penitipan'] = Carbon::parse($value)->addDays(30)->toDateString();
        }
    }

    public function getTanggalMulaiPenitipanAttribute()
    {
        return $this->tanggal_penitipan ? 
            Carbon::parse($this->tanggal_penitipan) : 
            Carbon::parse($this->created_at);
    }

    public function getBatasPenitipanAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value);
        }
        
        // Calculate if not set
        $tanggalMulai = $this->tanggal_mulai_penitipan;
        return $tanggalMulai->copy()->addDays(30);
    }
    public function fotoTambahan()
{
    return $this->hasMany(FotoBarang::class, 'barang_id');
}

    public function getSisaHariAttribute()
    {
        $today = Carbon::now();
        $batasPeritipan = $this->batas_penitipan;
        
        return $today->diffInDays($batasPeritipan, false); // false = can be negative
    }

    public function getIsExpiredAttribute()
    {
        return $this->sisa_hari < 0;
    }

    public function getStatusDurasiAttribute()
    {
        if ($this->sisa_hari < 0) {
            return 'expired'; // Sudah lewat batas
        } elseif ($this->sisa_hari <= 7) {
            return 'warning'; // Kurang dari 7 hari
        } elseif ($this->sisa_hari <= 14) {
            return 'caution'; // Kurang dari 14 hari
        } else {
            return 'safe'; // Masih aman
        }
    }

    public function getStatusDurasiBadgeClassAttribute()
    {
        switch ($this->status_durasi) {
            case 'expired':
                return 'bg-danger';
            case 'warning':
                return 'bg-warning';
            case 'caution':
                return 'bg-info';
            case 'safe':
                return 'bg-success';
            default:
                return 'bg-secondary';
        }
    }

    public function getStatusDurasiTextAttribute()
    {
        switch ($this->status_durasi) {
            case 'expired':
                return 'Kadaluarsa';
            case 'warning':
                return 'Segera Berakhir';
            case 'caution':
                return 'Perhatian';
            case 'safe':
                return 'Aman';
            default:
                return 'Tidak Diketahui';
        }
    }

    public function getFormattedSisaWaktuAttribute()
    {
        if ($this->sisa_hari < 0) {
            $hariLewat = abs($this->sisa_hari);
            return "Lewat {$hariLewat} hari";
        } elseif ($this->sisa_hari == 0) {
            return 'Berakhir hari ini';
        } else {
            return "{$this->sisa_hari} hari lagi";
        }
    }

    // COMPATIBILITY METHODS
    /**
     * Get description - handles both field names
     */
    public function getDescriptionAttribute()
    {
        return $this->deskripsi ?? $this->deskripsi_barang ?? '';
    }

    /**
     * Get image - handles both field names
     */
    public function getImageAttribute()
    {
        return $this->foto_barang ?? $this->gambar ?? '';
    }

    /**
     * Check if item has stock (for backward compatibility)
     */
    public function hasStock()
    {
        return $this->stok > 0 || $this->isAvailable();
    }

    /**
     * Get seller/consignor info
     */
    public function getSellerAttribute()
    {
        return $this->penitip ?? null;
    }
}
