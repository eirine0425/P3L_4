<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function keranjang()
    {
        return $this->belongsTo(KeranjangBelanja::class, 'keranjang_id');
    }
}
