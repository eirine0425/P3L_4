<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pembeli;
use App\Models\Pegawai;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = false;

    protected $fillable = [
        'pembeli_id',
        'cs_id',
        'tanggal_pelunasan',
        'point_digunakan',
        'point_diperoleh',
        'bukti_pembayaran',
        'metode_pengiriman',
        'tanggal_pesan',
        'total_harga',
        'status_transaksi',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id');
    }

    public function customerService()
    {
        return $this->belongsTo(Pegawai::class, 'cs_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'transaksi_id');
    }
}
