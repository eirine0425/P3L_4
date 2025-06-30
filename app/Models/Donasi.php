<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Barang;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';
    protected $primaryKey = 'request_id';

    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'deskripsi',
        'tanggal_donasi',
        'nama_kategori',
        'nama_penerima',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
    public function requestDonasi()
{
    return $this->belongsTo(RequestDonasi::class, 'request_id');
}
}
