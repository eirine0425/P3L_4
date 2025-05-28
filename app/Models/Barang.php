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
use Carbon\Carbon;

class Barang extends Model
{
    use HasFactory;

    // KEPT ORIGINAL: Table name and primary key
    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

    // KEPT ORIGINAL: Fillable fields with added batas_penitipan
    protected $fillable = [
        'penitip_id',
        'kategori_id',
        'status',
        'kondisi',
        'nama_barang',
        'harga',
        'rating',
        'deskripsi',
        'tanggal_penitipan',
        'batas_penitipan',
        'garansi_id',
        'foto_barang',
    ];

    // KEPT ORIGINAL: Relationships
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

    // FIXED: Relationship to discussions (removed rating filter since column doesn't exist)
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

    // FIXED: Accessor for rating - use the rating column directly from barang table
    public function getRatingAttribute($value)
    {
        // Return the rating from barang table directly
        // If null, return 0 as default
        return $value ?? 0;
    }

    // FIXED: Accessor for jumlah_ulasan - count all discussions
    public function getJumlahUlasanAttribute()
    {
        // Count all discussions for this product
        return $this->diskusi()->count();
    }

    // ADDED: Method to get formatted rating (for display)
    public function getFormattedRatingAttribute()
    {
        $rating = $this->rating;
        return number_format($rating, 1);
    }

    // ADDED: Method to check if product has discussions
    public function hasDiscussions()
    {
        return $this->diskusi()->exists();
    }

    // ADDED: Method to get recent discussions
    public function getRecentDiscussions($limit = 5)
    {
        return $this->diskusi()
            ->orderBy('tanggal_diskusi', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get consignment status information
     */
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

    // ADDED: Scope for available products
    public function scopeAvailable($query)
    {
        return $query->where('status', 'belum_terjual');
    }

    // ADDED: Scope for sold products
    public function scopeSold($query)
    {
        return $query->where('status', 'terjual');
    }

    // ADDED: Method to check if product is available
    public function isAvailable()
    {
        return $this->status === 'belum_terjual';
    }

    // ADDED: Method to get status badge class
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

    // ADDED: Method to get status display text
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

    // ADDED: Method to get condition badge class
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

    // ADDED: Method to format price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // ADDED: Method to get photo URL
    public function getPhotoUrlAttribute()
    {
        if ($this->foto_barang && file_exists(storage_path('app/public/' . $this->foto_barang))) {
            return asset('storage/' . $this->foto_barang);
        }
        
        return '/placeholder.svg?height=200&width=200&text=' . urlencode($this->nama_barang);
    }

    /**
     * Set the tanggal_penitipan attribute and automatically calculate batas_penitipan
     */
    public function setTanggalPenitipanAttribute($value)
    {
        $this->attributes['tanggal_penitipan'] = $value;
        
        if ($value) {
            $this->attributes['batas_penitipan'] = Carbon::parse($value)->addDays(30)->toDateString();
        }
    }

    /**
     * Get the actual consignment start date
     */
    public function getTanggalMulaiPenitipanAttribute()
    {
        return $this->tanggal_penitipan ? 
            Carbon::parse($this->tanggal_penitipan) : 
            Carbon::parse($this->created_at);
    }

    /**
     * Calculate and get batas_penitipan if not set
     */
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

    /**
     * Get remaining days until expiry
     */
    public function getSisaHariAttribute()
    {
        $today = Carbon::now();
        $batasPeritipan = $this->batas_penitipan;
        
        return $today->diffInDays($batasPeritipan, false); // false = can be negative
    }

    /**
     * Check if consignment is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->sisa_hari < 0;
    }

    /**
     * Get consignment status based on remaining days
     */
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

    /**
     * Get status duration badge class
     */
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

    /**
     * Get status duration text
     */
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

    /**
     * Get formatted remaining time
     */
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

    /**
     * Scope for items expiring soon (within 7 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) <= 7')
                     ->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) >= 0');
    }

    /**
     * Scope for expired items
     */
    public function scopeExpired($query)
    {
        return $query->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) < 0');
    }

    /**
     * Scope for items that need attention (expiring soon or expired)
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereRaw('DATEDIFF(batas_penitipan, CURDATE()) <= 7');
    }
}
