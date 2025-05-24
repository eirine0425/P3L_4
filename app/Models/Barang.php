<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KategoriBarang;

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
    ];

    // KEPT ORIGINAL: Relationships
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'penitip_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function garansi()
    {
        return $this->belongsTo(Garansi::class, 'garansi_id');
    }

    // ADDED: Relationship to discussions/reviews
    public function diskusi()
    {
        return $this->hasMany(DiskusiProduk::class, 'barang_id');
    }

    // ADDED: Accessor for rating if it doesn't exist in the database
    public function getRatingAttribute($value)
    {
        // If there's a rating column with a value, return it directly
        if ($value !== null) {
            return $value;
        }

        // Otherwise calculate from diskusi/reviews if available
        $reviews = $this->diskusi()->whereNotNull('rating')->get();
        if ($reviews->count() > 0) {
            return $reviews->avg('rating');
        }

        return 0;
    }

    // ADDED: Accessor for jumlah_ulasan if it doesn't exist
    public function getJumlahUlasanAttribute()
    {
        return $this->diskusi()->whereNotNull('rating')->count();
    }

        public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'barang_id', 'barang_id');
    }

    
}
