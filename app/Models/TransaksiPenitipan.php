<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPenitipan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penitipan';
    protected $primaryKey = 'id'; // Make sure this matches your database
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

    protected $casts = [
        'status_perpanjangan' => 'boolean',
        'batas_penitipan' => 'date',
        'tanggal_penitipan' => 'date',
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
