<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Barang;

class KategoriBarang extends Model
{
    use HasFactory;

    protected $table = 'kategori_barang';
    protected $primaryKey = 'kategori_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
