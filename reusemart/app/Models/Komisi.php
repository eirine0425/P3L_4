<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Barang;

class Komisi extends Model
{
    use HasFactory;

    protected $table = 'komisi';
    public $timestamps = false;
    public $incrementing = false; // karena tidak ada kolom id

    protected $fillable = [
        'pegawai_id',
        'penitip_id',
        'barang_id',
        'persentase',
        'nominal_komisi',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'penitip_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
