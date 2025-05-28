<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Penitip;
use App\Models\Barang;

class TransaksiPenitipan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penitipan';
    protected $primaryKey = 'transaksi_penitipan_id';
    public $timestamps = false;

    protected $fillable = [
        'penitip_id',
        'barang_id',
        'batas_penitipan',
        'tanggal_penitipan',
        'metode_penitipan',
        'status_perpanjangan',
        'status_penitipan',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'penitip_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
