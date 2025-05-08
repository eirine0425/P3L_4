<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Barang;
use App\Models\Pembeli;

class KeranjangBelanja extends Model
{
    use HasFactory;

    protected $table = 'keranjang_belanja';
    protected $primaryKey = 'keranjang_id';
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'pembeli_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id');
    }
}
