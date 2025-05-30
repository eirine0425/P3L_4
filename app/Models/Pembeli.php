<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\KeranjangBelanja;

class Pembeli extends Model
{
    use HasFactory;

    protected $table = 'pembeli';
    protected $primaryKey = 'pembeli_id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'user_id',
        'keranjang_id',
        'poin_loyalitas',
        'tanggal_registrasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function keranjang()
    {
        return $this->belongsTo(KeranjangBelanja::class, 'keranjang_id');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'pembeli_id', 'pembeli_id');
    }

    /**
     * Relationship with ratings
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'pembeli_id', 'pembeli_id');
    }

    /**
     * Get total ratings given by this buyer
     */
    public function getTotalRatingsGivenAttribute()
    {
        return $this->ratings()->count();
    }
}