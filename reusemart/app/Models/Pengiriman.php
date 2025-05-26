<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Alamat;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'pengiriman_id';
    public $timestamps = false;

    protected $fillable = [
        'pengirim_id',
        'transaksi_id',
        'alamat_id',
        'status_pengiriman',
        'tanggal_kirim',
        'nama_penerima',
        'tanggal_terima',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }
}
