<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KategoriBarang;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

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
}
