<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeranjangBelanja extends Model
{
    use HasFactory;

    protected $table = 'keranjang_belanja';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'barang_id',
        'pembeli_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id', 'pembeli_id');
    }
}
