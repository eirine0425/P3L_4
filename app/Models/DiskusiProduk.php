<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Pembeli;
use App\Models\Barang;

class DiskusiProduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'diskusi_produk';
    protected $primaryKey = 'diskusi_id';

    public $timestamps = false;

    protected $fillable = [
        'pembeli_id',
        'barang_id',
        'pertanyaan',
        'jawaban',
        'tanggal_diskusi',
    ];

    protected $dates = [
        'tanggal_diskusi',
        'deleted_at'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
