<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeranjangBelanja extends Model
{
    use HasFactory;

    protected $table = 'keranjang_belanja';
    protected $primaryKey = 'keranjang_id';
    
    // Aktifkan timestamps jika tabel memiliki kolom created_at dan updated_at
    public $timestamps = true;
    
    protected $fillable = [
        'pembeli_id',
        'barang_id'
    ];

    // Relasi ke model Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id', 'pembeli_id');
    }

    // Relasi ke model Barang - PERBAIKAN DISINI
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
