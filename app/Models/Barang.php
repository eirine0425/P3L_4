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

class Barang extends Model
{
    use HasFactory;

    // KEPT ORIGINAL: Table name and primary key
    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

    // KEPT ORIGINAL: Fillable fields
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
}
