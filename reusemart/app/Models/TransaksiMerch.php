<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pembeli;
use App\Models\Merch;

class TransaksiMerch extends Model
{
    use HasFactory;

    protected $table = 'transaksi_merch';
    public $timestamps = false;
    public $incrementing = false; // karena tidak pakai id sebagai primary key

    protected $fillable = [
        'pembeli_id',
        'merch_id',
        'tanggal_penukaran',
        'status',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id');
    }

    public function merch()
    {
        return $this->belongsTo(Merch::class, 'merch_id');
    }
}
